<?php

namespace Query\Domain\Model;

class FirmWhitelableInfoTest extends \Tests\TestBase
{
    protected $url = 'http://barapraja.com/consulta', $mailSenderAddress = 'noreply@barapraja.com', $mailSenderName = 'team barapraja';
    
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function executeConstruct()
    {
        return new TestableFirmWhitelableInfo($this->url, $this->mailSenderAddress, $this->mailSenderName);
    }
    public function test_construct_setProperties()
    {
        $whitelable = $this->executeConstruct();
        $this->assertEquals($this->url, $whitelable->url);
        $this->assertEquals($this->mailSenderAddress, $whitelable->mailSenderAddress);
        $this->assertEquals($this->mailSenderName, $whitelable->mailSenderName);
    }
    public function test_consturct_invalidUrlFormat_badRequest()
    {
        $this->url = 'bad url format';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: invalid firm whitelable url format';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_invalidMailSenderAddressFormat_badRequest()
    {
        $this->mailSenderAddress = 'invalid mail format';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: invalid firm whitelable mail sender address format';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_emptyMailSenderName_badRequest()
    {
        $this->mailSenderName = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: firm whitelable mail sender name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
}

class TestableFirmWhitelableInfo extends FirmWhitelableInfo
{
    public $url;
    public $mailSenderAddress;
    public $mailSenderName;
}
