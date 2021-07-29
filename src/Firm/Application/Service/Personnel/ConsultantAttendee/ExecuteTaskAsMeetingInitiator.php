<?php

namespace Firm\Application\Service\Personnel\ConsultantAttendee;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;

class ExecuteTaskAsMeetingInitiator
{

    /**
     * 
     * @var ConsultantAttendeeRepository
     */
    protected $consultantAttendeeRepository;

    public function __construct(ConsultantAttendeeRepository $consultantAttendeeRepository)
    {
        $this->consultantAttendeeRepository = $consultantAttendeeRepository;
    }
    
    public function execute(
            string $firmid, string $personnelId, string $attendeeId, ITaskExecutableByMeetingInitiator $task): void
    {
        $this->consultantAttendeeRepository
                ->aConsultantAttendeeBelongsToPersonnel($firmid, $personnelId, $attendeeId)
                ->executeTaskAsMeetingInitiator($task);
        $this->consultantAttendeeRepository->update();
    }

}
