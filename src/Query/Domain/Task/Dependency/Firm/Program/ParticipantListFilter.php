<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

class ParticipantListFilter
{

    /**
     * 
     * @var string|null
     */
    protected $programId;

    /**
     * 
     * @var string|null
     */
    protected $name;

    public function setProgramId(?string $programId)
    {
        $this->programId = $programId;
        return $this;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function __construct()
    {
        
    }
    
    protected function getProgramIdCriteria(&$parameters): ?string
    {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
        
    }
    protected function getNameCriteria(&$parameters): ?string
    {
        if (empty($this->name)) {
            return null;
        }
        $parameters['name'] = "%$this->name%";
        return <<<_STATEMENT
    AND (
        Client.firstName LIKE :name
        OR Client.lastName LIKE :name
        OR Team.name LIKE :name
        OR User.firstName LIKE :name 
        OR User.lastName LIKE :name
    )
_STATEMENT;
    }

    public function getCriteriaStatement(&$parameters): ?string
    {
        return $this->getProgramIdCriteria($parameters)
                . $this->getNameCriteria($parameters);
    }

}
