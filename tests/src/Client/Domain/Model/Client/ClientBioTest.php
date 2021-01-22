<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\BioForm;
use Client\Domain\Model\Client;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ClientBioTest extends TestBase
{
    protected $client;
    protected $bioForm;
    protected $formRecord;
    protected $clientBio;
    protected $id = "newId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->bioForm = $this->buildMockOfClass(BioForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->clientBio = new TestableClientBio($this->client, "id", $this->bioForm, $this->formRecordData);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->clientBio->formRecord = $this->formRecord;
    }
    
    protected function executeConstruct()
    {
        return new TestableClientBio($this->client, $this->id, $this->bioForm, $this->formRecordData);
    }
    public function test_construct_setProperties()
    {
        $this->bioForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->id, $this->formRecordData);
        $clientBio = $this->executeConstruct();
        $this->assertEquals($this->client, $clientBio->client);
        $this->assertEquals($this->bioForm, $clientBio->bioForm);
        $this->assertInstanceOf(FormRecord::class, $clientBio->formRecord);
        $this->assertFalse($clientBio->removed);
    }
    
    protected function executeUpdate()
    {
        $this->clientBio->update($this->formRecordData);
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
        $this->clientBio->removed = true;
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "forbidden: bio already removed";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeRemove()
    {
        $this->clientBio->remove();
    }
    public function test_remove_setRemovedTrue()
    {
        $this->executeRemove();
        $this->assertTrue($this->clientBio->removed);
    }
    public function test_remove_alreadyRemoved_forbidden()
    {
        $this->clientBio->removed = true;
        $operation = function (){
            $this->executeRemove();
        };
        $errorDetail = "forbidden: bio already removed";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeIsActiveBioCorrespondWithForm()
    {
        return $this->clientBio->isActiveBioCorrespondWithForm($this->bioForm);
    }
    public function test_isActiveBioCorrespondWithForm_nonRemovedBioCorrespondToSameForm_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveBioCorrespondWithForm());
    }
    public function test_isActiveBioCorrespondWithClientBioForm_alreadyRemoved_returnFalse()
    {
        $this->clientBio->removed = true;
        $this->assertFalse($this->executeIsActiveBioCorrespondWithForm());
    }
    public function test_isActiveBioCorrespondWithClientBioForm_differentForm_returnFalse()
    {
        $this->bioForm = $this->buildMockOfClass(BioForm::class);
        $this->assertFalse($this->executeIsActiveBioCorrespondWithForm());
    }
    
    protected function executeBelongsToClient()
    {
        return $this->clientBio->belongsToClient($this->client);
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

class TestableClientBio extends ClientBio
{
    public $client;
    public $bioForm;
    public $formRecord;
    public $removed;
}
