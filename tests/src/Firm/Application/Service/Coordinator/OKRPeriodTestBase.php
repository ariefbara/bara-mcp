<?php

namespace Tests\src\Firm\Application\Service\Coordinator;

use Firm\Application\Service\Coordinator\OKRPeriodRepository;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;
use PHPUnit\Framework\MockObject\MockObject;

class OKRPeriodTestBase extends CoordinatorTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $okrPeriodRepository;
    /**
     * 
     * @var MockObject
     */
    protected $okrPeriod;
    protected $okrPeriodId = 'okrPeriodId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->okrPeriodRepository = $this->buildMockOfInterface(OKRPeriodRepository::class);
        $this->okrPeriodRepository->expects($this->any())
                ->method('ofId')
                ->with($this->okrPeriodId)
                ->willReturn($this->okrPeriod);
    }
}
