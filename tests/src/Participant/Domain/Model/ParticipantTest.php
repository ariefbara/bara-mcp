<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\CompletedMission,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\ViewLearningMaterialActivityLog,
    Model\Participant\Worksheet
};
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ParticipantTest extends TestBase
{

    protected $participant;
    protected $program;
    protected $consultationRequest;
    protected $consultationSession;
    protected $teamProgramParticipation;

    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $startTime;
    protected $consultationSessionId = 'consultationSessionId';
    protected $otherConsultationRequest;
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecordData;
    protected $parentWorksheet;
    protected $completedMission;
    protected $team;
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->participant->active = true;
        $this->participant->note = null;
        $this->participant->consultationRequests = new ArrayCollection();
        $this->participant->consultationSessions = new ArrayCollection();
        $this->participant->completedMissions = new ArrayCollection();
        
        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->participant->teamProgramParticipation = $this->teamProgramParticipation;
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant->program = $this->program;

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequest->expects($this->any())->method('getId')->willReturn($this->consultationRequestId);
        $this->otherConsultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->participant->consultationRequests->add($this->consultationRequest);
        $this->participant->consultationRequests->add($this->otherConsultationRequest);
        
        $this->completedMission = $this->buildMockOfClass(CompletedMission::class);
        $this->participant->completedMissions->add($this->completedMission);

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->participant->consultationSessions->add($this->consultationSession);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->startTime = new DateTimeImmutable();

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->any())->method("isRootMission")->willReturn(true);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->parentWorksheet = $this->buildMockOfClass(Worksheet::class);
        
        $this->team = $this->buildMockOfClass(Team::class);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    protected function assertOperationCauseInactiveParticipantForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active program participant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeBelongsToTeam()
    {
        return $this->participant->belongsToTeam($this->team);
    }
    public function test_belongsToTeam_returnTeamProgramParticipationsTeamEqualsResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(true);
        $this->assertTrue($this->executeBelongsToTeam());
    }
    public function test_belongsToTeam_notATeamProgramParticipation_returnFalse()
    {
        $this->participant->teamProgramParticipation = null;
        $this->assertFalse($this->executeBelongsToTeam());
    }

    protected function executeQuit()
    {
        $this->participant->quit();
    }
    public function test_quit_setActiveFalseAndNoteQuit()
    {
        $this->executeQuit();
        $this->assertFalse($this->participant->active);
        $this->assertEquals('quit', $this->participant->note);
    }
    public function test_quit_alreadyInactive_forbiddenError()
    {
        $this->participant->active = false;

        $operation = function () {
            $this->executeQuit();
        };
        $errorDetail = 'forbidden: participant already inactive';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    protected function executeSubmitConsultationRequest()
    {
        $this->consultationSetup->expects($this->any())
                ->method('programEquals')
                ->willReturn(true);
        $this->consultant->expects($this->any())
                ->method('programEquals')
                ->willReturn(true);
        $this->consultationRequest->expects($this->any())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(false);
        return $this->participant->submitConsultationRequest(
                        $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime);
    }
    public function test_submitConsultationRequest_returnConsultationRequest()
    {
        $this->assertInstanceOf(ConsultationRequest::class, $this->executeSubmitConsultationRequest());
    }
    public function test_submitConsultationRequest_consultationSetupFromDifferentProgram_forbiddenError()
    {
        $this->consultationSetup->expects($this->once())
                ->method('programEquals')
                ->with($this->participant->program)
                ->willReturn(false);
        $operation = function () {
            $this->executeSubmitConsultationRequest();
        };
        $errorDetail = 'forbidden: consultation setup from different program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_submitConsultationRequest_consultantFromDifferentProgram_forbiddenError()
    {
        $this->consultant->expects($this->once())
                ->method('programEquals')
                ->with($this->participant->program)
                ->willReturn(false);
        $operation = function () {
            $this->executeSubmitConsultationRequest();
        };
        $errorDetail = 'forbidden: consultant from different program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_submitConsultationRequest_containProposedConsultationRequestInConflictWithNewConsultationRequestSchedule_conflictError()
    {
        $this->consultationRequest->expects($this->once())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(true);
        $operation = function () {
            $this->executeSubmitConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_submitConsultationRequest_containConsultationSessionConflictedWithNewConsultationRequest_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeSubmitConsultationRequest();
        };

        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    protected function executeChangeConsultationRequestTime()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(false);
        $this->participant->changeConsultationRequestTime($this->consultationRequestId, $this->startTime);
    }
    public function test_changeConsultationRequestTime_changeConsultationRequestTime()
    {
        $this->consultationRequest->expects($this->once())
                ->method('rePropose')
                ->with($this->startTime);
        $this->executeChangeConsultationRequestTime();
    }
    public function test_changeConsultationRequestTime_containOtherConsultationRequestConflictedWithReProposedSchedule_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('isProposedConsultationRequestConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeChangeConsultationRequestTime();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_changeConsultationRequestTime_containConsultationRequestConflictedWithReProposedSchedule_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeChangeConsultationRequestTime();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_changeConsultationRequestTime_consultationRequestNotFound_throwEx()
    {
        $operation = function () {
            $this->participant->changeConsultationRequestTime('non-existing-schedule', $this->startTime);
        };
        $errorDetail = "not found: consultation request not found";
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_changeConsultationRequestTime_fromTeamParticipant_includeTeamMemberConsultationRequestArgument()
    {
        $this->consultationRequest->expects($this->once())
                ->method('rePropose')
                ->with($this->startTime, $this->teamMember);
        $this->participant->changeConsultationRequestTime($this->consultationRequestId, $this->startTime, $this->teamMember);
    }
    public function test_changeConsultationRequestTime_pullConsultationRequestEventAndRecordIt()
    {
        $this->consultationRequest->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn($events = [$this->buildMockOfClass(CommonEvent::class)]);
        $this->executeChangeConsultationRequestTime();
        $this->assertEquals($events, $this->participant->recordedEvents);
    }

    protected function executeAcceptOfferedConsultationRequest()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(false);
        $this->participant->acceptOfferedConsultationRequest($this->consultationRequestId, $this->consultationSessionId, $this->teamMember);
    }
    public function test_acceptOfferedConsultationRequest_acceptConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('accept');
        $this->executeAcceptOfferedConsultationRequest();
    }
    public function test_acceptOfferedConsultationRequest_fromTeamParticipant_includeTeamMemberInAcceptConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('accept');
        $this->participant->acceptOfferedConsultationRequest(
                $this->consultationRequestId, $this->consultationSessionId, $this->teamMember);
    }
    public function test_acceptOfferedConsultationRequest_containOtherConsultationRequestConflictedWithThisSchedule_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('isProposedConsultationRequestConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptOfferedConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_acceptOfferedConsultationRequest_containConsultationSessionConflictedWithThisSchedule_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptOfferedConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_acceptOfferedConsultationRequest_addConsultationSessionFromConsultationRequestAndAddToCollection()
    {
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationSession');
        $this->executeAcceptOfferedConsultationRequest();

        $this->assertEquals(2, $this->participant->consultationSessions->count());
    }
    public function test_acceptOfferedConsultationRequest_pullAndRecordConsultationSessionRecordedEvents()
    {
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationSession')
                ->willReturn($this->consultationSession);
        $this->consultationSession->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn($events = [$this->buildMockOfClass(CommonEvent::class)]);
        $this->executeAcceptOfferedConsultationRequest();
        $this->assertEquals($events, $this->participant->recordedEvents);
    }
    
    protected function executeCreateRootWorksheet()
    {
        $this->mission->expects($this->any())
                ->method("programEquals")
                ->willReturn(true);
        return $this->participant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, null);
    }
    public function test_createRootWorksheet_returnRootWorksheet()
    {
        $this->assertInstanceOf(Worksheet::class, $this->executeCreateRootWorksheet());
    }
    public function test_createRootWorksheet_missionsProgramDifferentFromParticipantsProgram_forbiddenError()
    {
        $this->mission->expects($this->once())
                ->method("programEquals")
                ->with($this->participant->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeCreateRootWorksheet();
        };
        $errorDetail = "forbidden: can only access mission in same program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_createRootWorksheet_inactiveParticipant_forbiddenError()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeCreateRootWorksheet();
        };
        $this->assertOperationCauseInactiveParticipantForbiddenError($operation);
    }
    public function test_createRootWorksheet_addCompletedMission()
    {
        $this->executeCreateRootWorksheet();
        $this->assertEquals(2, $this->participant->completedMissions->count());
        $this->assertInstanceOf(CompletedMission::class, $this->participant->completedMissions->last());
    }
    public function test_createWorksheet_aCompletedMissionCorrespondToSameMissionAlreadyExistInCollection_preventAddNewCompletedMission()
    {
        $this->completedMission->expects($this->once())
                ->method("correspondWithMission")
                ->with($this->mission)
                ->willReturn(true);
        $this->executeCreateRootWorksheet();
        $this->assertEquals(1, $this->participant->completedMissions->count());
    }
    
    protected function executeSubmitBranchWorksheet()
    {
        $this->parentWorksheet->expects($this->any())
                ->method("belongsToParticipant")
                ->willReturn(true);
        return $this->participant->submitBranchWorksheet(
                $this->parentWorksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, 
                $this->teamMember);
    }
    public function test_submitBranchWorksheet_returnBranchWorksheetCreatedByParentWorksheet()
    {
        $this->parentWorksheet->expects($this->once())
                ->method("createBranchWorksheet")
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, $this->teamMember)
                ->willReturn($branch = $this->buildMockOfClass(Worksheet::class));
        $this->assertEquals($branch, $this->executeSubmitBranchWorksheet());
    }
    public function test_submitBranchWorksheet_parentWorkshseetsDoesntBelongsToParticipant_forbiddenError()
    {
        $this->parentWorksheet->expects($this->once())
                ->method("belongsToParticipant")
                ->with($this->participant)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitBranchWorksheet();
        };
        $errorDetail = "forbidden: can manage asset belongs to other participant";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_submitBranchWorksheet_addCompletedMissionToCollection()
    {
        $this->executeSubmitBranchWorksheet();
        $this->assertEquals(2, $this->participant->completedMissions->count());
        $this->assertInstanceOf(CompletedMission::class, $this->participant->completedMissions->last());
    }
    
    protected function executeIsActiveParticipantOfProgram()
    {
        return $this->participant->isActiveParticipantOfProgram($this->program);
    }
    public function test_isActiveParticipantOfProgram_anActiveParticicpantOfSameProgram_returnTrue()
    {
        $this->assertTrue($this->participant->isActiveParticipantOfProgram($this->program));
    }
    public function test_isActiveParticipantOfProgram_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->participant->isActiveParticipantOfProgram($this->program));
    }
    public function test_isActiveParticipantOfProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->participant->isActiveParticipantOfProgram($program));
    }
    
    public function test_logViewLearningMaterialActivity_returnViewLearningMaterialActivityLog()
    {
        $log = new ViewLearningMaterialActivityLog(
                $this->participant, $logId = "logId", $learningMaterialId = "learningMaterialId", $this->teamMember);
        $this->assertEquals($log, $this->participant->logViewLearningMaterialActivity($logId, $learningMaterialId, $this->teamMember));
    }
    
}

class TestableParticipant extends Participant
{
    public $recordedEvents;
    public $program;
    public $id;
    public $active = true;
    public $note;
    public $consultationRequests;
    public $consultationSessions;
    public $teamProgramParticipation;
    public $completedMissions;

    function __construct()
    {
        ;
    }

}
