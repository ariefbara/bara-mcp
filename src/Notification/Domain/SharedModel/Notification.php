<?php

namespace Notification\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification\ClientNotificationRecipient;
use Notification\Domain\SharedModel\Notification\PersonnelNotificationRecipient;
use Notification\Domain\SharedModel\Notification\UserNotificationRecipient;
use Resources\Uuid;

class Notification
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var ArrayCollection
     */
    protected $userNotificationRecipients;

    /**
     *
     * @var ArrayCollection
     */
    protected $clientNotificationRecipients;

    /**
     *
     * @var ArrayCollection
     */
    protected $personnelNotificationRecipients;

    public function __construct(string $id, string $message)
    {
        $this->id = $id;
        $this->message = $message;
        $this->userNotificationRecipients = new ArrayCollection();
        $this->clientNotificationRecipients = new ArrayCollection();
        $this->personnelNotificationRecipients = new ArrayCollection();
    }

    public function addUserRecipient(User $user): void
    {
        $id = Uuid::generateUuid4();
        $userNotificationRecipient = new UserNotificationRecipient($this, $id, $user);
        $this->userNotificationRecipients->add($userNotificationRecipient);
    }

    public function addClientRecipient(Client $client): void
    {
        $id = Uuid::generateUuid4();
        $clientNotificationRecipient = new ClientNotificationRecipient($this, $id, $client);
        $this->clientNotificationRecipients->add($clientNotificationRecipient);
    }

    public function addPersonnelRecipient(Personnel $personnel): void
    {
        $id = Uuid::generateUuid4();
        $personnelNotificationRecipient = new PersonnelNotificationRecipient($this, $id, $personnel);
        $this->personnelNotificationRecipients->add($personnelNotificationRecipient);
    }
    
    public function addManagerRecipient(Manager $manager): void
    {
        
    }

}
