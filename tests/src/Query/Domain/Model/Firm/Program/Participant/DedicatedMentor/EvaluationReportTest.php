<?php

namespace Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Shared\Form\AttachmentField;
use Query\Domain\Model\Shared\Form\IntegerField;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use Query\Domain\Model\Shared\Form\StringField;
use Query\Domain\Model\Shared\Form\TextAreaField;
use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class EvaluationReportTest extends TestBase
{
    protected $evaluationReport;
    protected $dedicatedMentor;
    protected $evaluationPlan;
    protected $formRecord;
    
    protected $client;
    protected $attachmentField;
    protected $integerField;
    protected $stringField;
    protected $textAreaField;
    protected $singleSelectField;
    protected $multiSelectField;
    
    protected $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = new TestableEvaluationReport();
        
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->evaluationReport->dedicatedMentor = $this->dedicatedMentor;
        
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationReport->evaluationPlan = $this->evaluationPlan;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->evaluationReport->formRecord = $this->formRecord;
        
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->attachmentField = $this->buildMockOfClass(AttachmentField::class);
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->stringField = $this->buildMockOfClass(StringField::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->multiSelectField = $this->buildMockOfClass(MultiSelectField::class);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    public function test_evaluationPlanEquals_sameEvaluationPlan_returnTrue()
    {
        $this->assertTrue($this->evaluationReport->evaluationPlanEquals($this->evaluationPlan));
    }
    public function test_evaluationPlanEquals_differentEvaluationPlan_returnTrue()
    {
        $this->evaluationReport->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->assertFalse($this->evaluationReport->evaluationPlanEquals($this->evaluationPlan));
    }
    
    public function test_getListOfClientPlusTeamName_returnClientPlusTeamNameListResultFromDedicatedMentor()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('getListOfClientPlusTeamName');
        $this->evaluationReport->getListOfClientPlusTeamName();
    }
    
    public function test_getMentorName_returnDedicatedMentorName()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('getMentorName');
        $this->evaluationReport->getMentorName();
    }
    
    public function test_getMentorPlusTeamName_individualParticipant_returnMentorName()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('getMentorPlusTeamName');
        $this->evaluationReport->getMentorPlusTeamName();
    }
    
    public function test_correspondWithClient_returnDedicatedMentorCorrespondWithClientResult()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->client);
        $this->evaluationReport->correspondWithClient($this->client);
    }
    
    public function test_getFileInfoListOfAttachmentFieldRecordCorrespondWith_returnFormRecordResult()
    {
        $this->formRecord->expects($this->once())
                ->method('getFileInfoListOfAttachmentFieldRecordCorrespondWith')
                ->with($this->attachmentField);
        $this->evaluationReport->getFileInfoListOfAttachmentFieldRecordCorrespondWith($this->attachmentField);
    }
    
    public function test_getIntegerFieldRecordValueCorrespondWith_returnFormRecordResult()
    {
        $this->formRecord->expects($this->once())
                ->method('getIntegerFieldRecordValueCorrespondWith')
                ->with($this->integerField);
        $this->evaluationReport->getIntegerFieldRecordValueCorrespondWith($this->integerField);
    }
    
    public function test_getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith_returnFormRecordResult()
    {
        $this->formRecord->expects($this->once())
                ->method('getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith')
                ->with($this->multiSelectField);
        $this->evaluationReport->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith($this->multiSelectField);
    }
    
    public function test_getSingleSelectFieldRecordSelectedOptionNameCorrespondWith_returnFormRecordResult()
    {
        $this->formRecord->expects($this->once())
                ->method('getSingleSelectFieldRecordSelectedOptionNameCorrespondWith')
                ->with($this->singleSelectField);
        $this->evaluationReport->getSingleSelectFieldRecordSelectedOptionNameCorrespondWith($this->singleSelectField);
    }
    
    public function test_getStringFieldRecordValueCorrespondWith_returnFormRecordResult()
    {
        $this->formRecord->expects($this->once())
                ->method('getStringFieldRecordValueCorrespondWith')
                ->with($this->stringField);
        $this->evaluationReport->getStringFieldRecordValueCorrespondWith($this->stringField);
    }
    
    public function test_getTextAreaFieldRecordValueCorrespondWith_returnFormRecordResult()
    {
        $this->formRecord->expects($this->once())
                ->method('getTextAreaFieldRecordValueCorrespondWith')
                ->with($this->textAreaField);
        $this->evaluationReport->getTextAreaFieldRecordValueCorrespondWith($this->textAreaField);
    }
    
    public function test_getParticipantName_returnDedicatedMentorParticipantName()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('getParticipantName');
        $this->evaluationReport->getParticipantName();
    }
    public function test_getParticipant_returnDedicatedMentorParticipant()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('getParticipant');
        $this->evaluationReport->getParticipant();
    }
    
    public function test_correspondWithParticipant_returnDedicatedMentorPartiicpantCorrespondenceStatus()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('correspondWithParticipant')
                ->with($this->participant);
        $this->evaluationReport->correspondWithParticipant($this->participant);
    }
}

class TestableEvaluationReport extends EvaluationReport
{
    public $dedicatedMentor;
    public $evaluationPlan;
    public $id = 'evaluation-report-id';
    public $modifiedTime;
    public $cancelled = false;
    public $formRecord;
    
    function __construct()
    {
        parent::__construct();
    }
}
