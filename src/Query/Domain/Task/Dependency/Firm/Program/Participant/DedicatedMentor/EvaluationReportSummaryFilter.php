<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor;

class EvaluationReportSummaryFilter
{

    /**
     * 
     * @var array
     */
    protected $evaluationPlanIdList;

    /**
     * 
     * @var array
     */
    protected $participantIdList;

    /**
     * 
     * @var array
     */
    protected $mentorIdList;

    /**
     * 
     * @var array
     */
    protected $clientIdList;
    
    /**
     * 
     * @var array
     */
    protected $personnelIdList;

    public function getEvaluationPlanIdList(): array
    {
        return $this->evaluationPlanIdList;
    }

    public function getParticipantIdList(): array
    {
        return $this->participantIdList;
    }

    public function getMentorIdList(): array
    {
        return $this->mentorIdList;
    }

    public function getClientIdList(): array
    {
        return $this->clientIdList;
    }
    
    public function getPersonnelIdList(): array
    {
        return $this->personnelIdList;
    }

    
    public function __construct()
    {
        $this->evaluationPlanIdList = [];
        $this->participantIdList = [];
        $this->mentorIdList = [];
        $this->clientIdList = [];
        $this->personnelIdList = [];
    }

    public function addEvaluationPlanId(string $evaluationPlanId): self
    {
        $this->evaluationPlanIdList[] = $evaluationPlanId;
        return $this;
    }

    public function addParticipantId(string $participantId): self
    {
        $this->participantIdList[] = $participantId;
        return $this;
    }

    public function addMentorId(string $mentorId): self
    {
        $this->mentorIdList[] = $mentorId;
        return $this;
    }

    public function addClientId(string $clientId): self
    {
        $this->clientIdList[] = $clientId;
        return $this;
    }
    
    public function addPersonnelId(string $personnelId): self
    {
        $this->personnelIdList[] = $personnelId;
        return $this;
    }

}
