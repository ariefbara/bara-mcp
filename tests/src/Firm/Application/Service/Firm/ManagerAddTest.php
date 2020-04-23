<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\ManagerData
};
use Tests\TestBase;

class ManagerAddTest extends TestBase
{
    protected $service;
    protected $managerRepository;
    protected $firmRepository, $firmId = 'firmId';
    protected $managerData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        
        $this->service = new ManagerAdd($this->managerRepository, $this->firmRepository);
        $this->managerData = $this->buildMockOfClass(ManagerData::class);
        $this->managerData->expects($this->any())
                ->method('getName')
                ->willReturn('new manager name');
        $this->managerData->expects($this->any())
                ->method('getEmail')
                ->willReturn('manager@email.org');
        $this->managerData->expects($this->any())
                ->method('getPassword')
                ->willReturn('password123');
    }
    
    protected function execute()
    {
        $this->managerRepository->expects($this->any())
                ->method('isEmailAvailable')
                ->willReturn(true);
        $this->service->execute($this->firmId, $this->managerData);
    }
    
    public function test_execute_addManagerToRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_emailUnavailable_throwEx()
    {
        $this->managerRepository->expects($this->once())
                ->method('isEmailAvailable')
                ->with($this->firmId, $this->managerData->getEmail())
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "conflict: email already registered";
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
    }
}
