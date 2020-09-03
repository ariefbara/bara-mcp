<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

class ViewWorksheet
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

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet
     */
    public function showAll(string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        return $this->worksheetRepository->all($firmId, $programId, $participantId, $page, $pageSize);
    }

    public function showById(string $firmId, string $programId, string $participantId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->ofId($firmId, $programId, $participantId, $worksheetId);
    }

}
