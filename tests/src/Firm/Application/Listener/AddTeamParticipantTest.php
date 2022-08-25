<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class AddTeamParticipantTest extends TestBase
{
    protected $teamRegistrantRepository;
    protected $teamRegistrant;
    protected $registrantId = 'registrantId';
    
    protected $listener;
    //
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRegistrantRepository = $this->buildMockOfInterface(TeamRegistrantRepository::class);
        $this->teamRegistrant = $this->buildMockOfClass(TeamRegistrant::class);
        $this->listener = new AddTeamParticipant($this->teamRegistrantRepository);
        
        $this->event = new CommonEvent(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $this->registrantId);
    }
    
    protected function handle()
    {
        $this->teamRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn($this->teamRegistrant);
        $this->listener->handle($this->event);
    }
    public function test_handle_addTeamRegistrantAsProgramParticipant()
    {
        $this->teamRegistrant->expects($this->once())
                ->method('addAsProgramParticipant');
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->teamRegistrantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noTeamRegistrantFound_doNothing()
    {
        $this->teamRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn(null);
        $this->teamRegistrantRepository->expects($this->never())
                ->method('update');
        $this->handle();
        $this->markAsSuccess();
        
    }
}
