<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\ContainNotification;
use Notification\Domain\SharedModel\ContainNotificationforCoordinator;
use Notification\Domain\SharedModel\Notification;

class ConsultationSessionNotification implements ContainNotification, ContainNotificationforCoordinator
{

    /**
     *
     * @var ConsultationSession
     */
    protected $consultationSession;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Notification
     */
    protected $notification;

    public function __construct(ConsultationSession $consultationSession, string $id, string $message)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->notification = new Notification($id, $message);
    }

    public function addClientRecipient(Client $client): void
    {
        $this->notification->addClientRecipient($client);
    }

    public function addPersonnelRecipient(Personnel $personnel): void
    {
        $this->notification->addPersonnelRecipient($personnel);
    }

    public function addUserRecipient(User $user): void
    {
        $this->notification->addUserRecipient($user);
    }

    public function addCoordinatorAsRecipient(Coordinator $coordinator): void
    {
        $this->notification->addCoordinatorRecipient($coordinator);
    }

}
