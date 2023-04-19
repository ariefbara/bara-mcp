<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Participant\Domain\Service\FileInfoRepository;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\src\Participant\Application\Service\Client\AsTeamMember\ObjectiveProgressReportBaseTest;

class UpdateObjectiveProgressReportTest extends ObjectiveProgressReportBaseTest
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
                ->method('fileInfoOfTeam')
                ->with($this->teamId, $this->fileInfoId)
                ->willReturn($this->fileInfo);
        //
        $this->service = new UpdateObjectiveProgressReport(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->objectiveProgressReportRepository, $this->fileInfoRepository);
        //
        $this->keyResultProgressReportData = (new TestableKeyResultProgressReportData(999))
                ->addFileInfoIdAsAttachment($this->fileInfoId);
        $this->objectiveProgressReportData->addKeyResultProgressReportData($this->keyResultProgressReportData, $this->keyResultId);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_execute_teamMemberUpdateObjectiveProgressReport()
    {
        $this->teamMember->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->teamParticipant, $this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->execute();
    }
    public function test_execute_modifyPayload()
    {
        $this->execute();
        $this->assertEquals([$this->fileInfo], $this->keyResultProgressReportData->attachments);
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
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
