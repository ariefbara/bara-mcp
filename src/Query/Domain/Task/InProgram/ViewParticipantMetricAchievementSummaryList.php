<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ViewParticipantMetricAchievementSummaryList implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->participantRepository
                ->participantListWithMetricSummaryInProgram($programId, $payload->getFilter());
    }

}
