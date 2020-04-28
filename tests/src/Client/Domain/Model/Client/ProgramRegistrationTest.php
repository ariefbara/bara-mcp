<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ {
    Client,
    Firm\Program
};
use Tests\TestBase;

class ProgramRegistrationTest extends TestBase
{
    protected $program;
    protected $client;
    protected $id = 'programRegistration-id';
    
    protected $programRegistration;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->client = $this->buildMockOfClass(Client::class);
        
        
        $program = $this->buildMockOfClass(Program::class);
        $program->expects($this->any())
                ->method('canAcceptRegistration')
                ->willReturn(true);
        $this->programRegistration = new TestableProgramRegistration($this->client, 'id', $program);
    }
    
    protected function executeConstruct()
    {
        $this->program->expects($this->any())
                ->method('canAcceptRegistration')
                ->willReturn(true);
        return new TestableProgramRegistration($this->client, $this->id, $this->program);
    }
    
    public function test_construct_setProperties()
    {
        $programRegistration = $this->executeConstruct();
        $this->assertEquals($this->client, $programRegistration->client);
        $this->assertEquals($this->id, $programRegistration->id);
        $this->assertEquals($this->program, $programRegistration->program);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $this->programRegistration->appliedTime->format('Y-m-d H:i:s'));
        $this->assertFalse($programRegistration->concluded);
        $this->assertNull($programRegistration->note);
    }
    public function test_construct_programCantAcceptRegistration_throwEx()
    {
        $this->program->expects($this->once())
                ->method('canAcceptRegistration')
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: program can't accept registration";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_cancel_setConcludedTrue()
    {
        $this->programRegistration->cancel();
        $this->assertTrue($this->programRegistration->concluded);
    }
    public function test_cancel_setNoteCancelled()
    {
        $this->programRegistration->cancel();
        $this->assertEquals('cancelled', $this->programRegistration->note);
    }
    public function test_cancel_programRegistrationAlreadyConcluded_throwEx()
    {
        $this->programRegistration->concluded = true;
        $operation = function (){
            $this->programRegistration->cancel();
        };
        $errorDetail = 'forbidden: program registration already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
}

class TestableProgramRegistration extends ProgramRegistration{
    public $program, $id, $client, $appliedTime, $concluded, $note;
}
