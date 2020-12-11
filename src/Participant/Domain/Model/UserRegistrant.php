<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class UserRegistrant
{

    protected $userId;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ProgramRegistration
     */
    protected $registrant;

    protected function __construct()
    {
        
    }

    public function submitProfile(ProgramsProfileForm $programsProfileForm, FormRecordData $formRecordData): void
    {
        $this->registrant->submitProfile($programsProfileForm, $formRecordData);
    }

    public function removeProfile(RegistrantProfile $registrantProfile): void
    {
        $this->registrant->removeProfile($registrantProfile);
    }

}
