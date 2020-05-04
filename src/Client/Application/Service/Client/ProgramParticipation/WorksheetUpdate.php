<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\Worksheet;
use Shared\Domain\Model\FormRecordData;

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
            ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId,
            string $name, FormRecordData $formRecordData): void
    {
        $worksheet = $this->worksheetRepository->ofId($programParticipationCompositionId, $worksheetId);
        $worksheet->update($name, $formRecordData);
        $this->worksheetRepository->update();
    }

}
