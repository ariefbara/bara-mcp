<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Task\Dependency\Firm\Program\Mission\MissionCommentRepository;

class ViewMissionCommentTask implements ITaskInProgramExecutableByParticipant
{

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var MissionComment|null
     */
    public $result;

    public function __construct(MissionCommentRepository $missionCommentRepository, string $id)
    {
        $this->missionCommentRepository = $missionCommentRepository;
        $this->id = $id;
    }

    public function executeTaskInProgram(string $programId): void
    {
        $this->result = $this->missionCommentRepository->aMissionCommentInProgram($programId, $this->id);
    }

}
