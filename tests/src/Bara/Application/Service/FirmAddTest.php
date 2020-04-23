<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Firm\ManagerData;
use Tests\TestBase;

class FirmAddTest extends TestBase
{

    protected $service;
    protected $firmRepository;
    protected $managerRepository;
    protected $adminRepository;
    protected $name = 'firm name', $identifier = 'identifier';
    protected $managerData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->service = new FirmAdd($this->firmRepository);

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
    }

    private function execute()
    {
        return $this->service->execute($this->name, $this->identifier, $this->managerData);
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
            ->with($this->identifier)
            ->willReturn(true);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'conflict: firm identifier already used';
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
    }

}
