<?php

namespace Payment\Domain\Model\Firm;

use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client, $personName;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->client->personName = $this->personName;
    }
    
    protected function generateCustomerInfo()
    {
        return $this->client->generateCustomerInfo();
    }
    public function test_generateCustomerInfo_returnCustomerInfo()
    {
        $this->personName->expects($this->once())
                ->method('getFullName')
                ->willReturn($name = 'client full name');
        $customerInfo = new CustomerInfo($name, $this->client->email);
        $this->assertEquals($customerInfo, $this->generateCustomerInfo());
    }
    
}

class TestableClient extends Client
{
    public $id = 'clientId';
    public $personName;
    public $email = 'client@email.org';
    
    function __construct()
    {
        parent::__construct();
    }
}
