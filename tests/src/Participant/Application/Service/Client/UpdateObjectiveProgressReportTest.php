<?php

namespace Participant\Application\Service\Client;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Participant\Domain\Service\FileInfoRepository;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\src\Participant\Application\Service\Client\ObjectiveProgressReportTestBase;

class UpdateObjectiveProgressReportTest extends ObjectiveProgressReportTestBase
{
    protected $fileInfoRepository, $fileInfo, $fileInfoId = 'fileInfoId';
    protected $service;
    //
    protected $keyResultProgressReportData, $keyResultId = 'keyResultId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->fileInfoRepository->expects($this->any())
                ->method('fileInfoOfClient')
                ->with($this->firmId, $this->clientId, $this->fileInfoId)
                ->willReturn($this->fileInfo);
        
        $this->service = new UpdateObjectiveProgressReport(
                $this->clientParticipantRepository, $this->objectiveProgressReportRepository, $this->fileInfoRepository);
        
        $this->keyResultProgressReportData = (new TestableKeyResultProgressReportData(999))
                ->addFileInfoIdAsAttachment($this->fileInfoId);
        $this->objectiveProgressReportData->addKeyResultProgressReportData($this->keyResultProgressReportData, $this->keyResultId);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->participantId, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_execute_clientParticipantUpdateObjectiveProgressReport()
    {
        $this->clientParticipant->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->execute();
    }
    public function test_update_modifyPayload()
    {
        $this->execute();
        $this->assertEquals([$this->fileInfo], $this->keyResultProgressReportData->attachments);
    }
    public function test_update_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}

class TestableKeyResultProgressReportData extends KeyResultProgressReportData
{
    public $value;
    public $attachments = [];
    public $fileInfoIdListOfAttachment = [];
}
