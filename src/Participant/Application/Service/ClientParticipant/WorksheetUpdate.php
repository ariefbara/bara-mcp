<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\Participant\WorksheetRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class WorksheetUpdate
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
            string $firmId, string $clientId, string $programId, string $worksheetId, string $name,
            FormRecordData $formRecordData): void
    {
        $this->worksheetRepository->aWorksheetBelongsToClientParticipant($firmId, $clientId, $programId, $worksheetId)
                ->update($name, $formRecordData);
        $this->worksheetRepository->update();
    }

}
