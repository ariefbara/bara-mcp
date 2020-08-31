<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $firm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->program->firm = $this->firm;
    }
    
    public function test_getFirmWhitelableInfo_returnFirmsGetWhitelableInfoResult()
    {
        $this->firm->expects($this->once())
                ->method('getFirmWhitelableInfo');
        $this->program->getFirmWhitelableInfo();
    }
}

class TestableProgram extends Program
{
    public $firm;
    public $id;
    public $name;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
