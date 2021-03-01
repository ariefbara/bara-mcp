<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Resources\Domain\Data\DataCollection;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class OKRPeriodDataTest extends TestBase
{
    protected $okrPeriodData;
    protected $labelData;
    protected $startDate;
    protected $endDate;
    protected $objectiveDataCollection;
    protected $objectiveData, $objectiveId = 'objectiveId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->labelData = $this->buildMockOfClass(LabelData::class);
        $this->startDate = new DateTimeImmutable('+1 days');
        $this->endDate = new DateTimeImmutable('+10 days');
        $this->okrPeriodData = new TestableOKRPeriodData($this->labelData, $this->startDate, $this->endDate);
        $this->objectiveDataCollection = $this->buildMockOfClass(DataCollection::class);
        $this->okrPeriodData->objectiveDataCollection = $this->objectiveDataCollection;
        
        $this->objectiveData = $this->buildMockOfClass(ObjectiveData::class);
    }
    
    public function test_construct_setProperties()
    {
        $okrPeriodData = new TestableOKRPeriodData($this->labelData, $this->startDate, $this->endDate);
        $this->assertEquals($this->labelData, $okrPeriodData->labelData);
        $this->assertEquals($this->startDate, $okrPeriodData->startDate);
        $this->assertEquals($this->endDate, $okrPeriodData->endDate);
        $objectiveDataCollection = new DataCollection();
        $this->assertEquals($objectiveDataCollection, $okrPeriodData->objectiveDataCollection);
    }
    
    public function test_addObjectiveData_pushObjectiveDataToCollection()
    {
        $this->objectiveDataCollection->expects($this->once())
                ->method('push')
                ->with($this->objectiveData, $this->objectiveId);
        $this->okrPeriodData->addObjectiveData($this->objectiveData, $this->objectiveId);
    }
    public function test_pullObjectiveDataWithId_returnObjectiveDataCollectionPullResult()
    {
        $this->objectiveDataCollection->expects($this->once())
                ->method('pull')
                ->with($this->objectiveId);
        $this->okrPeriodData->pullObjectiveDataWithId($this->objectiveId);
    }
    public function test_getObjectiveDataCollectionIterator_returnObjectiveDataCollection()
    {
        $this->assertEquals($this->objectiveDataCollection, $this->okrPeriodData->getObjectiveDataCollectionIterator());
    }
}

class TestableOKRPeriodData extends OKRPeriodData
{
    public $labelData;
    public $startDate;
    public $endDate;
    public $objectiveDataCollection;
}
