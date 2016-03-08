# Etten\SymfonyEvents

This adds [EventDispatcher Component](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
support into your [Nette Framework](https://nette.org/) project.

It's an extension of [Kdyby\Events](https://github.com/Kdyby/Events).

## Installation

Best way is installation via [Composer](https://getcomposer.org/).

`$ composer require kdyby/events`
`$ composer require etten/symfony-events`

Then open your `app/config/config.neon` file and register following extensions:

```yaml

extensions:
	kdyby.events: Kdyby\Events\DI\EventsExtension
	symfony.events: Etten\SymfonyEvents\EventsExtension

```

It's all!

## Register `EventSubscriberInterface` implementor

You have two options.
Just open your config file, eg. `app/config/config.neon`.

### a. Tagged service

```yaml

service:
	monolog.symfony.console.handler:
	class: Symfony\Bridge\Monolog\Handler\ConsoleHandler
	tags: [symfony.subscriber] # this is a magic line

```

### b. Extension section

```yaml

symfony.events:
	subscribers:
		- Symfony\Bridge\Monolog\Handler\ConsoleHandler

```
