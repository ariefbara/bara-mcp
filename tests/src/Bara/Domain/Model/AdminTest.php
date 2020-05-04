<?php

namespace Bara\Domain\Model;

use Tests\TestBase;

class AdminTest extends TestBase
{

    protected $admin, $originalEmail = 'original_address@email.org', $originalPassword = 'originalPwd123';
    protected $id = 'newid', $name = 'new sys admin name', $email = 'newAdmin@email.org', $password = 'newPwd123';

    protected function setUp(): void
    {
        parent::setUp();
        $adminData = new AdminData('name', $this->originalEmail);
        $this->admin = new TestableAdmin('id', $adminData, $this->originalPassword);
    }

    protected function executeConstruct()
    {
        return new TestableAdmin($this->id, $this->getAdminInput(), $this->password);
    }
    protected function getAdminInput()
    {
        return new AdminData($this->name, $this->email);
    }

    function test_construct_setProperties()
    {
        $admin = $this->executeConstruct();
        $this->assertEquals($this->id, $admin->id);
        $this->assertEquals($this->name, $admin->name);
        $this->assertEquals($this->email, $admin->email);
        $this->assertTrue($admin->password->match($this->password));
        $this->assertFalse($admin->removed);
    }

    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: sys admin name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_invalidEmailAddress_throwEx()
    {
        $this->email = 'invalid email';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: sys admin email is required and must be in valid email format';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_emptyEmail_throwEx()
    {
        $this->email = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: sys admin email is required and must be in valid email format';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    protected function executeUpdate()
    {
        $this->admin->updateProfile($this->getAdminInput());
    }

    function test_updateProfile_changeNameAndEmail()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->admin->name);
        $this->assertEquals($this->email, $this->admin->email);
    }

    public function test_updateProfile_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeUpdate();
        };
        $errorDetail = 'bad request: sys admin name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_updateProfile_invalidEmail_throwEx()
    {
        $this->email = '';
        $operation = function () {
            $this->executeUpdate();
        };
        $errorDetail = 'bad request: sys admin email is required and must be in valid email format';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    function test_changePassword_changePassword()
    {
        $this->admin->changePassword($this->originalPassword, $this->password);
        $this->assertTrue($this->admin->password->match($this->password));
    }

    function test_changePassword_unmatchedPreviousPassword_throwEx()
    {
        $operation = function () {
            $this->admin->changePassword('unmatched', $this->password);
        };
        $errorDetail = 'forbidden: previous password not match';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    function test_emailEquals_sameValue_returnTrue()
    {
        $this->assertTrue($this->admin->emailEquals($this->originalEmail));
    }

    function test_emailEquals_differentValue_returnFalse()
    {
        $this->assertFalse($this->admin->emailEquals('different_address@email.org'));
    }

    function test_remove_setRemovedTrue()
    {
        $this->admin->remove();
        $this->assertTrue($this->admin->removed);
    }
    
}

class TestableAdmin extends Admin
{

    public $id, $name, $email, $password, $removed;

}
