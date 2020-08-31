<?php

namespace Firm\Domain\Event\Firm\Program;

use Config\EventList;
use Firm\Application\Listener\Firm\Program\UserRegistrationAcceptedEventInterface;

class UserRegistrationAccepted implements UserRegistrationAcceptedEventInterface
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $userId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function __construct(string $firmId, string $programId, string $userId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return EventList::USER_REGISTRATION_ACCEPTED;
    }

}
