<?php
namespace Resources\Domain\Model\Mail;

use Tests\TestBase;
use Resources\Domain\ValueObject\Email;
use Resources\Domain\ValueObject\HumanName;

class RecipientTest extends TestBase
{
    protected $name = 'account name', $address = 'account@email.org';
    
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    private function executeConstruct()
    {
        return new TestableRecipient($this->address, $this->name);
    }
    function test_construct_setProperties() {
        $recipient = $this->executeConstruct();
        $this->assertEquals($this->address, $recipient->address);
        $this->assertEquals($this->name, $recipient->name);
    }
    public function test_construct_invalidAddressFormat_throwEx()
    {
        $this->address = 'invalid address';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mail recipient address is required and must be in valid email address format";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mail recipient name is required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
}

class TestableRecipient extends Recipient{
    public $name, $address;
}

