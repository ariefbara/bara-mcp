<?php

namespace User\Domain\Model\Personnel;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;
use User\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\EvaluationPlan,
    DependencyModel\Firm\Program\Participant,
    Model\Personnel\Coordinator\EvaluationReport
};

class CoordinatorTest extends TestBase
{
    protected $coordinator;
    protected $program;
    protected $evaluationReport;
    protected $participant, $evaluationPlan, $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = new TestableCoordinator();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->coordinator->program = $this->program;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->coordinator->evaluationReports = new ArrayCollection();
        $this->coordinator->evaluationReports->add($this->evaluationReport);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeSubmitEvaluationReportOfParticipant()
    {
        $this->participant->expects($this->any())
                ->method("isActiveParticipantOfProgram")
                ->willReturn(true);
        $this->evaluationPlan->expects($this->any())
                ->method("isAnEnabledEvaluationPlanInProgram")
                ->willReturn(true);
        $this->coordinator->submitEvaluationReportOfParticipant(
                $this->participant, $this->evaluationPlan, $this->formRecordData);
    }
    public function test_submitEvaluationReportOfParticipant_addEvaluationReportToCollection()
    {
        $this->executeSubmitEvaluationReportOfParticipant();
        $this->assertEquals(2, $this->coordinator->evaluationReports->count());
        $this->assertInstanceOf(EvaluationReport::class, $this->coordinator->evaluationReports->last());
    }
    public function test_submitEvaluationReportOfParticipant_notAnActiveParticipantOfProgram_forbidden()
    {
        $this->participant->expects($this->once())
                ->method("isActiveParticipantOfProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitEvaluationReportOfParticipant();
        };
        $errorDetail = "forbidden: participant can't receive evaluation report";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_submitEvaluationReportOfParticipant_evaluationPlanCantBeUsedInProgram_forbidden()
    {
        $this->evaluationPlan->expects($this->once())
                ->method("isAnEnabledEvaluationPlanInProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitEvaluationReportOfParticipant();
        };
        $errorDetail = "forbidden: evaluation plan can't be used";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_submitEvaluationReportOfParticipant_anEvaluationReportCorrespondWithParticipantAndEvaluationPlanAlreadyExist_updateThisReportInsteadOfAddNewReport()
    {
        $this->evaluationReport->expects($this->once())
                ->method("aReportOfEvaluationPlanCorrespondWithParticipant")
                ->with($this->participant, $this->evaluationPlan)
                ->willReturn(true);
        
        $this->evaluationReport->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        
        $this->executeSubmitEvaluationReportOfParticipant();
        
        $this->assertEquals(1, $this->coordinator->evaluationReports->count());
    }
}

class TestableCoordinator extends Coordinator
{
    public $personnel;
    public $id;
    public $program;
    public $active = true;
    public $evaluationReports;
    
    function __construct()
    {
        parent::__construct();
    }
}
