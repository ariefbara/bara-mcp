<?php

namespace Query\Domain\Model\Firm;

use Tests\TestBase;

class ParticipantTypesTest extends TestBase
{
    protected $participantTypes;
    protected $types = ['user'];
    protected $type = 'user';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->participantTypes = new TestableParticipantTypes(['user', 'client']);
        
    }
    
    protected function executeConstruct()
    {
        return new TestableParticipantTypes($this->types);
    }
    public function test_construct_setProperties()
    {
        $participantTypes = $this->executeConstruct();
        $this->assertEquals($this->types, $participantTypes->values);
    }
    public function test_construct_containInvalidType_forbiddenError()
    {
        $this->types[] = 'invalid';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'forbidden: unrecognized participat type';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_construct_containClientAndTeamValue()
    {
        $this->types = ['client'];
        $this->executeConstruct();
        $this->types = ['team'];
        $this->executeConstruct();
        $this->types = ['user', 'client', 'team'];
        $this->executeConstruct();
        
        $this->markAsSuccess();
    }
    
    protected function executeContain()
    {
        return $this->participantTypes->hasType($this->type);
    }
    
    public function test_contain_storedValuesIncludeType_returnTrue()
    {
        $this->assertTrue($this->executeContain());
    }
    public function test_contain_storedValuesDoesntContainType_returnFalse()
    {
        $this->type = 'team';
        $this->assertFalse($this->executeContain());
    }
}

class TestableParticipantTypes extends ParticipantTypes
{
    public $values;
}
