<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Model\Participant\Task\TaskReportData;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;
use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class SubmitTaskReportTest extends ParticipantTaskTestBase
{
    protected $participantFileInfoRepository;
    protected $participantFileInfoOne, $participantFileInfoOneId = 'participantFileInfoOneId';
    protected $participantFileInfoTwo, $participantFileInfoTwoId = 'participantFileInfoTwoId';
    
    protected $submitTaskReport;
    //
    protected $payload, $content = 'report content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTaskDependency();
        
        $this->participantFileInfoRepository = $this->buildMockOfInterface(ParticipantFileInfoRepository::class);
        $this->participantFileInfoOne = $this->buildMockOfClass(ParticipantFileInfo::class);
        $this->participantFileInfoTwo = $this->buildMockOfClass(ParticipantFileInfo::class);
        
        $this->submitTaskReport = new SubmitTaskReport($this->taskRepository, $this->participantFileInfoRepository);
        
        $this->payload = new SubmitTaskReportPayload($this->taskId, $this->content);
        $this->payload->attachParticipantFileInfoId($this->participantFileInfoOneId);
        $this->payload->attachParticipantFileInfoId($this->participantFileInfoTwoId);
    }
    
    protected function execute()
    {
        $this->participantFileInfoRepository->expects($this->exactly(2))
                ->method('ofId')
                ->withConsecutive([$this->participantFileInfoOneId], [$this->participantFileInfoTwoId])
                ->willReturnOnConsecutiveCalls($this->participantFileInfoOne, $this->participantFileInfoTwo);
        
        $this->submitTaskReport->execute($this->participant, $this->payload);
    }
    public function test_execute_submitTaskReport()
    {
        $this->task->expects($this->once())
                ->method('submitReport');
        $this->execute();
    }
    protected function assertParticipantFileInfoUsableByParticipant()
    {
        $this->participantFileInfoOne->expects($this->once())
                ->method('assertUsableByParticipant')
                ->with($this->participant);
        
        $this->participantFileInfoTwo->expects($this->once())
                ->method('assertUsableByParticipant')
                ->with($this->participant);
        
        $this->execute();
    }
    public function test_execute_assertTaskManageableByParticipant()
    {
        $this->task->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
