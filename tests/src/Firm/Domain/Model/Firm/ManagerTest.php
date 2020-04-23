<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Tests\TestBase;

class ManagerTest extends TestBase
{

    protected $firm;
    protected $id = 'new-id', $name = 'new manager name', $email = 'new_address@email.org', $password = 'password123',
        $phone = '08112313123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
    }

    private function executeConstruct()
    {
        return new TestableManager($this->firm, $this->id, $this->getManagerData());
    }
    protected function getManagerData()
    {
        return new ManagerData($this->name, $this->email, $this->password, $this->phone);
    }

    public function test_construct_setProperties()
    {
        $manager = $this->executeConstruct();
        $this->assertEquals($this->firm, $manager->firm);
        $this->assertEquals($this->id, $manager->id);
        $this->assertEquals($this->name, $manager->name);
        $this->assertEquals($this->email, $manager->email);
        $this->assertTrue($manager->password->match($this->password));
        $this->assertEquals($this->phone, $manager->phone);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $manager->joinTime->format('Y-m-d H:i:s'));
        $this->assertFalse($manager->removed);
    }

    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager name is required';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    public function test_construct_invalidEmail_throwEx()
    {
        $this->email = 'invalid address';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager email is required and must be in valid email format';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    public function test_construct_invalidPhoneFormat_throwEx()
    {
        $this->phone = 'invalid phone format';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager phone must be in valid phone format';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    public function test_construct_emptyPhone_processNormally()
    {
        $this->phone = '';
        $this->executeConstruct();
        $this->markAsSuccess();
    }

}

class TestableManager extends Manager
{

    public $firm, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $adminAssignments;

}
