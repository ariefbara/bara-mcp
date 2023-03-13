<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class ProgramRegistration
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
     * @var RegistrationStatus
     */
    protected $status;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $registeredTime;

    /**
     *
     * @var string||null
     */
    protected $note;

    /**
     * 
     * @var ArrayCollection
     */
    protected $profiles;

    protected function __construct()
    {
    }

//    public function __construct(Program $program, string $id)
//    {
//        $this->program = $program;
//        $this->id = $id;
//        $this->concluded = false;
//        $this->registeredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
//        $this->note = null;
//    }

    public function cancel(): void
    {
        $this->status = $this->status->cancel();
    }

    public function isUnconcludedRegistrationToProgram(Program $program): bool
    {
        return !$this->status->isConcluded() && $this->program === $program;
    }

    public function submitProfile(ProgramsProfileForm $programsProfileForm, FormRecordData $formRecordData): void
    {
        $this->assertUnconcluded();
        if (!$programsProfileForm->programEquals($this->program)) {
            $errorDetail = "forbidden: unable to submit profile from other program's profile template";
            throw RegularException::forbidden($errorDetail);
        }
        
        $p = function (RegistrantProfile $registrantProfile) use ($programsProfileForm) {
            return $registrantProfile->anActiveProfileCorrespondWithProgramsProfileForm($programsProfileForm);
        };
        if (!empty($profile = $this->profiles->filter($p)->first())) {
            $profile->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $profile = new RegistrantProfile($this, $id, $programsProfileForm, $formRecordData);
            $this->profiles->add($profile);
        }
    }

    public function removeProfile(RegistrantProfile $registrantProfile): void
    {
        $this->assertUnconcluded();
        if (!$registrantProfile->belongsToRegistrant($this)) {
            $errorDetail = "forbidden: can only remove self profile";
            throw RegularException::forbidden($errorDetail);
        }
        $registrantProfile->remove();
    }
    
    protected function assertUnconcluded(): void
    {
        if ($this->status->isConcluded()) {
            $errorDetail = "forbidden: only unconcluded registrant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
