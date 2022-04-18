<?php

namespace Client\Domain\DependencyModel\Firm\Program;

use Client\Domain\DependencyModel\Firm\Program;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\TestBase;

class RegistrantTest extends TestBase
{
    protected $registrant;
    protected $program;
    protected $registrationStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = new TestableRegistrant();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->registrant->program = $this->program;
        
        $this->registrationStatus = $this->buildMockOfClass(RegistrationStatus::class);
        $this->registrant->status = $this->registrationStatus;
    }
    
    protected function isActiveRegistrationCorrespondWithProgram()
    {
        return $this->registrant->isActiveRegistrationCorrespondWithProgram($this->program);
    }
    public function test_isActiveRegistrationCorrespondWithProgram_unconcludedRegistrationOfSameProgram_returnTrue()
    {
        $this->assertTrue($this->isActiveRegistrationCorrespondWithProgram());
    }
    public function test_isActiveRegistrationCorrespondWithProgram_concludedRegistration()
    {
        $this->registrationStatus->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $this->assertFalse($this->isActiveRegistrationCorrespondWithProgram());
    }
    public function test_isActiveRegistrationCorrespondWithProgram_differentProgram_returnFalse()
    {
        $this->registrant->program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->isActiveRegistrationCorrespondWithProgram());
    }
}

class TestableRegistrant extends Registrant
{
    public $program;
    public $id = 'registrant-id';
    public $status;
    
    function __construct()
    {
        parent::__construct();
    }
}
