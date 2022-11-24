<?php

namespace Query\Domain\Task\Dependency;

class NoteFilterForCoordinator
{

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
    
    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->noteFilter->getOptionalConditionStatement($parameters)
                . $this->getProgramIdOptionalStatement($parameters)
                . $this->getParticipantIdOptionalStatement($parameters);
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
