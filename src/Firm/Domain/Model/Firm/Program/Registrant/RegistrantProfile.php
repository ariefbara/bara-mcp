<?php

namespace Firm\Domain\Model\Firm\Program\Registrant;

use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use Firm\Domain\Model\Firm\Program\Registrant;
use SharedContext\Domain\Model\SharedEntity\FormRecord;

class RegistrantProfile
{

    /**
     * 
     * @var Registrant
     */
    protected $registrant;

    /**
     * 
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var ProgramsProfileForm
     */
    protected $programsProfileForm;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    /**
     * 
     * @var bool
     */
    protected $removed;

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }
    
    public function transferToParticipant(Participant $participant): void
    {
        $participant->addProfile($this->programsProfileForm, $this->formRecord);
    }

}
