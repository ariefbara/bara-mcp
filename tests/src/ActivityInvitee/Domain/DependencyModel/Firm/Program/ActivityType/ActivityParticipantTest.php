<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program\ActivityType;

use ActivityInvitee\Domain\DependencyModel\Firm\FeedbackForm;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ActivityParticipantTest extends TestBase
{
    protected $activityParticipant;
    protected $reportForm;
    protected $formRecordId = "formRecordId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityParticipant = new TestableActivityParticipant();
        $this->reportForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->activityParticipant->reportForm = $this->reportForm;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_createFormRecord_returnReportFormCreateFormRecordResult()
    {
        $this->reportForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->activityParticipant->createFormRecord($this->formRecordId, $this->formRecordData);
    }
}

class TestableActivityParticipant extends ActivityParticipant
{
    public $id;
    public $reportForm;
    
    function __construct()
    {
        parent::__construct();
    }
}
