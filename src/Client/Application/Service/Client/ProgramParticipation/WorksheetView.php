<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\Worksheet;

class WorksheetView
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

    public function showById(ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->ofId($programParticipationCompositionId, $worksheetId);
    }

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAll(ProgramParticipationCompositionId $programParticipationCompositionId, int $page,
            int $pageSize)
    {
        return $this->worksheetRepository->all($programParticipationCompositionId, $page, $pageSize);
    }

}
