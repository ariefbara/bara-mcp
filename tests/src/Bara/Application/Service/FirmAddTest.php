<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\ {
    Firm\ManagerData,
    FirmData
};
use Tests\TestBase;

class FirmAddTest extends TestBase
{

    protected $firmRepository;
    protected $dispatcher;
    protected $service;
    protected $managerRepository;
    protected $adminRepository;
    protected $firmData;
    protected $managerData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->dispatcher = $this->buildMockOfClass(\Resources\Application\Event\Dispatcher::class);
        $this->service = new FirmAdd($this->firmRepository, $this->dispatcher);

        $this->managerData = $this->buildMockOfClass(ManagerData::class);
        $this->managerData->expects($this->any())
            ->method('getName')
            ->willReturn('admin name');
        $this->managerData->expects($this->any())
            ->method('getEmail')
            ->willReturn('admin@email.org');
        $this->managerData->expects($this->any())
            ->method('getPassword')
            ->willReturn('password123');
        
        $this->firmData = new FirmData('name', 'identifier', 'http://firm.com', 'noreply@firm.com', 'firm name', 7.5);
    }

    private function execute()
    {
        return $this->service->execute($this->firmData, $this->managerData);
    }

    public function test_execute_addFirmToRepository()
    {
        $this->firmRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    
    public function test_execute_identifierNotAvailable_throwEx()
    {
        $this->firmRepository->expects($this->once())
            ->method('containRecordOfIdentifier')
            ->with($this->firmData->getIdentifier())
            ->willReturn(true);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'conflict: firm identifier already used';
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
    }
    public function test_execute_returnNewId()
    {
        $this->firmRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }
    public function test_execute_dispatchFirm()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch');
        $this->execute();
    }

}
