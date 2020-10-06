<?php

namespace Query\Domain\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\{
    Program,
    Program\Participant,
    Program\Participant\Worksheet
};

class WorksheetFinder
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    public function __construct(WorksheetRepository $worksheetRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
    }

    public function findWorksheetBelongsToParticipant(Participant $participant, string $worksheetId): Worksheet
    {
        $firmId = $participant->getProgram()->getFirm()->getId();
        $programId = $participant->getProgram()->getId();
        $participantId = $participant->getId();
        return $this->worksheetRepository->ofId($firmId, $programId, $participantId, $worksheetId);
    }

    public function findAllWorksheetsBelongsToParticipant(Participant $participant, int $page, int $pageSize)
    {
        $firmId = $participant->getProgram()->getFirm()->getId();
        $programId = $participant->getProgram()->getId();
        $participantId = $participant->getId();
        return $this->worksheetRepository->all($firmId, $programId, $participantId, $page, $pageSize);
    }

    public function findAllRootWorksheetBelongsToParticipant(Participant $participant, int $page, int $pageSize)
    {
        $firmId = $participant->getProgram()->getFirm()->getId();
        $programId = $participant->getProgram()->getId();
        $participantId = $participant->getId();
        return $this->worksheetRepository->allRootWorksheets($firmId, $programId, $participantId, $page, $pageSize);
    }

    public function findAllBranchesOfWorksheetBelongsToParticipant(
            Participant $participant, string $worksheetId, int $page, int $pageSize)
    {
        $firmId = $participant->getProgram()->getFirm()->getId();
        $programId = $participant->getProgram()->getId();
        $participantId = $participant->getId();
        return $this->worksheetRepository->allBranchesOfParentWorksheet(
                        $firmId, $programId, $participantId, $worksheetId, $page, $pageSize);
    }

    public function findWorksheetBelongsToParticipantInProgram(
            Program $program, string $participantId, string $worksheetId): Worksheet
    {
        $firmId = $program->getFirm()->getId();
        $programId = $program->getId();
        return $this->worksheetRepository->ofId($firmId, $programId, $participantId, $worksheetId);
    }

    public function findAllWorksheetBelongsToParticipantInProgram(
            Program $program, string $participantId, int $page, int $pageSize)
    {
        $firmId = $program->getFirm()->getId();
        $programId = $program->getId();
        return $this->worksheetRepository->all($firmId, $programId, $participantId, $page, $pageSize);
    }

    public function findAllRootWorksheetBelongsToParticipantInProgram(
            Program $program, string $participantId, int $page, int $pageSize)
    {
        $firmId = $program->getFirm()->getId();
        $programId = $program->getId();
        return $this->worksheetRepository->allRootWorksheets($firmId, $programId, $participantId, $page, $pageSize);
    }

    public function findAllBranchOfWorksheetBelongsToParticipantInProgram(
            Program $program, string $participantId, string $worksheetId, int $page, int $pageSize)
    {
        $firmId = $program->getFirm()->getId();
        $programId = $program->getId();
        return $this->worksheetRepository->allBranchesOfParentWorksheet(
                        $firmId, $programId, $participantId, $worksheetId, $page, $pageSize);
    }

}
