<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Client,
    Program
};
use Resources\ {
    Application\Service\Mailer,
    Domain\ValueObject\PersonName
};
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant, $participant;
    
    protected $program;
    protected $client;
    
    protected $id = 'newClientParticipantId';
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->clientParticipant = new TestableClientParticipant($this->program, 'id', $this->client);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant->participant = $this->participant;
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        $this->client->expects($this->any())->method('getEmail')->willReturn('client@email.org');
        $this->client->expects($this->any())->method('getPersonName')->willReturn(new PersonName('hadi', 'pranoto'));
    }
    
    public function test_construct_setProperties()
    {
        $clientParticipant = new TestableClientParticipant($this->program, $this->id, $this->client);
        $this->assertEquals($this->program, $clientParticipant->program);
        $this->assertEquals($this->id, $clientParticipant->id);
        $this->assertEquals($this->client, $clientParticipant->client);
        
        $participant = new Participant($this->id);
        $this->assertEquals($participant, $clientParticipant->participant);
    }
    
    public function test_bootout_executeParticipantsBootoutMethod()
    {
        $this->participant->expects($this->once())
                ->method('bootout');
        $this->clientParticipant->bootout();
    }
    
    public function test_reenroll_executeParticipantsReenrollMethod()
    {
        $this->participant->expects($this->once())
                ->method('reenroll');
        $this->clientParticipant->reenroll();
    }
    
    public function test_correspondWithRegistrant_returnRegistrantClientEqualsMethod()
    {
        $clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $clientRegistrant->expects($this->once())
                ->method('clientEquals')
                ->with($this->client)
                ->willReturn(true);
        
        $this->assertTrue($this->clientParticipant->correspondWithRegistrant($clientRegistrant));
    }
    
    public function test_sendRegistrationAcceptedMail_setMailToMailer()
    {
        $this->mailer->expects($this->once())
                ->method('send');
        $this->clientParticipant->sendRegistrationAcceptedMail($this->mailer);
    }
    
    public function test_getClientMailRecipient_returnClienstGetMailRecipientResult()
    {
        $this->client->expects($this->once())
                ->method('getMailRecipient');
        $this->clientParticipant->getClientMailRecipient();
    }
    
    public function test_getClientName_returnClientsGetNameResult()
    {
        $this->client->expects($this->once())
                ->method('getName');
        $this->clientParticipant->getClientName();
    }
    
}

class TestableClientParticipant extends ClientParticipant
{
    public $program;
    public $id;
    public $client;
    public $participant;
}
