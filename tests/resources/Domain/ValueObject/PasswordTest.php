<?php
namespace Resources\Domain\ValueObject;

use Tests\TestBase;

class PasswordTest extends TestBase
{
    protected $password = 'password123';
    protected $vo;
    
    protected function setUp(): void {
        parent::setUp();
        $this->vo = new TestablePassword($this->password);
    }
    
    private function executeConstruct() {
        return new TestablePassword($this->password);
    }
    function test_construct_createPasswordVO() {
        $vo = $this->executeConstruct();
        $this->assertInstanceOf('Resources\Domain\ValueObject\Password', $vo);
    }
    function test_construct_setPasswordAsHashedPassword() {
        $vo = $this->executeConstruct();
        $this->assertNotEmpty($vo->getPassword());
        $this->assertTrue(password_verify($this->password, $vo->getPassword()));
    }
    function test_construct_invalidPasswordFormat() {
        /**
         * Password must at least 8 character contain alphabet and number
         */
//         $this->password = 'short1';
//         $this->password = 'noNumberCharacter';
        $this->password = '123123123123123';

        $operation = function (){
            $this->executeConstruct();
        };
        
        $errorDetail = "bad request: password required at least 8 character long contain alphabet and number";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    function test_match_matchedPassword_returnTrue() {
        $this->assertTrue($this->vo->match($this->password));
    }
    function test_match_unmatchedPassword_returnFalse() {
        $this->assertFalse($this->vo->match('unmatchedPassword'));
    }
}
class TestablePassword extends Password{
    function getPassword() {
        return $this->password;
    }
}

