<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Personnel,
    Model\Firm\Program\Participant\ConsultationRequest,
    Model\User,
    SharedModel\ContainNotification,
    SharedModel\Notification
};

class ConsultationRequestNotification implements ContainNotification
{

    /**
     *
     * @var ConsultationRequest
     */
    protected $consultationRequest;

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
    
    public function __construct(ConsultationRequest $consultationRequest, string $id, string $message)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->notification = new Notification($id, $message);
    }

    public function addUserRecipient(User $user): void
    {
        $this->notification->addUserRecipient($user);
    }

    public function addClientRecipient(Client $client): void
    {
        $this->notification->addClientRecipient($client);
    }

    public function addPersonnelRecipient(Personnel $personnel): void
    {
        $this->notification->addPersonnelRecipient($personnel);
    }

}
