<?php

namespace Tests\src\Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class FirmTaskTestBase extends TestBase
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
