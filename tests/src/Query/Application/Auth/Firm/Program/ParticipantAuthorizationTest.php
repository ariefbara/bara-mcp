<?php

namespace Query\Application\Auth\Firm\Program;

class ParticipantAuthorizationTest extends \Tests\TestBase
{
    protected $authZ;
    protected $participantRepository;
    protected $firmId = 'firmId', $programId = 'programId', $clientId = 'clientId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->authZ = new ParticipantAuthorization($this->participantRepository);
    }
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->programId, $this->clientId);
    }
    public function test_execute_noActiveParticipantRecordCorrespondWithClientExist_throwEx()
    {
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'forbidden: only active program participant allow to make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_execute_aCoordinatorRecordCorrespondWithPersonnelExist_void()
    {
        $this->participantRepository->expects($this->once())
                ->method('containRecordOfactiveParticipantCorrespondWithClient')
                ->with($this->firmId, $this->programId, $this->clientId)
                ->willReturn(true);
        $this->execute();
    }
}
