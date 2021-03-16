<?php

namespace Participant\Domain\Model\Participant\OKRPeriod;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Resources\Domain\Data\DataCollection;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class ObjectiveDataTest extends TestBase
{
    protected $labelData;
    protected $weight = 500;
    protected $objectiveData;
    protected $keyResultDataCollection;
    protected $keyResultData, $keyResultId = 'keyResultId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->labelData = $this->buildMockOfClass(LabelData::class);
        $this->objectiveData = new TestableObjectiveData($this->labelData, $this->weight);
        $this->keyResultDataCollection = $this->buildMockOfClass(DataCollection::class);
        $this->objectiveData->keyResultDataCollection = $this->keyResultDataCollection;
        
        $this->keyResultData = $this->buildMockOfClass(KeyResultData::class);
    }
    
    public function test_construct_setProperties()
    {
        $objectiveData = new TestableObjectiveData($this->labelData, $this->weight);
        $this->assertEquals($this->labelData, $objectiveData->labelData);
        $this->assertEquals($this->weight, $objectiveData->weight);
        $this->assertEquals(new DataCollection(), $objectiveData->keyResultDataCollection);
    }
    
    public function test_addKeyResultData_pushKeyResultDataToCollection()
    {
        $this->keyResultDataCollection->expects($this->once())
                ->method('push')
                ->with($this->keyResultData, $this->keyResultId);
        $this->objectiveData->addKeyResultData($this->keyResultData, $this->keyResultId);
    }
    public function test_pullKeyResultData_returnDataCollectionPullResult()
    {
        $this->keyResultDataCollection->expects($this->once())
                ->method('pull')
                ->with($this->keyResultId);
        $this->objectiveData->pullKeyResultData($this->keyResultId);
    }
    public function test_getKeyResultDataCollectionIterator_returnDataCollection()
    {
        $this->assertEquals($this->keyResultDataCollection, $this->objectiveData->getKeyResultDataIterator());
    }
}

class TestableObjectiveData extends ObjectiveData
{
    public $labelData;
    public $weight;
    public $keyResultDataCollection;
}
