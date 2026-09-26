<?php

/*
 * User History Event Subscriber
 * Automatically logs page visits, user logins, and logouts across the application.
 */

namespace Customize\EventListener;

use Customize\Service\UserHistoryLogger;
use Eccube\Entity\Customer;
use Eccube\Entity\Member;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserHistorySubscriber implements EventSubscriberInterface
{
    private UserHistoryLogger $logger;
    private Context $requestContext;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        UserHistoryLogger $logger,
        Context $requestContext,
        TokenStorageInterface $tokenStorage
    ) {
        $this->logger = $logger;
        $this->requestContext = $requestContext;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['onKernelResponse', -10]],
            SecurityEvents::INTERACTIVE_LOGIN => [['onInteractiveLogin', 0]],
            LogoutEvent::class => [['onLogout', 0]],
        ];
    }

    /**
     * Intercept every HTTP response to track page views and user navigation history.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $path = $request->getPathInfo();

        // Skip static asset requests, health checks, and debug profiler routes
        if (preg_match('/\.(css|js|map|png|jpe?g|gif|svg|ico|woff2?|ttf|eot|webp|avif)$/i', $path)) {
            return;
        }

        if (str_starts_with($path, '/_profiler') ||
            str_starts_with($path, '/_wdt') ||
            str_starts_with($path, '/_error') ||
            str_starts_with($path, '/assets/')
        ) {
            return;
        }

        // Exclude all admin panel requests completely (User History is customer/guest only)
        if ($this->requestContext->isAdmin()) {
            return;
        }

        // Avoid log pollution from log download or duplicate domain event routes
        $route = (string) $request->attributes->get('_route', '');
        if ($route === 'admin_setting_system_user_history_download') {
            return;
        }

        // Avoid duplicate logging for routes handled with rich metadata by domain listeners
        if (in_array($route, ['product_detail', 'product_add_cart', 'shopping_complete'], true)) {
            return;
        }

        $userInfo = $this->resolveUserInfo();

        // Do not log if user is an administrator
        if ($userInfo['user_type'] === 'admin') {
            return;
        }

        $this->logger->log('PAGE_VIEW', [
            'user_type' => $userInfo['user_type'],
            'user_id' => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'user_email' => $userInfo['user_email'],
            'ip' => $request->getClientIp() ?: '127.0.0.1',
            'method' => $request->getMethod(),
            'route' => $route,
            'url' => $request->getRequestUri(),
            'status_code' => $response->getStatusCode(),
            'referer' => (string) $request->headers->get('referer', ''),
            'user_agent' => (string) $request->headers->get('user-agent', ''),
            'details' => [
                'query' => $request->query->all(),
            ],
        ]);
    }

    /**
     * Log user authentication login events (front customer logins only).
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $request = $event->getRequest();
        $token = $event->getAuthenticationToken();
        $user = $token ? $token->getUser() : null;

        // Skip administrator logins; only track storefront customers
        if (!($user instanceof Customer)) {
            return;
        }

        $userType = 'customer';
        $userId = $user->getId();
        $userName = trim($user->getName01() . ' ' . $user->getName02()) ?: ($user->getEmail() ?: 'Customer #' . $user->getId());
        $userEmail = $user->getEmail();

        $this->logger->log('USER_LOGIN', [
            'user_type' => $userType,
            'user_id' => $userId,
            'user_name' => $userName,
            'user_email' => $userEmail,
            'ip' => $request->getClientIp() ?: '127.0.0.1',
            'method' => $request->getMethod(),
            'route' => (string) $request->attributes->get('_route', ''),
            'url' => $request->getRequestUri(),
            'status_code' => 200,
            'referer' => (string) $request->headers->get('referer', ''),
            'user_agent' => (string) $request->headers->get('user-agent', ''),
            'details' => [
                'auth_type' => 'front_customer_login',
                'login_time' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Log user logout events (front customer logouts only).
     */
    public function onLogout(LogoutEvent $event): void
    {
        $request = $event->getRequest();
        $token = $event->getToken();
        $user = $token ? $token->getUser() : null;

        // Skip administrator logouts; only track storefront customers
        if (!($user instanceof Customer)) {
            return;
        }

        $userType = 'customer';
        $userId = $user->getId();
        $userName = trim($user->getName01() . ' ' . $user->getName02()) ?: ($user->getEmail() ?: 'Customer #' . $user->getId());
        $userEmail = $user->getEmail();

        $this->logger->log('USER_LOGOUT', [
            'user_type' => $userType,
            'user_id' => $userId,
            'user_name' => $userName,
            'user_email' => $userEmail,
            'ip' => $request ? $request->getClientIp() : '127.0.0.1',
            'method' => $request ? $request->getMethod() : 'GET',
            'route' => $request ? (string) $request->attributes->get('_route', '') : '',
            'url' => $request ? $request->getRequestUri() : '',
            'status_code' => 200,
            'referer' => $request ? (string) $request->headers->get('referer', '') : '',
            'user_agent' => $request ? (string) $request->headers->get('user-agent', '') : '',
            'details' => [
                'action' => 'session_terminated',
            ],
        ]);
    }

    /**
     * Helper to resolve current authenticated user details.
     */
    private function resolveUserInfo(): array
    {
        $user = $this->requestContext->getCurrentUser();

        if ($user instanceof Customer) {
            return [
                'user_type' => 'customer',
                'user_id' => $user->getId(),
                'user_name' => trim($user->getName01() . ' ' . $user->getName02()) ?: ($user->getEmail() ?: 'Customer #' . $user->getId()),
                'user_email' => $user->getEmail(),
            ];
        }

        if ($user instanceof Member) {
            return [
                'user_type' => 'admin',
                'user_id' => $user->getId(),
                'user_name' => $user->getName() ?: $user->getUsername(),
                'user_email' => $user->getUsername(),
            ];
        }

        return [
            'user_type' => 'guest',
            'user_id' => null,
            'user_name' => 'Guest',
            'user_email' => null,
        ];
    }
}
