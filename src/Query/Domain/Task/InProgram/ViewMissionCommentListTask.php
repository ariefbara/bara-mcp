<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Task\Dependency\Firm\Program\Mission\MissionCommentFilter;
use Query\Domain\Task\Dependency\Firm\Program\Mission\MissionCommentRepository;

class ViewMissionCommentListTask implements ITaskInProgramExecutableByParticipant
{

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    /**
     * 
     * @var MissionCommentFilter
     */
    protected $payload;

    /**
     * 
     * @var MissionComment[]|null
     */
    public $result;

    public function __construct(MissionCommentRepository $missionCommentRepository, MissionCommentFilter $payload)
    {
        $this->missionCommentRepository = $missionCommentRepository;
        $this->payload = $payload;
    }

    public function executeTaskInProgram(string $programId): void
    {
        $this->result = $this->missionCommentRepository->allMissionCommentInProgram($programId, $this->payload);
    }

}
