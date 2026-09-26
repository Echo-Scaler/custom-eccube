<?php

/*
 * User History Event Listener
 * Listens to domain events such as product detail views, order purchases, registrations, and custom user actions.
 */

namespace Customize\EventListener;

use Customize\Event\UserActionEvent;
use Customize\Service\UserHistoryLogger;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Event\EventArgs;
use Eccube\Request\Context;
use Symfony\Component\HttpFoundation\RequestStack;

class UserHistoryListener
{
    private UserHistoryLogger $logger;
    private RequestStack $requestStack;
    private Context $requestContext;

    public function __construct(
        UserHistoryLogger $logger,
        RequestStack $requestStack,
        Context $requestContext
    ) {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
    }

    /**
     * Triggered when a customer or guest views a product detail page.
     * Event: front.product.detail.initialize
     */
    public function onProductDetail(EventArgs $event): void
    {
        /** @var Product|null $product */
        $product = $event->getArgument('Product');
        if (!$product) {
            return;
        }

        $request = $this->requestStack->getMainRequest();
        $userInfo = $this->resolveUserInfo();

        $details = [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'product_code' => $product->hasProductClass() ? $product->getCodeMin() : null,
            'price_min' => $product->hasProductClass() ? $product->getPrice02Min() : null,
        ];

        $this->logger->log('PRODUCT_VIEW', [
            'user_type' => $userInfo['user_type'],
            'user_id' => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'user_email' => $userInfo['user_email'],
            'ip' => $request ? $request->getClientIp() : '127.0.0.1',
            'method' => $request ? $request->getMethod() : 'GET',
            'route' => $request ? (string) $request->attributes->get('_route', 'product_detail') : 'product_detail',
            'url' => $request ? $request->getRequestUri() : '',
            'status_code' => 200,
            'referer' => $request ? (string) $request->headers->get('referer', '') : '',
            'user_agent' => $request ? (string) $request->headers->get('user-agent', '') : '',
            'details' => $details,
        ]);
    }

    /**
     * Triggered when a customer or guest adds a product to the cart.
     * Event: front.product.cart.add.complete
     */
    public function onCartAdd(EventArgs $event): void
    {
        /** @var Product|null $product */
        $product = $event->getArgument('Product');
        $form = $event->getArgument('form');

        $quantity = 1;
        if ($form && isset($form['quantity'])) {
            $quantity = (int) $form['quantity']->getData();
        }

        $request = $this->requestStack->getMainRequest();
        $userInfo = $this->resolveUserInfo();

        $details = [
            'product_id' => $product ? $product->getId() : null,
            'product_name' => $product ? $product->getName() : 'Product',
            'quantity' => $quantity,
        ];

        $this->logger->log('CART_ADD', [
            'user_type' => $userInfo['user_type'],
            'user_id' => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'user_email' => $userInfo['user_email'],
            'ip' => $request ? $request->getClientIp() : '127.0.0.1',
            'method' => 'POST',
            'route' => $request ? (string) $request->attributes->get('_route', 'product_add_cart') : 'product_add_cart',
            'url' => $request ? $request->getRequestUri() : '',
            'status_code' => 200,
            'referer' => $request ? (string) $request->headers->get('referer', '') : '',
            'user_agent' => $request ? (string) $request->headers->get('user-agent', '') : '',
            'details' => $details,
        ]);
    }

    /**
     * Triggered when a customer completes checkout / purchase.
     * Event: front.shopping.complete.initialize
     */
    public function onShoppingComplete(EventArgs $event): void
    {
        /** @var Order|null $order */
        $order = $event->getArgument('Order');
        if (!$order) {
            return;
        }

        $request = $this->requestStack->getMainRequest();
        $userInfo = $this->resolveUserInfo();

        $details = [
            'order_id' => $order->getId(),
            'order_no' => $order->getOrderNo(),
            'total' => $order->getTotal(),
            'payment_method' => $order->getPayment() ? $order->getPayment()->getMethod() : null,
            'item_count' => count($order->getOrderItems()),
        ];

        $this->logger->log('PURCHASE_COMPLETE', [
            'user_type' => $userInfo['user_type'],
            'user_id' => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'user_email' => $userInfo['user_email'],
            'ip' => $request ? $request->getClientIp() : '127.0.0.1',
            'method' => $request ? $request->getMethod() : 'POST',
            'route' => $request ? (string) $request->attributes->get('_route', 'shopping_complete') : 'shopping_complete',
            'url' => $request ? $request->getRequestUri() : '',
            'status_code' => 200,
            'referer' => $request ? (string) $request->headers->get('referer', '') : '',
            'user_agent' => $request ? (string) $request->headers->get('user-agent', '') : '',
            'details' => $details,
        ]);
    }

    /**
     * Triggered when a new customer registers on the site.
     * Event: front.entry.index.complete
     */
    public function onCustomerRegister(EventArgs $event): void
    {
        /** @var Customer|null $customer */
        $customer = $event->getArgument('Customer');

        $request = $this->requestStack->getMainRequest();

        $details = [
            'customer_id' => $customer ? $customer->getId() : null,
            'customer_email' => $customer ? $customer->getEmail() : null,
        ];

        $this->logger->log('CUSTOMER_REGISTER', [
            'user_type' => 'customer',
            'user_id' => $customer ? $customer->getId() : null,
            'user_name' => $customer ? trim($customer->getName01() . ' ' . $customer->getName02()) : 'New Customer',
            'user_email' => $customer ? $customer->getEmail() : null,
            'ip' => $request ? $request->getClientIp() : '127.0.0.1',
            'method' => $request ? $request->getMethod() : 'POST',
            'route' => $request ? (string) $request->attributes->get('_route', 'entry') : 'entry',
            'url' => $request ? $request->getRequestUri() : '',
            'status_code' => 200,
            'referer' => $request ? (string) $request->headers->get('referer', '') : '',
            'user_agent' => $request ? (string) $request->headers->get('user-agent', '') : '',
            'details' => $details,
        ]);
    }

    /**
     * Triggered on custom UserActionEvent.
     */
    public function onUserAction(UserActionEvent $event): void
    {
        $request = $this->requestStack->getMainRequest();
        $userInfo = $this->resolveUserInfo();

        $this->logger->log($event->getAction(), [
            'user_type' => $userInfo['user_type'],
            'user_id' => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'user_email' => $userInfo['user_email'],
            'ip' => $request ? $request->getClientIp() : '127.0.0.1',
            'method' => $request ? $request->getMethod() : 'GET',
            'route' => $request ? (string) $request->attributes->get('_route', '') : '',
            'url' => $request ? $request->getRequestUri() : '',
            'status_code' => 200,
            'referer' => $request ? (string) $request->headers->get('referer', '') : '',
            'user_agent' => $request ? (string) $request->headers->get('user-agent', '') : '',
            'details' => $event->getDetails(),
        ]);
    }

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

        return [
            'user_type' => 'guest',
            'user_id' => null,
            'user_name' => 'Guest',
            'user_email' => null,
        ];
    }
}
