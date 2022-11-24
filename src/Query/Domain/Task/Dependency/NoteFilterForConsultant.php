<?php

namespace Query\Domain\Task\Dependency;

class NoteFilterForConsultant
{

    const OWN_NOTE = 'OWN';
    const DEDICATED_MENTEE_NOTES = 'MENTEE';
    const BOTH = 'BOTH';

    /**
     * 
     * @var NoteFilter
     */
    protected $noteFilter;

    /**
     * 
     * @var string|null
     */
    protected $programId;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    /**
     * 
     * @var string|null
     */
    protected $noteOwnership;

    public function setProgramId(?string $programId)
    {
        $this->programId = $programId;
        return $this;
    }

    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }

    public function setNoteOwnership(?string $noteOwnership)
    {
        $this->noteOwnership = $noteOwnership;
        return $this;
    }

    public function __construct(NoteFilter $noteFilter)
    {
        $this->noteFilter = $noteFilter;
    }
    
    protected function getProgramIdOptionalStatement(&$parameters): ?string
    {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
    }
    
    protected function getParticipantIdOptionalStatement(&$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND Participant.id = :participantId
_STATEMENT;
    }
    
    protected function getNoteOwnershipOptionalStatement(&$parameters): ?string
    {
        switch ($this->noteOwnership) {
            case self::OWN_NOTE :
                return <<<_STATEMENT
    AND ConsultantNote.Consultant_id = Consultant.id
_STATEMENT;
            case self::DEDICATED_MENTEE_NOTES :
                return <<<_STATEMENT
    AND DedicatedMentor.id IS NOT NULL
_STATEMENT;
            case self::BOTH :
                return <<<_STATEMENT
    AND (DedicatedMentor.id IS NOT NULL OR ConsultantNote.Consultant_id = Consultant.id)
_STATEMENT;
                break;
            default:
                return null;
        }
    }
    
    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->noteFilter->getOptionalConditionStatement($parameters)
                . $this->getProgramIdOptionalStatement($parameters)
                . $this->getParticipantIdOptionalStatement($parameters)
                . $this->getNoteOwnershipOptionalStatement($parameters);
    }
    
    public function getOrderStatement(): ?string
    {
        return $this->noteFilter->getOrderStatement();
    }
    
    public function getLimitStatement(): ?string
    {
        return $this->noteFilter->getLimitStatement();
    }

}
