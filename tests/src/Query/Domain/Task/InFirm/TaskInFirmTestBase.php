<?php

namespace Tests\src\Query\Domain\Task\InFirm;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Domain\Model\Firm;
use Tests\TestBase;

class TaskInFirmTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $firm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
}
