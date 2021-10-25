<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;
use Query\Domain\Task\Dependency\Firm\Program\ActivityType\Activity\AttendeeRepository;

class ViewActivityAttendeeTask implements ITaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     * 
     * @var string
     */
    protected $attendeeId;

    /**
     * 
     * @var Invitee|null
     */
    public $result;

    public function __construct(AttendeeRepository $attendeeRepository, string $attendeeId)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->attendeeId = $attendeeId;
        $this->result = null;
    }

    public function executeTaskInProgram(Program $program): void
    {
        $this->result = $this->attendeeRepository->anAttendeeInProgram($program->getId(), $this->attendeeId);
    }

}
