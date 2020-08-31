<?php

namespace Participant\Application\Service\Participant;

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
        $this->worksheetRepository->aWorksheetOfClientParticipant($firmId, $clientId, $programId, $worksheetId)
                ->remove();
        $this->worksheetRepository->update();
    }

}
