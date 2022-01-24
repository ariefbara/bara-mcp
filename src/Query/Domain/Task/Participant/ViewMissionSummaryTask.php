<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MissionSummaryRepository;

class ViewMissionSummaryTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var MissionSummaryRepository
     */
    protected $missionSummaryRepository;
    public $result;

    public function __construct(MissionSummaryRepository $missionSummaryRepository)
    {
        $this->missionSummaryRepository = $missionSummaryRepository;
    }

    public function execute(string $participantId): void
    {
        $this->result = $this->missionSummaryRepository->ofParticipantId($participantId);
    }

}
