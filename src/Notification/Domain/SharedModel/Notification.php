<?php

namespace Notification\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification\ClientNotificationRecipient;
use Notification\Domain\SharedModel\Notification\CoordinatorNotificationRecipient;
use Notification\Domain\SharedModel\Notification\ManagerNotificationRecipient;
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

    /**
     *
     * @var ArrayCollection
     */
    protected $managerNotificationRecipients;

    /**
     *
     * @var ArrayCollection
     */
    protected $coordinatorNotificationRecipients;

    public function __construct(string $id, string $message)
    {
        $this->id = $id;
        $this->message = $message;
        $this->userNotificationRecipients = new ArrayCollection();
        $this->clientNotificationRecipients = new ArrayCollection();
        $this->personnelNotificationRecipients = new ArrayCollection();
        $this->managerNotificationRecipients = new ArrayCollection();
        $this->coordinatorNotificationRecipients = new ArrayCollection();
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
        $id = Uuid::generateUuid4();
        $managerNotificationRecipient = new ManagerNotificationRecipient($this, $id, $manager);
        $this->managerNotificationRecipients->add($managerNotificationRecipient);
    }
    
    public function addCoordinatorRecipient(Coordinator $coordinator): void
    {
        $id = Uuid::generateUuid4();
        $coordinatorNotificationRecipient = new CoordinatorNotificationRecipient($this, $id, $coordinator);
        $this->coordinatorNotificationRecipients->add($coordinatorNotificationRecipient);
    }

}
