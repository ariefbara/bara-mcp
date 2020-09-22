<?php

namespace Notification\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\ {
    Firm\Client,
    User
};

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
    protected $userNotifations;
    /**
     *
     * @var ArrayCollection
     */
    protected $clientNotifations;
    
    public function addUserRecipient(User $user): void
    {
        
    }
    
    public function addClientRecipient(Client $client): void
    {
        
    }
}
