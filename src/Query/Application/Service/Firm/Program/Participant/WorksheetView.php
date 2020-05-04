<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

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

    public function showById(ParticipantCompositionId $participantCompositionId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->ofId($participantCompositionId, $worksheetId);
    }

    /**
     * 
     * @param ParticipantCompositionId $participantCompositionId
     * @param string $page
     * @param string $pageSize
     * @return Worksheet[]
     */
    public function showAll(ParticipantCompositionId $participantCompositionId, string $page, string $pageSize)
    {
        return $this->worksheetRepository->all($participantCompositionId, $page, $pageSize);
    }

}
