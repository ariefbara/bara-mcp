<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Task\Dependency\Firm\ProgramRepository;
use Query\Domain\Task\PaginationPayload;

class ViewAllAvailableProgramsTask implements ITaskExecutableByClient
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var PaginationPayload
     */
    protected $payload;
    public $result;

    public function __construct(ProgramRepository $programRepository, PaginationPayload $payload)
    {
        $this->programRepository = $programRepository;
        $this->payload = $payload;
    }

    public function execute(string $clientId): void
    {
        $this->result = $this->programRepository->allAvailableProgramsForClient($clientId, $this->payload);
    }

}
