<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ParticipantTest extends TestBase
{

    protected $participant;
    protected $program;
    protected $consultationRequest;
    protected $consultationSession;
    
    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $startTime;
    protected $consultationSessionId = 'consultationSessionId';
    protected $otherConsultationRequest;
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecordData;
    protected $parentWorksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->participant->active = true;
        $this->participant->note = null;
        $this->participant->consultationRequests = new ArrayCollection();
        $this->participant->consultationSessions = new ArrayCollection();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant->program = $this->program;

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequest->expects($this->any())->method('getId')->willReturn($this->consultationRequestId);
        $this->otherConsultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->participant->consultationRequests->add($this->consultationRequest);
        $this->participant->consultationRequests->add($this->otherConsultationRequest);

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->participant->consultationSessions->add($this->consultationSession);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->startTime = new DateTimeImmutable();

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->any())->method("isRootMission")->willReturn(true);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->parentWorksheet = $this->buildMockOfClass(Worksheet::class);
    }
    protected function assertOperationCauseInactiveParticipantForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active program participant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertOperationCauseAssetNotOwnedForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: unable to manage asset of other participant";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
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

    protected function executeProposeConsultation()
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
        return $this->participant->proposeConsultation(
                        $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime);
    }
    public function test_proposeConsultation_returnConsultationRequest()
    {
        $consultationRequest = new ConsultationRequest(
                $this->participant, $this->consultationRequestId, $this->consultationSetup, $this->consultant,
                $this->startTime);

        $this->assertEquals($consultationRequest, $this->executeProposeConsultation());
    }
    public function test_proposeConsultation_consultationSetupFromDifferentProgram_forbiddenError()
    {
        $this->consultationSetup->expects($this->once())
                ->method('programEquals')
                ->with($this->participant->program)
                ->willReturn(false);
        $operation = function () {
            $this->executeProposeConsultation();
        };
        $errorDetail = 'forbidden: consultation setup from different program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_proposeConsultation_consultantFromDifferentProgram_forbiddenError()
    {
        $this->consultant->expects($this->once())
                ->method('programEquals')
                ->with($this->participant->program)
                ->willReturn(false);
        $operation = function () {
            $this->executeProposeConsultation();
        };
        $errorDetail = 'forbidden: consultant from different program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_proposeConsultation_containProposedConsultationRequestInConflictWithNewConsultationRequestSchedule_conflictError()
    {
        $this->consultationRequest->expects($this->once())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(true);
        $operation = function () {
            $this->executeProposeConsultation();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_proposeConsultation_containConsultationSessionConflictedWithNewConsultationRequest_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeProposeConsultation();
        };

        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    protected function executeReProposeConsultationRequest()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(false);
        $this->participant->reproposeConsultationRequest($this->consultationRequestId, $this->startTime);
    }
    public function test_reProposeConsultationRequest_reProposeConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('rePropose')
                ->with($this->startTime);
        $this->executeReProposeConsultationRequest();
    }
    public function test_reProposeNegotinateConsultationSession_containOtherConsultationRequestConflictedWithReProposedSchedule_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('isProposedConsultationRequestConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeReProposeConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_reProposeNegotinateConsultationSession_containConsultationRequestConflictedWithReProposedSchedule_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeReProposeConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_reProposeNegotinateConsultationSetupScXhedule_consultationRequestNotFound_throwEx()
    {
        $operation = function () {
            $this->participant->reProposeConsultationRequest('non-existing-schedule', $this->startTime);
        };
        $errorDetail = "not found: consultation request not found";
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }

    protected function executeAcceptConsultationRequest()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('isProposedConsultationRequestConflictedWith')
                ->willReturn(false);
        $this->participant->acceptConsultationRequest($this->consultationRequestId, $this->consultationSessionId);
    }
    public function test_acceptConsultationRequest_acceptConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('accept');
        $this->executeAcceptConsultationRequest();
    }
    public function test_acceptConsultationRequest_containOtherConsultationRequestConflictedWithThisSchedule_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('isProposedConsultationRequestConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_acceptConsultationRequest_containConsultationSessionConflictedWithThisSchedule_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_acceptConsultationRequest_addConsultationSessionFromConsultationRequestAndAddToCollection()
    {
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationSession');
        $this->executeAcceptConsultationRequest();

        $this->assertEquals(2, $this->participant->consultationSessions->count());
    }
    
    protected function executeCreateRootWorksheet()
    {
        $this->mission->expects($this->any())
                ->method("programEquals")
                ->willReturn(true);
        return $this->participant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData);
    }
    public function test_createRootWorksheet_returnRootWorksheet()
    {
        $this->mission->expects($this->once())
                ->method("programEquals")
                ->willReturn(true);
        $worksheet = Worksheet::createRootWorksheet(
                        $this->participant, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData);
        $this->assertEquals($worksheet, $this->executeCreateRootWorksheet());
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
    
    protected function executeSubmitBranchWorksheet()
    {
        $this->parentWorksheet->expects($this->any())
                ->method("belongsTo")
                ->willReturn(true);
        return $this->participant->submitBranchWorksheet(
                $this->parentWorksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData);
    }
    public function test_submitBranchWorksheet_returnWorksheetsCreateBranchResult()
    {
        $branch = $this->buildMockOfClass(Worksheet::class);
        $this->parentWorksheet->expects($this->once())
                ->method("createBranchWorksheet")
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData)
                ->willReturn($branch);
        $this->assertEquals($branch, $this->executeSubmitBranchWorksheet());
    }
    public function test_submitBranchworksheet_WorksheetDoesntBelongsToParticipant_forbiddenError()
    {
        $this->parentWorksheet->expects($this->once())
                ->method("belongsTo")
                ->with($this->participant)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertOperationCauseAssetNotOwnedForbiddenError($operation);
    }
    public function test_submitBranchWorksheet_inactiveProgramParticipant_forbiddenError()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertOperationCauseInactiveParticipantForbiddenError($operation);
    }
    
    protected function executeUpdateWorksheet()
    {
        $this->parentWorksheet->expects($this->any())
                ->method("belongsTo")
                ->willReturn(true);
        $this->participant->updateWorksheet($this->parentWorksheet, $this->worksheetName, $this->formRecordData);
    }
    public function test_updateWorksheet_executeWorksheetsUpdateMethod()
    {
        $this->parentWorksheet->expects($this->once())
                ->method("update")
                ->with($this->worksheetName, $this->formRecordData);
        $this->executeUpdateWorksheet();
    }
    public function test_updateWorksheet_inactiveProgramParticipation_forbiddenError()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeUpdateWorksheet();
        };
        $this->assertOperationCauseInactiveParticipantForbiddenError($operation);
    }
    public function test_updateWorksheet_worksheetDoesntBelongsToParticipant_forbiddenError()
    {
        $this->parentWorksheet->expects($this->once())
                ->method("belongsTo")
                ->with($this->participant)
                ->willReturn(false);
        $operation = function (){
            $this->executeUpdateWorksheet();
        };
        $this->assertOperationCauseAssetNotOwnedForbiddenError($operation);
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
}

class TestableParticipant extends Participant
{

    public $program;
    public $id;
    public $active = true;
    public $note;
    public $consultationRequests;
    public $consultationSessions;

    function __construct()
    {
        ;
    }

}
