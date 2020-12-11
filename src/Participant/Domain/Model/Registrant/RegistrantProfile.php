<?php

namespace Participant\Domain\Model\Registrant;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\ProgramRegistration;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class RegistrantProfile
{

    /**
     * 
     * @var ProgramRegistration
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

    function __construct(
            ProgramRegistration $registrant, string $id, ProgramsProfileForm $programsProfileForm,
            FormRecordData $formRecordData)
    {
        $this->registrant = $registrant;
        $this->id = $id;
        $this->programsProfileForm = $programsProfileForm;
        $this->formRecord = $this->programsProfileForm->createFormRecord($id, $formRecordData);
        $this->removed = false;
    }
    
    public function update(FormRecordData $formRecordData): void
    {
        $this->assertNotRemoved();
        $this->formRecord->update($formRecordData);
    }
    
    public function remove(): void
    {
        $this->assertNotRemoved();
        $this->removed = true;
    }
    
    public function anActiveProfileCorrespondWithProgramsProfileForm(ProgramsProfileForm $programsProfileForm): bool
    {
        return !$this->removed && $this->programsProfileForm === $programsProfileForm;
    }
    
    public function belongsToRegistrant(ProgramRegistration $registrant): bool
    {
        return $this->registrant === $registrant;
    }
    
    protected function assertNotRemoved(): void
    {
        if ($this->removed) {
            $errorDetail = "forbidden: unable not process non existing profile";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
