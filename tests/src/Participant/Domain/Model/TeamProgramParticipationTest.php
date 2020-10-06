<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class TeamProgramParticipationTest extends TestBase
{

    protected $teamProgramParticipation;
    protected $programParticipation;
    protected $program;
    protected $worksheetId = "worksheetId", $worksheetName = "worksheet name", $mission, $formRecordData;
    protected $worksheet;
    protected $consultationRequest, $consultationRequestId = "consultationRequestId", $startTime;
    protected $consultationSetup, $consultant;
    protected $consultationSession;
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramParticipation = new TestableTeamProgramParticipation();
        $this->teamProgramParticipation->team = $this->buildMockOfClass(Team::class);
        $this->programParticipation = $this->buildMockOfClass(Participant::class);
        $this->teamProgramParticipation->programParticipation = $this->programParticipation;

        $this->program = $this->buildMockOfClass(Program::class);

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);

        $this->worksheet = $this->buildMockOfClass(Worksheet::class);

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->startTime = new DateTimeImmutable();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }

    public function test_isActiveParticipantOfProgram_returnProgramParticipationsIsActiveParticipantOfProgramResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("isActiveParticipantOfProgram")
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->teamProgramParticipation->isActiveParticipantOfProgram($this->program));
    }

    public function test_teamEquals_sameTeam_returnTrue()
    {
        $this->assertTrue($this->teamProgramParticipation->teamEquals($this->teamProgramParticipation->team));
    }

    public function test_teamEquals_differentTeam_returnFalse()
    {
        $team = $this->buildMockOfClass(Team::class);
        $this->assertFalse($this->teamProgramParticipation->teamEquals($team));
    }

    public function test_submitRootWorksheet_returnProgramParticipationCreateRootWorksheetResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("createRootWorksheet")
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, $this->teamMember);
        $this->teamProgramParticipation->submitRootWorksheet(
                $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, $this->teamMember);
    }

    public function test_submitBranchWorksheet_returnProgramParticipationSubmitBranchWorksheetResult()
    {
        $branch = $this->buildMockOfClass(Worksheet::class);
        $this->programParticipation->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, $this->teamMember)
                ->willReturn($branch);
        $this->assertEquals(
                $branch,
                $this->teamProgramParticipation->submitBranchWorksheet($this->worksheet, $this->worksheetId,
                        $this->worksheetName, $this->mission, $this->formRecordData, $this->teamMember));
    }

    public function test_updateWorksheet_executeProgramParticipationsUpdateWorksheet()
    {
        $this->programParticipation->expects($this->once())
                ->method("updateWorksheet")
                ->with($this->worksheet, $this->worksheetName, $this->formRecordData, $this->teamMember);
        $this->teamProgramParticipation->updateWorksheet($this->worksheet, $this->worksheetName, $this->formRecordData, $this->teamMember);
    }

    public function test_quit_executeProgramParticipationQuitMethod()
    {
        $this->programParticipation->expects($this->once())
                ->method("quit");
        $this->teamProgramParticipation->quit();
    }

    public function test_submitConsultatioNRequest_returnProgramParticipationsSubmitConsultationRequestResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("submitConsultationRequest")
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime, $this->teamMember);
        $this->teamProgramParticipation->submitConsultationRequest(
                $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime, $this->teamMember);
    }
    public function test_changeConsultationRequestTime_executeProgramParticipationChangeConsultationRequestTime()
    {
        $this->programParticipation->expects($this->once())
                ->method("changeConsultationRequestTime")
                ->with($this->consultationRequestId, $this->startTime, $this->teamMember);
        $this->teamProgramParticipation->changeConsultationRequestTime($this->consultationRequestId, $this->startTime, $this->teamMember);
    }
    public function test_cancelConsultationRequest_executeProgramParticipationCancelConsultationRequestMethod()
    {
        $this->programParticipation->expects($this->once())
                ->method("cancelConsultationRequest")
                ->with($this->consultationRequest, $this->teamMember);
        $this->teamProgramParticipation->cancelConsultationRequest($this->consultationRequest, $this->teamMember);
    }
    public function test_acceptOfferedConsultationRequest_executeProgramParticipationsAcceptOfferedConsultationRequestMethod()
    {
        $this->programParticipation->expects($this->once())
                ->method("acceptOfferedConsultationRequest")
                ->with($this->consultationRequestId, $this->anything(), $this->teamMember);
        $this->teamProgramParticipation->acceptOfferedConsultationRequest($this->consultationRequestId, $this->teamMember);
    }
    public function test_submitConsultationSessionReport_executeProgramParticipationSubmitConsultationSessionReport()
    {
        $this->programParticipation->expects($this->once())
                ->method("submitConsultationSessionReport")
                ->with($this->consultationSession, $this->formRecordData, $this->teamMember);
        $this->teamProgramParticipation->submitConsultationSessionReport($this->consultationSession, $this->formRecordData, $this->teamMember);
    }

}

class TestableTeamProgramParticipation extends TeamProgramParticipation
{

    public $team;
    public $id;
    public $programParticipation;

    function __construct()
    {
        parent::__construct();
    }

}
