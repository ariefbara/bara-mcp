<?php

namespace Query\Domain\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\ {
    Participant,
    Participant\Worksheet
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
        return $this->worksheetRepository->aWorksheetBelongToParticipant($participant->getId(), $worksheetId);
    }

    public function findAllWorksheetsBelongsToParticipant(Participant $participant, int $page, int $pageSize)
    {
        return $this->worksheetRepository->allWorksheetBelongToParticipant($participant->getId(), $page, $pageSize);
    }

    public function findAllRootWorksheetBelongsToParticipant(Participant $participant, int $page, int $pageSize)
    {
        return $this->worksheetRepository->allRootWorksheetsBelongToParticipant($participant->getId(), $page, $pageSize);
    }

    public function findAllBranchesOfWorksheetBelongsToParticipant(
            Participant $participant, string $worksheetId, int $page, int $pageSize)
    {
        return $this->worksheetRepository->allBranchesOfWorksheetBelongToParticipant(
                        $participant->getId(), $worksheetId, $page, $pageSize);
    }

}
