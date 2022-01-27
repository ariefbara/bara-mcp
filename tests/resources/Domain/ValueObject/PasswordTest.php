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
    public function test_construct_lengthLessThanEight_400()
    {
        $this->password = "short1";
        $this->assertRegularExceptionThrowed(function(){
            $this->executeConstruct();
        }, 'Bad Request', 'bad request: minimum password length is 8 character and must contain combination of alphabet and number');
    }
    public function test_construct_withoutNumber_400()
    {
        $this->password = "passwordwithoutnumber*&(*&";
        $this->assertRegularExceptionThrowed(function(){
            $this->executeConstruct();
        }, 'Bad Request', 'bad request: minimum password length is 8 character and must contain combination of alphabet and number');
    }
    public function test_construct_withoutAlphabet_400()
    {
        $this->password = "213123123*(*$%!@#2323";
        $this->assertRegularExceptionThrowed(function(){
            $this->executeConstruct();
        }, 'Bad Request', 'bad request: minimum password length is 8 character and must contain combination of alphabet and number');
    }
    public function test_construct_containSpecialCharacter()
    {
        $this->password = "*#&^$*&#^HKJHKJHjhskdjfh231231";
        $this->executeConstruct();
        $this->markAsSuccess();
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

