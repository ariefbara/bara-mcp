<?php

namespace SharedContext\Domain\Model\Mentoring;

use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class MentorReportTest extends TestBase
{
    protected $mentoring;
    protected $form;
    protected $formRecordData;
    protected $mentorReport, $formRecord;
    protected $id = 'newId', $participantRating = 9;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->form = $this->buildMockOfClass(Form::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->mentorReport = new TestableMentorReport($this->mentoring, 'id', 6, $this->form, $this->formRecordData);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->mentorReport->formRecord = $this->formRecord;
    }
    
    protected function construct()
    {
        return new TestableMentorReport(
                $this->mentoring, $this->id, $this->participantRating, $this->form, $this->formRecordData);
    }
    public function test_construct_setProperties()
    {
        $mentorReport = $this->construct();
        $this->assertSame($this->mentoring, $mentorReport->mentoring);
        $this->assertSame($this->id, $mentorReport->id);
        $this->assertSame($this->participantRating, $mentorReport->participantRating);
        $this->assertInstanceOf(FormRecord::class, $mentorReport->formRecord);
    }
    
    protected function update()
    {
        $this->mentorReport->update($this->participantRating, $this->form, $this->formRecordData);
    }
    public function test_update_updateProperties()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->update();
        $this->assertSame($this->participantRating, $this->mentorReport->participantRating);
    }
}

class TestableMentorReport extends MentorReport
{
    public $mentoring;
    public $id;
    public $participantRating;
    public $formRecord;
}
