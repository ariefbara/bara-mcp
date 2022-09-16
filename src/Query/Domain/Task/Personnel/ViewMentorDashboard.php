<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\PersonnelRepository;

class ViewMentorDashboard implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var ViewMentorDashboardPayload
     */
    protected $payload;

    public function __construct(PersonnelRepository $personnelRepository, ViewMentorDashboardPayload $payload)
    {
        $this->personnelRepository = $personnelRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): void
    {
        $this->payload->result = $this->personnelRepository->mentorDashboardSummary($personnelId);
    }

}
