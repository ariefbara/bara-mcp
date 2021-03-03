<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use DateTimeImmutable;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Resources\Domain\Data\DataCollection;
use Tests\TestBase;

class ObjectiveProgressReportDataTest extends TestBase
{
    protected $reportDate;
    protected $objectiveProgressReportData;
    protected $dataCollection;
    protected $keyResultProgressReportData;
    protected $keyResultId = 'keyResultId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->reportDate = new DateTimeImmutable('-5 days');
        $this->objectiveProgressReportData = new TestableObjectiveProgressReportData($this->reportDate);
        $this->dataCollection = $this->buildMockOfClass(DataCollection::class);
        $this->objectiveProgressReportData->keyResultProgressReportDataCollection = $this->dataCollection;
        
        $this->keyResultProgressReportData = $this->buildMockOfClass(KeyResultProgressReportData::class);
    }
    
    public function test_construct_setProperties()
    {
        $objectiveProgressData = new TestableObjectiveProgressReportData($this->reportDate);
        $this->assertEquals($this->reportDate, $objectiveProgressData->reportDate);
        $this->assertEquals(new DataCollection(), $objectiveProgressData->keyResultProgressReportDataCollection);
    }
    
    public function test_addKeyResultProgressData_pushKeyResultProgressDataToCollection()
    {
        $this->dataCollection->expects($this->once())
                ->method('push')
                ->with($this->keyResultProgressReportData, $this->keyResultId);
        $this->objectiveProgressReportData->addKeyResultProgressReportData($this->keyResultProgressReportData, $this->keyResultId);
    }
    
    public function test_pullKeyResultProgressData_returnDataCollectionPullResult()
    {
        $this->dataCollection->expects($this->once())
                ->method('pull')
                ->with($this->keyResultId);
        $this->objectiveProgressReportData->pullKeyResultProgressReportData($this->keyResultId);
    }
    public function test_getKeyResultProgressDataIterator_returnDataCollection()
    {
        $this->assertEquals($this->dataCollection, $this->objectiveProgressReportData->getKeyResultProgressReportDataIterator());
    }
}

class TestableObjectiveProgressReportData extends ObjectiveProgressReportData
{
    public $reportDate;
    public $keyResultProgressReportDataCollection;
}
