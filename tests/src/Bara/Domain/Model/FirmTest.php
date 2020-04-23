<?php

namespace Bara\Domain\Model;

use Bara\Domain\Model\Firm\{
    Manager,
    ManagerData
};
use Tests\TestBase;

class FirmTest extends TestBase
{

    protected $firm;
    protected $admin;
    protected $id = 'new-id', $name = 'new firm name', $identifier = 'new_firm_identifier', $managerData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managerData = $this->buildMockOfClass(ManagerData::class);
        $this->managerData->expects($this->any())
                ->method('getName')
                ->willReturn('manager name');
        $this->managerData->expects($this->any())
                ->method('getEmail')
                ->willReturn('manager@email.org');
        $this->managerData->expects($this->any())
                ->method('getPassword')
                ->willReturn('password123');

        $this->firm = new TestableFirm('id', 'firm name', 'identifier', $this->managerData);
    }

    private function executeConstruct()
    {
        return new TestableFirm($this->id, $this->name, $this->identifier, $this->managerData);
    }

    public function test_construct_setProperties()
    {
        $firm = $this->executeConstruct();
        $this->assertEquals($this->id, $firm->id);
        $this->assertEquals($this->name, $firm->name);
        $this->assertEquals($this->identifier, $firm->identifier);
        $this->assertFalse($firm->suspended);
    }

    public function test_construct_setManager()
    {
        $firm = $this->executeConstruct();
        $this->assertInstanceOf(Manager::class, $firm->managers->first());
    }

    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    
    public function test_construct_identifierContainNonAlphanumericUnderscoreOrHypen_throwEx()
    {
        $this->identifier = 'containINvalidChar*#$';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm identifier is required and must only contain alphanumeric, underscore and hypen character without whitespace';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_identifierContainWhitespace_throwEx()
    {
        $this->identifier = 'contain whitespace';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm identifier is required and must only contain alphanumeric, underscore and hypen character without whitespace';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_suspend_setSuspendedTrue()
    {
        $this->firm->suspend();
        $this->assertTrue($this->firm->suspended);
    }

}

class TestableFirm extends Firm
{

    public $id, $name, $identifier, $suspended;
    public $managers;

}
