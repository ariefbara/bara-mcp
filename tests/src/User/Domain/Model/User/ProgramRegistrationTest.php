<?php

namespace User\Domain\Model\User;

use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;
use User\Domain\Model\User;

class ProgramRegistrationTest extends TestBase
{
    protected $user;
    protected $programRegistration;
    protected $registrant;
    
    protected $program, $programId = 'programId';
    protected $id = 'programRegistrationId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->buildMockOfClass(User::class);
        $program = $this->buildMockOfInterface(\User\Domain\Model\ProgramInterface::class);
        $program->expects($this->any())->method('isRegistrationOpenFor')->willReturn(true);
        
        $this->programRegistration = new TestableProgramRegistration($this->user, 'id', $program);
        $this->programRegistration->programId = $this->programId;
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->programRegistration->registrant = $this->registrant;
        
        $this->program = $this->buildMockOfInterface(\User\Domain\Model\ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
        
    }
    protected function executeConstruct()
    {
        $this->program->expects($this->any())
                ->method('isRegistrationOpenFor')
                ->willReturn(true);
        
        return new TestableProgramRegistration($this->user, $this->id, $this->program);
    }
    public function test_construct_setProperties()
    {
        $programRegistration = $this->executeConstruct();
        $this->assertEquals($this->user, $programRegistration->user);
        $this->assertEquals($this->id, $programRegistration->id);
        $this->assertEquals($this->programId, $programRegistration->programId);
        
        $registrant = new Registrant($this->program, $this->id);
        $this->assertEquals($registrant, $programRegistration->registrant);
    }
    public function test_construct_programRegistrationClosed_forbiddenError()
    {
        $this->program->expects($this->once())
                ->method('isRegistrationOpenFor')
                ->with(ParticipantTypes::USER_TYPE)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'forbidden: program registration closed';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_cancel_executeRegistrantsCancelMethod()
    {
        $this->registrant->expects($this->once())
                ->method('cancel');
        $this->programRegistration->cancel();
    }
    
    public function test_isUnconcludedRegistrationToProgram_returnRegistrantIsUnconcludedRegistrationToProgramResult()
    {
        $this->registrant->expects($this->once())
                ->method('isUnconcludedRegistrationToProgram')
                ->with($this->program);
        $this->programRegistration->isUnconcludedRegistrationToProgram($this->program);
    }
}

class TestableProgramRegistration extends ProgramRegistration
{
    public $user;
    public $id;
    public $programId;
    public $registrant;
}
