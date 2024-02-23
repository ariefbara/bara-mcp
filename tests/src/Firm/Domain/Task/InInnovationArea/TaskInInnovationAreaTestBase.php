<?php

namespace Tests\src\Firm\Domain\Task\InInnovationArea;

use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TaskInInnovationAreaTestBase extends TestBase
{
    protected MockObject $innovationArea;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->innovationArea = $this->buildMockOfClass(InnovationArea::class);
    }
    
}
