<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Application\Service\Firm\Client\ProgramParticipationRepository;
use Query\Domain\Service\DataFinder;

class ViewSummary
{

    /**
     * 
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    /**
     * 
     * @var DataFinder
     */
    protected $dataFinder;

    public function __construct(ProgramParticipationRepository $programParticipationRepository, DataFinder $dataFinder)
    {
        $this->programParticipationRepository = $programParticipationRepository;
        $this->dataFinder = $dataFinder;
    }

    public function execute(string $firmId, string $clientId, string $programParticipationId): array
    {
        return $this->programParticipationRepository->ofId($firmId, $clientId, $programParticipationId)
                ->viewSummary($this->dataFinder);
    }

}
