<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\{
    Model\Firm\Personnel,
    Model\Firm\Program,
    Model\Firm\Program\Participant\Worksheet,
    Service\Firm\Program\Participant\WorksheetFinder,
    Service\Firm\Program\ParticipantFinder
};
use Resources\Exception\RegularException;

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
    protected $active;

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

    function isActive(): bool
    {
        return $this->active;
    }

    protected function __construct()
    {
        
    }

    protected function assertActvie(): void
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function viewParticipant(ParticipantFinder $participantFinder, $participantId): Participant
    {
        $this->assertActvie();
        return $participantFinder->findParticipantInProgram($this->program, $participantId);
    }

    public function viewAllParticipant(ParticipantFinder $participantFinder, int $page, int $pageSize,
            ?bool $activeStatus)
    {
        $this->assertActvie();
        return $participantFinder->findAllParticipantInProgram($this->program, $page, $pageSize, $activeStatus);
    }

    public function viewWorksheet(WorksheetFinder $worksheetFinder, string $participantId, string $worksheetId): Worksheet
    {
        $this->assertActvie();
        return $worksheetFinder->findWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $worksheetId);
    }

    public function viewAllWorksheets(WorksheetFinder $worksheetFinder, string $participantId, int $page, int $pageSize)
    {
        $this->assertActvie();
        return $worksheetFinder->findAllWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $page, $pageSize);
    }

    public function viewAllRootWorksheets(
            WorksheetFinder $worksheetFinder, string $participantId, int $page, int $pageSize)
    {
        $this->assertActvie();
        return $worksheetFinder->findAllRootWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $page, $pageSize);
    }

    public function viewAllBrancesOfWorksheets(
            WorksheetFinder $worksheetFinder, string $participantId, string $worksheetId, int $page, int $pageSize)
    {
        $this->assertActvie();
        return $worksheetFinder->findAllBranchOfWorksheetBelongsToParticipantInProgram(
                        $this->program, $participantId, $worksheetId, $page, $pageSize);
    }

}
