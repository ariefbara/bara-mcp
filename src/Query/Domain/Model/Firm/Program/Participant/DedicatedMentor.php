<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Resources\Exception\RegularException;

class DedicatedMentor
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
     * @var Consultant
     */
    protected $consultant;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    public function getModifiedTimeString(): string
    {
        return $this->modifiedTime->format('Y-m-d H:i:s');
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    protected function __construct()
    {
        
    }

    public function getParticipantName(): string
    {
        return $this->participant->getName();
    }

    public function getMentorName(): string
    {
        return $this->consultant->getPersonnelName();
    }

    public function getMentorPlusTeamName(): string
    {
        return (empty($teamName = $this->participant->getTeamName())) ?
                $this->consultant->getPersonnelName() :
                "{$this->consultant->getPersonnelName()} (of team: {$teamName})";
    }

    public function getListOfClientPlusTeamName(): array
    {
        return $this->participant->getListOfClientPlusTeamName();
    }

    public function correspondWithClient(Client $client): bool
    {
        return $this->participant->correspondWithClient($client);
    }

    public function correspondWithParticipant(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

    public function getTeamName(): ?string
    {
        return $this->participant->getTeamName();
    }

    public function getListOfIndividualParticipantNameOrMemberNameOfTeamParticipantWithinInspection(
            InspectedClientList $inspectedClientList): iterable
    {
        return $this->participant->getListOfIndividualParticipantNameOrMemberNameOfTeamParticipantWithinInspection(
                        $inspectedClientList);
    }

    public function executeQueryTaskOnDedicatedMentee(
            QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor $task, $payload): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('only active dedicated mentor can make this request');
        }
        $task->execute($this->participant->getId(), $payload);
    }

}
