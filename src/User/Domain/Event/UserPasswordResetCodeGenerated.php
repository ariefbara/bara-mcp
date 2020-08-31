<?php

namespace User\Domain\Event;

use Bara\Application\Listener\UserResetPasswordCodeGeneratedEventInterface;

class UserPasswordResetCodeGenerated implements UserResetPasswordCodeGeneratedEventInterface
{

    const EVENT_NAME = "UserPasswordResetCodeGenerated";

    /**
     *
     * @var string
     */
    protected $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

}
