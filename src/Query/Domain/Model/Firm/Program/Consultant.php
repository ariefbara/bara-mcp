<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\ {
    Model\Firm\Personnel,
    Model\Firm\Program,
    Service\Firm\Program\ParticipantFinder
};

class Consultant
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
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }
    
    protected function assertActvie(): void
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw \Resources\Exception\RegularException::forbidden($errorDetail);
        }
    }
    
    public function viewParticipant(ParticipantFinder $participantFinder, $participantId): Participant
    {
        $this->assertActvie();
        return $participantFinder->findParticipantInProgram($this->program, $participantId);
    }
    public function viewAllParticipant(ParticipantFinder $participantFinder, int $page, int $pageSize, ?bool $activeStatus)
    {
        $this->assertActvie();
        return $participantFinder->findAllParticipantInProgram($this->program, $page, $pageSize, $activeStatus);
    }

}
