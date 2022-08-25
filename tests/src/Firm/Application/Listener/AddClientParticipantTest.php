<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class AddClientParticipantTest extends TestBase
{

    protected $clientRegistrantRepository;
    protected $clientRegistrant;
    protected $registrantId = 'registrantId';
    protected $listener;
    //
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);

        $this->listener = new AddClientParticipant($this->clientRegistrantRepository);

        $this->event = new CommonEvent(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $this->registrantId);
    }
    
    protected function handle()
    {
        $this->clientRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn($this->clientRegistrant);
        $this->listener->handle($this->event);
    }
    public function test_handle_addClientRegistrantAsProgramParticipant()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('addAsProgramParticipant');
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noClientRegistrantCorrespondWithIdFound_void()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn(null);
        $this->clientRegistrantRepository->expects($this->never())
                ->method('update');
        $this->handle();
    }

}
