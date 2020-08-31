<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant;

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
     * @param string $firmId
     * @param string $clientId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programId, int $page, int $pageSize, ?string $missionId,
            ?string $parentWorksheetId)
    {
        return $this->worksheetRepository->allWorksheetsBelongsToClientParticipant(
                        $firmId, $clientId, $programId, $page, $pageSize, $missionId, $parentWorksheetId);
    }

    public function showById(string $firmId, string $clientId, string $programId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository
                        ->aWorksheetBelongsToClientParticipant($firmId, $clientId, $programId, $worksheetId);
    }

}
