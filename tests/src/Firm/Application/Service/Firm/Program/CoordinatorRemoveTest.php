<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Tests\TestBase;

class CoordinatorRemoveTest extends TestBase
{

    protected $service;
    protected $coordinatorRepository, $coordinator, $programCompositionId, $coordinatorId = 'coordinator-id';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programCompositionId = $this->buildMockOfClass(ProgramCompositionId::class);

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programCompositionId, $this->coordinatorId)
            ->willReturn($this->coordinator);

        $this->service = new CoordinatorRemove($this->coordinatorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programCompositionId, $this->coordinatorId);
    }
    public function test_execute_removeCoordinator()
    {
        $this->coordinator->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }

}
