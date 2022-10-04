<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\PersonnelRepository;
use Query\Domain\Task\GenericQueryPayload;

class ViewCoordinatorDashboardSummary implements PersonnelTask
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param GenericQueryPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->personnelRepository->coordinatorDashboardSummary($personnelId);
    }

}
