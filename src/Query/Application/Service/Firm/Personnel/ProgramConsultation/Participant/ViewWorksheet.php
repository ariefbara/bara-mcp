<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultation\Participant;

use Query\{
    Application\Service\Firm\Personnel\ProgramConsultationRepository,
    Domain\Service\Firm\Program\Participant\WorksheetFinder
};

class ViewWorksheet
{

    /**
     *
     * @var ProgramConsultationRepository
     */
    protected $programConsultationRepository;

    /**
     *
     * @var WorksheetFinder
     */
    protected $worksheetFinder;

    public function __construct(ProgramConsultationRepository $programConsultationRepository,
            WorksheetFinder $worksheetFinder)
    {
        $this->programConsultationRepository = $programConsultationRepository;
        $this->worksheetFinder = $worksheetFinder;
    }

    public function showAll(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId, int $page,
            int $pageSize)
    {
        return $this->programConsultationRepository
                        ->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId)
                        ->viewAllWorksheets($this->worksheetFinder, $participantId, $page, $pageSize);
    }

    public function showAllRoot(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId, int $page,
            int $pageSize)
    {
        return $this->programConsultationRepository
                        ->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId)
                        ->viewAllRootWorksheets($this->worksheetFinder, $participantId, $page, $pageSize);
    }

    public function showAllBranchesOfAWorksheet(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId,
            string $worksheetId, int $page, int $pageSize)
    {
        return $this->programConsultationRepository
                        ->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId)
                        ->viewAllBrancesOfWorksheets(
                                $this->worksheetFinder, $participantId, $worksheetId, $page, $pageSize);
    }

    public function showById(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId,
            string $worksheetId
    )
    {
        return $this->programConsultationRepository
                        ->aProgramConsultationOfPersonnel($firmId, $personnelId, $programConsultationId)
                        ->viewWorksheet($this->worksheetFinder, $participantId, $worksheetId);
    }

}
