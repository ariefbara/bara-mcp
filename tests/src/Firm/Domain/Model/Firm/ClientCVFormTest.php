<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Tests\TestBase;

class ClientCVFormTest extends TestBase
{
    protected $firm;
    protected $profileForm;
    protected $clientCVForm;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        
        $this->clientCVForm = new TestableClientCVForm($this->firm, "id", $this->profileForm);
    }
    
    public function test_construct_setProperties()
    {
        $clientCVForm = new TestableClientCVForm($this->firm, $this->id, $this->profileForm);
        $this->assertEquals($this->firm, $clientCVForm->firm);
        $this->assertEquals($this->id, $clientCVForm->id);
        $this->assertEquals($this->profileForm, $clientCVForm->profileForm);
        $this->assertFalse($clientCVForm->disabled);
    }
    
    public function test_disable_setDisabledTrue()
    {
        $this->clientCVForm->disable();
        $this->assertTrue($this->clientCVForm->disabled);
    }
    public function test_disable_alreadyDisalbed_forbidden()
    {
        $this->clientCVForm->disabled = true;
        $operation = function (){
            $this->clientCVForm->disable();
        };
        $errorDetail = "forbidden: client cv form already disable";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_enable_setDisabledFalse()
    {
        $this->clientCVForm->disabled = true;
        $this->clientCVForm->enable();
        $this->assertFalse($this->clientCVForm->disabled);
    }
    public function test_enable_alreadyEnabled_forbidden()
    {
        $operation = function (){
            $this->clientCVForm->enable();
        };
        $errorDetail = "forbidden: client cv form already enabled";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->clientCVForm->belongsToFirm($this->clientCVForm->firm));
    }
    public function test_belongsToFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->clientCVForm->belongsToFirm($firm));
    }
    
    public function test_correspondWithProfileForm_sameProfileForm_returnTrue()
    {
        $this->assertTrue($this->clientCVForm->correspondWithProfileForm($this->profileForm));
    }
    public function test_correspondWithProfileForm_differentProfileForm_returnFalse()
    {
        $profileForm = $this->buildMockOfClass(ProfileForm::class);
        $this->assertFalse($this->clientCVForm->correspondWithProfileForm($profileForm));
    }
}

class TestableClientCVForm extends ClientCVForm
{
    public $firm;
    public $id;
    public $profileForm;
    public $disabled;
}
