<?php

namespace Firm\Application\Service\Personnel\ConsultantAttendee;

use Resources\Application\Event\Dispatcher;

class InviteAllActiveDedicatedMentees
{

    /**
     * 
     * @var ConsultantAttendeeRepository
     */
    protected $consultantAttendeeRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(ConsultantAttendeeRepository $consultantAttendeeRepository, Dispatcher $dispatcher)
    {
        $this->consultantAttendeeRepository = $consultantAttendeeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId): void
    {
        $consultantAttendee = $this->consultantAttendeeRepository
                ->aConsultantAttendeeBelongsToPersonnel($firmId, $personnelId, $meetingId);
        $consultantAttendee->inviteAllActiveDedicatedMentees();
        
        $this->consultantAttendeeRepository->update();
        
        $this->dispatcher->dispatch($consultantAttendee);
    }

}
