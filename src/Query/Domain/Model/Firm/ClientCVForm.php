<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;

class ClientCVForm
{

    /**
     * 
     * @var Firm
     */
    protected $firm;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ProfileForm
     */
    protected $profileForm;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProfileForm(): ProfileForm
    {
        return $this->profileForm;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function __construct()
    {
        
    }

}
