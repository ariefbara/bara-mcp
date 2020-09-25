<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\Program;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class ProgramRegistrationTest extends TestBase
{
    protected $programRegistration;
    protected $program;
    
    protected $id = 'programRegistrationId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        
        $this->programRegistration = new TestableProgramRegistration($this->program, 'id');
    }
    
    public function test_construct_setProperties()
    {
        $programRegistration = new TestableProgramRegistration($this->program, $this->id);
        $this->assertEquals($this->program, $programRegistration->program);
        $this->assertEquals($this->id, $programRegistration->id);
        $this->assertFalse($programRegistration->concluded);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $programRegistration->registeredTime);
        $this->assertNull($programRegistration->note);
    }
    
    protected function executeCancel()
    {
        $this->programRegistration->cancel();
    }
    public function test_cancel_setConcludedTrueAndNoteCancelled()
    {
        $this->executeCancel();
        $this->assertTrue($this->programRegistration->concluded);
        $this->assertEquals('cancelled', $this->programRegistration->note);
    }
    public function test_cancel_alreadyConcluded_forbiddenError()
    {
        $this->programRegistration->concluded = true;
        
        $operation = function (){
            $this->executeCancel();
        };
        $errorDetail = 'forbidden: program registration already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeIsUnconcludedRegistrationToProgram()
    {
        return $this->programRegistration->isUnconcludedRegistrationToProgram($this->program);
    }
    public function test_isUnconcludedRegistrationToProgram_returnTrue()
    {
        $this->assertTrue($this->executeIsUnconcludedRegistrationToProgram());
    }
    public function test_isUnconcludedRegistrationToProgram_differentProgramId_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->programRegistration->isUnconcludedRegistrationToProgram($program));
    }
    public function test_isUnconcludedRegistrationToProgram_concludedProgramRegistration_returnFalse()
    {
        $this->programRegistration->concluded = true;
        $this->assertFalse($this->executeIsUnconcludedRegistrationToProgram());
    }
}

class TestableProgramRegistration extends ProgramRegistration
{
    public $program;
    public $id;
    public $concluded;
    public $registeredTime;
    public $note;
}
