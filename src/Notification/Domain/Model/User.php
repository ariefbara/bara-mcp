<?php

namespace Notification\Domain\Model;

use Resources\Domain\ValueObject\PersonName;

class User
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var PersonName
     */
    protected $personName;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $activationCode = null;

    /**
     *
     * @var string
     */
    protected $resetPasswordCode = null;

    /**
     *
     * @var bool
     */
    protected $activated = false;
    
    protected function __construct()
    {
        ;
    }
    
    public function getName(): string
    {
        return $this->personName->getFullName();
    }
}
