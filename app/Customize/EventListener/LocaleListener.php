<?php

/*
 * Locale Session Listener
 * Reads the locale stored in the session and applies it to every request.
 */

namespace Customize\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'ja')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priority 20 runs before Symfony's own LocaleListener (priority 16)
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        // Apply locale from session (set by LocaleController)
        $locale = $request->getSession()->get('_locale', $this->defaultLocale);

        // Validate — only allow known locales
        $allowed = ['en', 'ja', 'my'];
        if (!in_array($locale, $allowed, true)) {
            $locale = $this->defaultLocale;
        }

        $request->setLocale($locale);
    }
}
