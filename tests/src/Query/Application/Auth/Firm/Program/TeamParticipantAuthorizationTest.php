<?php

namespace Query\Application\Auth\Firm\Program;

use Tests\TestBase;

class TeamParticipantAuthorizationTest extends TestBase
{
    protected $authZ;
    protected $participantRepository;
    
    protected $teamId = "teamId", $programId = "programId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->authZ = new TeamParticipantAuthorization($this->participantRepository);
    }
    
    protected function execute()
    {
        $this->participantRepository->expects($this->any())
                ->method('containRecordOfActiveParticipantCorrespondWithTeam')
                ->willReturn(true);
        $this->authZ->execute($this->teamId, $this->programId);
    }
    
    public function test_execute_doNothing()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfActiveParticipantCorrespondWithUser_forbiddenError()
    {
        $this->participantRepository->expects($this->once())
                ->method('containRecordOfActiveParticipantCorrespondWithTeam')
                ->with($this->teamId, $this->programId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only active program participant can make this request";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
}
