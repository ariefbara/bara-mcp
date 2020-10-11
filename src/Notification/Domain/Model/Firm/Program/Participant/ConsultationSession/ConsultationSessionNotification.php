<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\{
    Model\Firm\Client,
    Model\Firm\Personnel,
    Model\Firm\Program\Participant\ConsultationSession,
    Model\User,
    SharedModel\ContainNotification,
    SharedModel\Notification
};

class ConsultationSessionNotification implements ContainNotification
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

}
