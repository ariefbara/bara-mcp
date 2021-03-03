<?php

namespace Tests\src\Participant\Application\Service\User;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriodData;
use PHPUnit\Framework\MockObject\MockObject;

class OKRPeriodTestBase extends UserTestBase
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
    /**
     * 
     * @var MockObject
     */
    protected $okrPeriodData;
    protected $okrPeriodId = 'okrPeriodId';
    protected $nextId = 'nextId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->okrPeriodRepository = $this->buildMockOfInterface(OKRPeriodRepository::class);
        $this->okrPeriodRepository->expects($this->any())
                ->method('ofId')
                ->with($this->okrPeriodId)
                ->willReturn($this->okrPeriod);
        $this->okrPeriodRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
    }
}
