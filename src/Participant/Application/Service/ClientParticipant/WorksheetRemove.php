<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\Participant\WorksheetRepository;

class WorksheetRemove
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

    public function execute(string $firmId, string $clientId, string $programId, string $worksheetId): void
    {
        $this->worksheetRepository->aWorksheetBelongsToClientParticipant($firmId, $clientId, $programId, $worksheetId)
                ->remove();
        $this->worksheetRepository->update();
    }

}
