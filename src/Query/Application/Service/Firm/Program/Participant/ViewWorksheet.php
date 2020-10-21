<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\ {
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

class ViewWorksheet
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
     * @param string $firmId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet
     */
    public function showAll(string $programId, string $participantId, int $page, int $pageSize, ?WorksheetFilter $worksheetFilter)
    {
        return $this->worksheetRepository->allWorksheetBelongsToParticipantInProgram(
                $programId, $participantId, $page, $pageSize, $worksheetFilter);
    }

    public function showById(string $programId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetInProgram($programId, $worksheetId);
    }

}
