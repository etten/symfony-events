<?php

namespace Etten\SymfonyEvents;

use Kdyby\Events\EventManager;
use Nette\DI;
use Nette\Utils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventsExtension extends DI\CompilerExtension
{

	const SUBSCRIBER_TAG = 'symfony.subscriber';

	/** @var array */
	public $defaults = [
		'subscribers' => [],
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		Utils\Validators::assertField($config, 'subscribers', 'array');
		foreach ($config['subscribers'] as $subscriber) {
			$builder->addDefinition(
				$this->prefix('subscriber.' . md5($subscriber))
			)
				->setClass($subscriber)
				->setAutowired(FALSE)
				->addTag(self::SUBSCRIBER_TAG);
		}
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$manager = $this->getEventManager();

		foreach (array_keys($builder->findByTag(self::SUBSCRIBER_TAG)) as $serviceName) {
			$def = $builder->getDefinition($serviceName);

			if (!in_array(EventSubscriberInterface::class, class_implements($def->getClass()))) {
				throw new \RuntimeException(sprintf(
					'Subscriber %s does not implement %s.',
					$serviceName,
					EventSubscriberInterface::class
				));
			}

			/** @var EventSubscriberInterface|string $class */
			$class = $def->getClass();
			$events = $class::getSubscribedEvents();

			foreach ($events as $name => $data) {
				$manager->addSetup('addEventListener', [
					$name,
					['@' . $serviceName, $data[0]],
					$data[1],
				]);
			}
		}
	}

	/**
	 * @return DI\ServiceDefinition
	 */
	private function getEventManager()
	{
		$builder = $this->getContainerBuilder();

		foreach ($builder->findByType(EventManager::class) as $manager) {
			return $manager;
		}

		throw new \RuntimeException('EventManager service was not found.');
	}

}
