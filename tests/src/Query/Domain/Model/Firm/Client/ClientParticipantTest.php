<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\ {
    Model\Firm\Program\Participant,
    Service\LearningMaterialFinder
};
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant;
    protected $participant;
    protected $learningMaterialFinder;
    protected $learningMaterialId = "learningMaterialId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = new TestableClientParticipant();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant->participant = $this->participant;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
    }
    
    public function test_viewLearningMaterial_returnParticipantsViewLearningMaterialResult()
    {
        $this->participant->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->clientParticipant->viewLearningMaterial($this->learningMaterialFinder, $this->learningMaterialId);
    }
    
    public function test_pullRecordedEvents_returnParticipantPullRecordedEventsResult()
    {
        $this->participant->expects($this->once())
                ->method("pullRecordedEvents");
        $this->clientParticipant->pullRecordedEvents();
    }
}

class TestableClientParticipant extends ClientParticipant
{
    public $client;
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
