<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor\EvaluationReport;
use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class DedicatedMentorTest extends TestBase
{
    protected $consultant;
    protected $dedicatedMentor;
    
    protected $task;

    protected $evaluationReport, $evaluationPlan, $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentor = new TestableDedicatedMentor();
        
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->dedicatedMentor->consultant = $this->consultant;
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByDedicatedMentor::class);
        
        $this->dedicatedMentor->evaluationReports = new ArrayCollection();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->dedicatedMentor->evaluationReports->add($this->evaluationReport);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeTask()
    {
        $this->dedicatedMentor->executeTask($this->task);
    }
    public function test_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->dedicatedMentor);
        $this->executeTask();
    }
    public function test_executeTask_inactiveDedicatedMentor_forbidden()
    {
        $this->dedicatedMentor->cancelled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTask();
        }, 'Forbidden', 'forbidden: only active dedicated mentor can make this request');
    }
    
    protected function executeSubmitEvaluationReport()
    {
        $this->dedicatedMentor->submitEvaluationReport($this->evaluationPlan, $this->formRecordData);
    }
    public function test_submitEvaluationReport_addEvaluationReportToCollection()
    {
        $this->executeSubmitEvaluationReport();
        $this->assertEquals(2, $this->dedicatedMentor->evaluationReports->count());
        $this->assertInstanceOf(EvaluationReport::class, $this->dedicatedMentor->evaluationReports->first());
    }
    public function test_submitEvaluationReport_alreadyHasActiveEvaluationReportCorrespondWithEvaluationPlan_updateThisReport()
    {
        $this->evaluationReport->expects($this->once())
                ->method('isActiveReportCorrespondWithEvaluationPlan')
                ->with($this->evaluationPlan)
                ->willReturn(true);
        $this->evaluationReport->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeSubmitEvaluationReport();
    }
    public function test_submitEvaluationReport_causeAnUpdate_preventAddNewReport()
    {
        $this->evaluationReport->expects($this->once())
                ->method('isActiveReportCorrespondWithEvaluationPlan')
                ->with($this->evaluationPlan)
                ->willReturn(true);
        $this->executeSubmitEvaluationReport();
        $this->assertEquals(1, $this->dedicatedMentor->evaluationReports->count());
    }
    public function test_submitEvaluationReport_verifyEvaluationPlanUsableByMentor()
    {
        $this->consultant->expects($this->once())
                ->method('verifyAssetUsable')
                ->with($this->evaluationPlan);
        $this->executeSubmitEvaluationReport();
    }
    public function test_submitEvaluationReport_aggregateEventFromEvaluationReport()
    {
        $this->executeSubmitEvaluationReport();
        $this->assertInstanceOf(CommonEvent::class, $this->dedicatedMentor->recordedEvents[0]);
    }
    
}

class TestableDedicatedMentor extends DedicatedMentor
{
    public $consultant;
    public $id = 'dedicated-mentor-id';
    public $cancelled = false;
    public $evaluationReports;
    public $recordedEvents;
    
    function __construct()
    {
        parent::__construct();
    }
}
