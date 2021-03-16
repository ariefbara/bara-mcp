<?php

namespace Query\Application\Service\Firm\Client;

use Query\Application\Service\Firm\ClientRepository;
use Query\Domain\Service\DataFinder;

class ViewAllActiveProgramParticipationSummary
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var DataFinder
     */
    protected $dataFinder;

    public function __construct(ClientRepository $clientRepository, DataFinder $dataFinder)
    {
        $this->clientRepository = $clientRepository;
        $this->dataFinder = $dataFinder;
    }
    
    public function execute(string $firmId, string $clientId, int $page, int $pageSize): array
    {
        return $this->clientRepository->ofId($firmId, $clientId)
                ->viewAllActiveProgramParticipationSummary($this->dataFinder, $page, $pageSize);
    }

}
