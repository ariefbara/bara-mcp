<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Participant\Domain\Service\FileInfoRepository;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use Tests\src\Participant\Application\Service\Client\AsTeamMember\ObjectiveProgressReportBaseTest;

class SubmitObjectiveProgressReportTest extends ObjectiveProgressReportBaseTest
{

    protected $objectiveRepository;
    protected $objective;
    protected $objectiveId = 'objectiveId';
    protected $fileInfoRepository, $fileInfo, $fileInfoId = 'fileInfoId';
    protected $service;
    //
    protected $keyResultProgressReportData, $keyResultId = 'keyResultId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveRepository = $this->buildMockOfInterface(ObjectiveRepository::class);
        $this->objectiveRepository->expects($this->any())
                ->method('ofId')
                ->with($this->objectiveId)
                ->willReturn($this->objective);
        
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->fileInfoRepository = $this->buildMockOfInterface(FileInfoRepository::class);
        $this->fileInfoRepository->expects($this->any())
                ->method('fileInfoOfTeam')
                ->with($this->teamId, $this->fileInfoId)
                ->willReturn($this->fileInfo);

        $this->service = new SubmitObjectiveProgressReport(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->objectiveRepository,
                $this->objectiveProgressReportRepository, $this->fileInfoRepository);
        
        $this->keyResultProgressReportData = (new TestableKeyResultProgressReportData(999))
                ->addFileInfoIdAsAttachment($this->fileInfoId);
        $this->objectiveProgressReportData->addKeyResultProgressReportData($this->keyResultProgressReportData, $this->keyResultId);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->objectiveId,
                        $this->objectiveProgressReportData);
    }
    public function test_execute_addObjectiveProgressReportSubmittedByTeamMemberToRepository()
    {
        $this->teamMember->expects($this->once())
                ->method('submitObjectiveProgressReport')
                ->with($this->teamParticipant, $this->objective, $this->nextObjectiveProgressReportId, $this->objectiveProgressReportData);
        $this->objectiveProgressReportRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_modifyPayload()
    {
        $this->execute();
        $this->assertEquals([$this->fileInfo], $this->keyResultProgressReportData->attachments);
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextObjectiveProgressReportId, $this->execute());
    }

}

class TestableKeyResultProgressReportData extends KeyResultProgressReportData
{
    public $value;
    public $attachments = [];
    public $fileInfoIdListOfAttachment = [];
}
