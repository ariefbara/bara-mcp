<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ {
    Manager,
    Program\Consultant
};
use Tests\TestBase;

class DisableConsultantTest extends TestBase
{

    protected $consultantRepository, $consultant;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $consultantId = "consultantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfClass(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method("aConsultantOfId")
                ->with($this->consultantId)
                ->willReturn($this->consultant);

        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfClass(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);

        $this->service = new DisableConsultant($this->consultantRepository, $this->managerRepository);
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->consultantId);
    }

    public function test_execute_disableConsultantInManager()
    {
        $this->manager->expects($this->once())
                ->method("disableConsultant")
                ->with($this->consultant);
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->consultantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
