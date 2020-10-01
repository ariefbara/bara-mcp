<?php

namespace Participant\Domain\Model;

use Participant\Domain\{
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
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
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData);
        $this->teamProgramParticipation->submitRootWorksheet(
                $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData);
    }

    public function test_submitBranchWorksheet_returnProgramParticipationSubmitBranchWorksheetResult()
    {
        $branch = $this->buildMockOfClass(Worksheet::class);
        $this->programParticipation->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData)
                ->willReturn($branch);
        $this->assertEquals(
                $branch,
                $this->teamProgramParticipation->submitBranchWorksheet($this->worksheet, $this->worksheetId,
                        $this->worksheetName, $this->mission, $this->formRecordData));
    }
    
    public function test_updateWorksheet_executeProgramParticipationsUpdateWorksheet()
    {
        $this->programParticipation->expects($this->once())
                ->method("updateWorksheet")
                ->with($this->worksheet, $this->worksheetName, $this->formRecordData);
        $this->teamProgramParticipation->updateWorksheet($this->worksheet, $this->worksheetName, $this->formRecordData);
    }
    
    public function test_quit_executeProgramParticipationQuitMethod()
    {
        $this->programParticipation->expects($this->once())
                ->method("quit");
        $this->teamProgramParticipation->quit();
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