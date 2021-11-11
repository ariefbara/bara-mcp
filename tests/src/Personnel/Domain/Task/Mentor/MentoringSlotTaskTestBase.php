<?php

namespace Tests\src\Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlotRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class MentoringSlotTaskTestBase extends MentorTaskTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var MockObject
     */
    protected $mentoringSlot;
    protected $mentoringSlotId = 'mentoringSlotId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringSlot = $this->buildMockOfClass(MentoringSlot::class);
        $this->mentoringSlotRepository = $this->buildMockOfInterface(MentoringSlotRepository::class);
        $this->mentoringSlotRepository->expects($this->any())
                ->method('ofId')
                ->with($this->mentoringSlotId)
                ->willReturn($this->mentoringSlot);
    }

}
