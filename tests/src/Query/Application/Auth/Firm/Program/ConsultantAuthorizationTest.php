<?php

namespace Query\Application\Auth\Firm\Program;

use Tests\TestBase;

class ConsultantAuthorizationTest extends TestBase
{
    protected $consultantRepository;
    protected $auth;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->auth = new ConsultantAuthorization($this->consultantRepository);
    }
    
    protected function execute()
    {
        $this->consultantRepository->expects($this->any())
                ->method('containRecordOfUnremovedConsultantCorrespondWithPersonnel')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn(true);
        $this->auth->execute($this->firmId, $this->personnelId, $this->programId);
    }
    
    public function test_execute_hasRecordOfConsultantCorrespondToPersonnel_void()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noConsultantRecordCorrespondToPersonnel_throwEx()
    {
        $this->consultantRepository->expects($this->once())
                ->method('containRecordOfUnremovedConsultantCorrespondWithPersonnel')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "unauthorized: only program consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }
}
