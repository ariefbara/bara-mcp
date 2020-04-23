<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Firm;
use Tests\TestBase;

class FirmSuspendTest extends TestBase
{

    protected $service;
    protected $firmRepository, $firm, $firmId = 'firm-id';

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firmRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId)
            ->willReturn($this->firm);

        $this->service = new FirmSuspend($this->firmRepository);
    }

    private function execute()
    {
        $this->service->execute($this->firmId);
    }

    public function test_execute_suspendFirm()
    {
        $this->firm->expects($this->once())
            ->method('suspend');
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->firmRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }

}
