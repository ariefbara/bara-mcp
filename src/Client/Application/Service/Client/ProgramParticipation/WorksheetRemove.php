<?php

namespace Client\Application\Service\Client\ProgramParticipation;

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
    
    public function execute(ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): void
    {
        $this->worksheetRepository->ofId($programParticipationCompositionId, $worksheetId)->remove();
        $this->worksheetRepository->update();
    }

}
