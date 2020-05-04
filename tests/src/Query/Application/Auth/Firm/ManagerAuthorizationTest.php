<?php

namespace Query\Application\Auth\Firm;

use Tests\TestBase;

class ManagerAuthorizationTest extends TestBase
{
    protected $authZ;
    protected $managerRepository;
    protected $firmId = 'firmId', $managerId = 'managerId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->authZ = new ManagerAuthorization($this->managerRepository);
    }
    
    protected function execute()
    {
        $this->managerRepository->expects($this->any())
                ->method('containRecordOfId')
                ->willReturn(true);
        $this->authZ->execute($this->firmId, $this->managerId);
    }
    
    public function test_execute_void()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    
    public function test_execute_noRecordOfManagerInRepository_throwEx()
    {
        $this->managerRepository->expects($this->once())
                ->method('containRecordOfId')
                ->with($this->firmId, $this->managerId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "unauthorized: only firm manager can make this request";
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }
}
