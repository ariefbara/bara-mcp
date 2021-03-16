<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Application\Service\User\ProgramParticipationRepository;
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
    
    public function execute(string $userId, string $programParticipationId): array
    {
        return $this->programParticipationRepository->ofId($userId, $programParticipationId)
                ->viewSummary($this->dataFinder);
    }

}
