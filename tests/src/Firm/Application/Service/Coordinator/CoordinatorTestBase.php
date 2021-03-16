<?php

namespace Tests\src\Firm\Application\Service\Coordinator;

use Firm\Application\Service\Coordinator\CoordinatorRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class CoordinatorTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $coordinatorRepository;
    /**
     * 
     * @var MockObject
     */
    protected $coordinator;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(\Firm\Domain\Model\Firm\Program\Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method('aCoordinatorCorrespondWithProgram')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);
    }
}
