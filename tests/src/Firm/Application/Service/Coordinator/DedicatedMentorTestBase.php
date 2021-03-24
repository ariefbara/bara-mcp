<?php

namespace Tests\src\Firm\Application\Service\Coordinator;

use Firm\Application\Service\Coordinator\DedicatedMentorRepository;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use PHPUnit\Framework\MockObject\MockObject;

class DedicatedMentorTestBase extends CoordinatorTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $dedicatedMentorRepository;
    /**
     * 
     * @var MockObject
     */
    protected $dedicatedMentor;
    protected $dedicatedMentorId = 'dedicatedMentorId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentorRepository = $this->buildMockOfInterface(DedicatedMentorRepository::class);
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->dedicatedMentorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->dedicatedMentorId)
                ->willReturn($this->dedicatedMentor);
    }
}
