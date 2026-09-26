<?php

/*
 * User History Action Event
 * Allows manual dispatching of custom user activity logs anywhere in the application.
 */

namespace Customize\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserActionEvent extends Event
{
    private string $action;
    private array $details;

    public function __construct(string $action, array $details = [])
    {
        $this->action = $action;
        $this->details = $details;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
