<?php

namespace Tests\src\Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriodData;
use PHPUnit\Framework\MockObject\MockObject;

class OKRPeriodBaseTest extends TeamMemberBaseTest
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
    protected $nextOKRPeriodId = 'nextOkrPeriodId';
    /**
     * 
     * @var MockObject
     */
    protected $okrPeriodData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->okrPeriodRepository = $this->buildMockOfInterface(OKRPeriodRepository::class);
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        
        $this->okrPeriodRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextOKRPeriodId);
        
        $this->okrPeriodRepository->expects($this->any())
                ->method('ofId')
                ->with($this->okrPeriodId)
                ->willReturn($this->okrPeriod);
        
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
    }
}
