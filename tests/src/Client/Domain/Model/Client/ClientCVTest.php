<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\ClientCVForm;
use Client\Domain\Model\Client;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ClientCVTest extends TestBase
{
    protected $client;
    protected $clientCVForm;
    protected $formRecord;
    protected $clientCV;
    protected $id = "newId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientCVForm = $this->buildMockOfClass(ClientCVForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->clientCV = new TestableClientCV($this->client, "id", $this->clientCVForm, $this->formRecordData);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->clientCV->formRecord = $this->formRecord;
    }
    
    protected function executeConstruct()
    {
        return new TestableClientCV($this->client, $this->id, $this->clientCVForm, $this->formRecordData);
    }
    public function test_construct_setProperties()
    {
        $this->clientCVForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->id, $this->formRecordData);
        $clientCV = $this->executeConstruct();
        $this->assertEquals($this->client, $clientCV->client);
        $this->assertEquals($this->id, $clientCV->id);
        $this->assertEquals($this->clientCVForm, $clientCV->clientCVForm);
        $this->assertInstanceOf(FormRecord::class, $clientCV->formRecord);
        $this->assertFalse($clientCV->removed);
    }
    
    protected function executeUpdate()
    {
        $this->clientCV->update($this->formRecordData);
    }
    public function test_update_updateFormRecord()
    {
        $this->formRecord->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        $this->executeUpdate();
    }
    public function test_update_alreadyRemoved_forbidden()
    {
        $this->clientCV->removed = true;
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "forbidden: CV already removed";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeRemove()
    {
        $this->clientCV->remove();
    }
    public function test_remove_setRemovedTrue()
    {
        $this->executeRemove();
        $this->assertTrue($this->clientCV->removed);
    }
    public function test_remove_alreadyRemoved_forbidden()
    {
        $this->clientCV->removed = true;
        $operation = function (){
            $this->executeRemove();
        };
        $errorDetail = "forbidden: CV already removed";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeIsActiveCVCorrespondWithClientCVForm()
    {
        return $this->clientCV->isActiveCVCorrespondWithClientCVForm($this->clientCVForm);
    }
    public function test_isActiveCVCorrespondWithClientCVForm_nonRemovedCVCorrespondToSameForm_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveCVCorrespondWithClientCVForm());
    }
    public function test_isActiveCVCorrespondWithClientCVForm_alreadyRemoved_returnFalse()
    {
        $this->clientCV->removed = true;
        $this->assertFalse($this->executeIsActiveCVCorrespondWithClientCVForm());
    }
    public function test_isActiveCVCorrespondWithClientCVForm_differentForm_returnFalse()
    {
        $this->clientCVForm = $this->buildMockOfClass(ClientCVForm::class);
        $this->assertFalse($this->executeIsActiveCVCorrespondWithClientCVForm());
    }
    
    protected function executeBelongsToClient()
    {
        return $this->clientCV->belongsToClient($this->client);
    }
    public function test_belongsToClient_sameClient_returnTrue()
    {
        $this->assertTrue($this->executeBelongsToClient());
    }
    public function test_belongsToClient_differentClient_returnFalse()
    {
        $this->client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->executeBelongsToClient());
    }
}

class TestableClientCV extends ClientCV
{
    public $client;
    public $id;
    public $clientCVForm;
    public $formRecord;
    public $removed;
}
