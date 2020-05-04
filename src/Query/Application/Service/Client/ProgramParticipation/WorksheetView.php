<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;

class WorksheetView
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    function __construct(WorksheetRepository $worksheetRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
    }

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAll(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        return $this->worksheetRepository
                        ->allWorksheetsOfParticipant($programParticipationCompositionId, $page, $pageSize);
    }

    public function showById(ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetOfParticipant($programParticipationCompositionId, $worksheetId);
    }

}
