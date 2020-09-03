<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\Participant\WorksheetRepository;

class RemoveWorksheet
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

    public function execute(string $userId, string $userParticipantId, string $worksheetId): void
    {
        $this->worksheetRepository->aWorksheetBelongsToUserParticipant($userId, $userParticipantId, $worksheetId)
                ->remove();
        $this->worksheetRepository->update();
    }
}
