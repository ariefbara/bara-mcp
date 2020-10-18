<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\{
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

    public function __construct(WorksheetRepository $worksheetRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
    }

    /**
     * 
     * @param string $userId
     * @param string $userParticipantId
     * @param int $page
     * @param int $pageSize
     * @param WorksheetFilter|null $worksheetFilter
     * @return Worksheet[]
     */
    public function showAll(
            string $userId, string $userParticipantId, int $page, int $pageSize, ?WorksheetFilter $worksheetFilter)
    {
        return $this->worksheetRepository->allWorksheetsInProgramParticipationBelongsToUser(
                        $userId, $userParticipantId, $page, $pageSize, $worksheetFilter);
    }

    public function showById(string $userId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetBelongsToUser($userId, $worksheetId);
    }

}
