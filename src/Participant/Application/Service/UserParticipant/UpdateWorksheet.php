<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\Participant\WorksheetRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class UpdateWorksheet
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

    public function execute(
            string $userId, string $userParticipantId, string $worksheetId, string $name, FormRecordData $formRecordData): void
    {
        $this->worksheetRepository->aWorksheetBelongsToUserParticipant($userId, $userParticipantId, $worksheetId)
                ->update($name, $formRecordData);
        $this->worksheetRepository->update();
    }

}
