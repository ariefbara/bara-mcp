<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

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
     * @param string|null $missionId
     * @param string|null $parentWorksheetId
     * @return Worksheet[]
     */
    public function showAll(string $userId, string $userParticipantId, int $page, int $pageSize, ?string $missionId,
            ?string $parentWorksheetId)
    {
        return $this->worksheetRepository->allWorksheetBelongsToUserParticipant($userId, $userParticipantId, $page,
                        $pageSize, $missionId, $parentWorksheetId);
    }

    public function showById(string $userId, string $userParticipantId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetBelongsToUserParticipant($userId, $userParticipantId, $worksheetId);
    }

}
