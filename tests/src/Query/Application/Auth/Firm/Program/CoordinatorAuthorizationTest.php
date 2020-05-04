<?php

namespace Query\Application\Auth\Firm\Program;

use Tests\TestBase;

class CoordinatorAuthorizationTest extends TestBase
{
    protected $authZ;
    protected $coordinatorRepository;
    protected $firmId = 'firmId', $personnelId = 'programId', $programId = 'programId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->authZ = new CoordinatorAuthorization($this->coordinatorRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->personnelId, $this->programId);
    }
    
    public function test_execute_noCoordinatorRecordCorrespondWithPersonnelExist_throwEx()
    {
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "unauthorized: only program coordinator allow to make this request";
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }
    public function test_execute_aCoordinatorRecordCorrespondWithPersonnelExist_void()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('containRecordOfUnremovedCoordinatorCorrespondWithPersonnel')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn(true);
        $this->execute();
    }
}
