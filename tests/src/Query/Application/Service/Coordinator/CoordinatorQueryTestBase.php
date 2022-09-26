<?php

namespace Tests\src\Query\Application\Service\Coordinator;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Coordinator\CoordinatorRepository;
use Query\Domain\Model\Firm\Program\Coordinator;
use Tests\TestBase;

class CoordinatorQueryTestBase extends TestBase
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
    protected $firmId = 'firmId', $personnelId = 'personnelId', $coordinatorId = 'coordinatorId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository->expects($this->any())
                ->method('aCoordinatorBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->coordinatorId)
                ->willReturn($this->coordinator);
    }

}
