<?php

namespace Participant\Domain\Service;

use Participant\Domain\ {
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValueData,
    SharedModel\FileInfo
};
use Tests\TestBase;

class MetricAssignmentReportDataProviderTest extends TestBase
{
    protected $fileInfoRepository;
    protected $dataProvider;
    protected $assignmentFieldValueData;
    protected $assignmentFieldId = "assignmentFieldId", $value = "99.99", $note = "note", $fileInfoId = "fileInfoId";
    protected $fileInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = $this->buildMockOfInterface(LocalFileInfoRepository::class);
        $this->dataProvider = new TestableMetricAssignmentReportDataProvider($this->fileInfoRepository);
        $this->assignmentFieldValueData = $this->buildMockOfClass(AssignmentFieldValueData::class);
        $this->dataProvider->assignmentFieldValueDataCollection[$this->assignmentFieldId] = $this->assignmentFieldValueData;
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoRepository->expects($this->any())
                ->method("ofId")
                ->with($this->fileInfoId)
                ->willReturn($this->fileInfo);
    }
    
    protected function executeAddAssignmentFieldValueData()
    {
        $this->dataProvider->addAssignmentFieldValueData(
                $this->assignmentFieldId, $this->value, $this->note, $this->fileInfoId);
    }
    public function test_addAssignmentFieldValueData_addAssignmentFieldValueDataToCollection()
    {
        $this->executeAddAssignmentFieldValueData();
        $data = new AssignmentFieldValueData($this->value, $this->note, $this->fileInfo);
        $this->assertEquals($data, $this->dataProvider->assignmentFieldValueDataCollection[$this->assignmentFieldId]);
    }
    public function test_addAssignmentFieldValueData_emptyFileInfoId_setFileInfoNull()
    {
        $this->fileInfoId = null;
        $this->executeAddAssignmentFieldValueData();
        $data = new AssignmentFieldValueData($this->value, $this->note, null);
        $this->assertEquals($data, $this->dataProvider->assignmentFieldValueDataCollection[$this->assignmentFieldId]);
    }
    
    protected function executeGetAssignmentFieldValueDataCorrespondWithAssignmentField()
    {
        return $this->dataProvider->getAssignmentFieldValueDataCorrespondWithAssignmentField($this->assignmentFieldId);
    }
    public function test_getAssignmentFieldValueDataCorrespondWithAssignmentField_returnAssignmentFieldValueDataCorrespondWithFieldId()
    {
        $this->assertEquals($this->assignmentFieldValueData, $this->executeGetAssignmentFieldValueDataCorrespondWithAssignmentField());
    }
    public function test_getAssignmentFieldValueDataCorrespondWithAssignmentField_noDataInCollectionCorrespondWithField_returnNull()
    {
        $this->assignmentFieldId = "nonExist";
        $this->assertNull($this->executeGetAssignmentFieldValueDataCorrespondWithAssignmentField());
    }
    
    protected function executeIterateAllAttachedFileInfo()
    {
        $this->assignmentFieldValueData->expects($this->any())
                ->method("getAttachedFileInfo")
                ->willReturn($this->fileInfo);
        return $this->dataProvider->iterateAllAttachedFileInfo();
    }
    public function test_iterateAllAttachedFileInfo_returnAllAttachedFileInfoInCollectionAssignmentFieldValueData()
    {
        $this->assignmentFieldValueData->expects($this->once())
                ->method("getAttachedFileInfo")
                ->willReturn($this->fileInfo);
        $this->assertEquals([$this->fileInfo], $this->executeIterateAllAttachedFileInfo());
    }
    public function test_iterateAllAttachedFileInfo_containAssignmentFieldValueDataWithNullFileInfo_ignoreAddingThisNullFileInfo()
    {
        $this->dataProvider->assignmentFieldValueDataCollection["otherFieldId"] = $this->assignmentFieldValueData;
        $this->assignmentFieldValueData->expects($this->at(0))
                ->method("getAttachedFileInfo")
                ->willReturn(null);
        $this->assertEquals([$this->fileInfo], $this->executeIterateAllAttachedFileInfo());
    }
}

class TestableMetricAssignmentReportDataProvider extends MetricAssignmentReportDataProvider
{
    public $fileInfoRepository;
    public $assignmentFieldValueDataCollection;
}
