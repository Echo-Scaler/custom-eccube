<?php

/*
 * Locale Switcher Controller
 * Saves the selected locale in the session and redirects back to the referrer.
 */

namespace Customize\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController
{
    /**
     * @Route("/locale/{locale}", name="locale_switch", methods={"GET"}, requirements={"locale"="en|ja|my"})
     */
    public function switchLocale(Request $request, string $locale): RedirectResponse
    {
        // Persist the selected locale in the session
        $request->getSession()->set('_locale', $locale);

        // Redirect back to where the user came from
        $referer = $request->headers->get('referer');
        if ($referer) {
            // Strip any stale _locale query param from the referer URL
            $parsed = parse_url($referer);
            $query = [];
            if (!empty($parsed['query'])) {
                parse_str($parsed['query'], $query);
            }
            unset($query['_locale']);
            $qs = !empty($query) ? '?' . http_build_query($query) : '';
            $cleanUrl = ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? '') .
                        (!empty($parsed['port']) ? ':' . $parsed['port'] : '') .
                        ($parsed['path'] ?? '/') . $qs;
            return new RedirectResponse($cleanUrl);
        }

        return new RedirectResponse('/');
    }
}
