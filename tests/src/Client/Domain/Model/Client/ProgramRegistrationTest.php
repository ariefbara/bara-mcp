<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ {
    Client,
    ProgramInterface
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class ProgramRegistrationTest extends TestBase
{
    protected $programRegistration;
    protected $client;
    protected $program, $programId = 'programId';
    protected $registrant;
    protected $id = 'newProgramRegistrationId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        
        $program = $this->buildMockOfClass(ProgramInterface::class);
        $program->expects($this->any())
                ->method('isRegistrationOpenFor')
                ->willReturn(true);
        
        $this->programRegistration = new TestableProgramRegistration($this->client, 'id', $program);
        $this->programRegistration->programId = $this->programId;
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->programRegistration->registrant = $this->registrant;
        
        $this->program = $this->buildMockOfClass(ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
    }
    
    protected function executeConstruct()
    {
        $this->program->expects($this->any())
                ->method('isRegistrationOpenFor')
                ->willReturn(true);
        return new TestableProgramRegistration($this->client, $this->id, $this->program);
    }
    public function test_construct_setProperties()
    {
        $programRegistration = $this->executeConstruct();
        $this->assertEquals($this->client, $programRegistration->client);
        $this->assertEquals($this->id, $programRegistration->id);
        $this->assertEquals($this->programId, $programRegistration->programId);
        
        $registrant = new Registrant($this->program, $this->id);
        $this->assertEquals($registrant, $programRegistration->registrant);
    }
    public function test_construct_programRegistrationClosedForClientParticipantType_forbiddenError()
    {
        $this->program->expects($this->once())
                ->method('isRegistrationOpenFor')
                ->with(ParticipantTypes::CLIENT_TYPE)
                ->willReturn(false);
        $operation  = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'forbidden: program registration is closed';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    public function test_cancel_cancelRegistrant()
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
    public $client;
    public $id;
    public $programId;
    public $registrant;
}
