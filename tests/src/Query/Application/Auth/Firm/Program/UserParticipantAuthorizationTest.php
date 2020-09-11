<?php

namespace Query\Application\Auth\Firm\Program;

use Tests\TestBase;

class UserParticipantAuthorizationTest extends TestBase
{
    protected $authZ;
    protected $participantRepository;
    
    protected $firmId = 'firmId', $programId = 'programId', $userId = 'userId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->authZ = new UserParticipantAuthorization($this->participantRepository);
    }
    
    protected function execute()
    {
        $this->participantRepository->expects($this->any())
                ->method('containRecordOfActiveParticipantCorrespondWithUser')
                ->willReturn(true);
        $this->authZ->execute($this->firmId, $this->programId, $this->userId);
    }
    
    public function test_execute_doNothing()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfActiveParticipantCorrespondWithUser_forbiddenError()
    {
        $this->participantRepository->expects($this->once())
                ->method('containRecordOfActiveParticipantCorrespondWithUser')
                ->with($this->firmId, $this->programId, $this->userId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only active program user participant can make this request";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
}
