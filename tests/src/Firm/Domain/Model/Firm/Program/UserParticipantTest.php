<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Firm\Program,
    User
};
use Resources\ {
    Application\Service\Mailer,
    Domain\ValueObject\PersonName
};
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    
    protected $id = 'id', $userId = 'userId';
    
    protected $registrant;

    protected function setUp(): void
    {
        parent::setUp();
        
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant = new TestableUserParticipant($this->participant, 'id', 'userId');
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
    }
    
    public function test_construct_setProperties()
    {
        $userParticipant = new TestableUserParticipant($this->participant, $this->id, $this->userId);
        $this->assertEquals($this->participant, $userParticipant->participant);
        $this->assertEquals($this->id, $userParticipant->id);
        $this->assertEquals($this->userId, $userParticipant->userId);
    }
    
    public function test_correspondWithRegistrant_returnRegistrantsCorrespondWithUserResult()
    {
        $this->registrant->expects($this->once())
                ->method('correspondWithUser')
                ->with($this->userParticipant->userId);
        $this->userParticipant->correspondWithRegistrant($this->registrant);
    }
    
}

class TestableUserParticipant extends UserParticipant
{
    public $participant;
    public $id;
    public $userId;
}
