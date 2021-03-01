<?php

namespace Tests\src\Participant\Domain\Model;

use Participant\Domain\Model\Participant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ParticipantTestBase extends TestBase
{
    /**
     * 
     * @var TestableParticipant
     */
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
    }
    protected function assertInactiveParticipantError(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: only active program participant can make this request');
    }
    protected function assertUnmanageableByParticipantError(callable $operation, string $assetName): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', "forbidden: unable to manage $assetName");
    }
    protected function setAssetManageable(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('isManageableByParticipant')
                ->with($this->participant)
                ->willReturn(true);
    }
    protected function setAssetUnmanageable(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('isManageableByParticipant')
                ->with($this->participant)
                ->willReturn(false);
    }
    
}

class TestableParticipant extends Participant
{
    public $program;
    public $id = 'participantId';
    public $active = true;
    public $note;
    public $metricAssignment;
    public $consultationRequests;
    public $consultationSessions;
    public $teamProgramParticipation;
    public $clientParticipant;
    public $userParticipant;
    public $completedMissions;
    public $profiles;
    public $okrPeriods;
    
    function __construct()
    {
        parent::__construct();
    }
}
