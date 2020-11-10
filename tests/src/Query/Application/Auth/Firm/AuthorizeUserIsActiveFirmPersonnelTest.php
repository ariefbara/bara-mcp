<?php

namespace Query\Application\Auth\Firm;

use Tests\TestBase;

class AuthorizeUserIsActiveFirmPersonnelTest extends TestBase
{
    protected $personnelRepository;
    protected $authZ;
    protected $firmId = "firmId", $personnelId = "personnelId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->authZ = new AuthorizeUserIsActiveFirmPersonnel($this->personnelRepository);
    }
    
    protected function execute()
    {
        $this->personnelRepository->expects($this->any())
                ->method("containRecordOfActivePersonnelInFirm")
                ->willReturn(true);
        $this->authZ->execute($this->firmId, $this->personnelId);
    }
    public function test_execute_repoContainRecordOfActivePersonnel_void()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfActivePersonnelInFirm_Forbidden()
    {
        $this->personnelRepository->expects($this->once())
                ->method("containRecordOfActivePersonnelInFirm")
                ->with($this->firmId, $this->personnelId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only active personnel can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
                
    }
}
