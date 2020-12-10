<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\ProfileForm;
use Query\Domain\Model\Firm\Program;

class ProgramsProfileForm
{

    /**
     * 
     * @var Program
     */
    protected $program;

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

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getProfileForm(): ProfileForm
    {
        return $this->profileForm;
    }

    function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

}
