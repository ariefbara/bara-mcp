<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\AssetBelongsToParticipant;
use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ParticipantProfile implements AssetBelongsToParticipant
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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

    protected function assertUnremoved(): void
    {
        if ($this->removed) {
            $errorDetail = "forbidden: unable not process non existing profile";
            throw RegularException::forbidden($errorDetail);
        }
    }
    function __construct(
            Participant $participant, string $id, ProgramsProfileForm $programsProfileForm,
            FormRecordData $formRecordData)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->programsProfileForm = $programsProfileForm;
        $this->formRecord = $this->programsProfileForm->createFormRecord($id, $formRecordData);
        $this->removed = false;
    }

    public function belongsToParticipant(Participant $participant): bool
    {
        return $this->participant === $participant;
    }
    
    public function anActiveProfileCorrespondWithProgramsProfileForm(ProgramsProfileForm $programsProfileForm): bool
    {
        return !$this->removed && $this->programsProfileForm === $programsProfileForm;
    }
    
    public function update(FormRecordData $formRecordData): void
    {
        $this->assertUnremoved();
        $this->formRecord->update($formRecordData);
    }
    
    public function remove(): void
    {
        $this->assertUnremoved();
        $this->removed = true;
    }

}
