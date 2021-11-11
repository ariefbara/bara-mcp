<?php

namespace SharedContext\Domain\Model\Mentoring;

use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ParticipantReportTest extends TestBase
{

    protected $mentoring;
    protected $form;
    protected $formRecordData;
    protected $participantReport, $formRecord;
    protected $id = 'newId', $mentorRating = 9;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->form = $this->buildMockOfClass(Form::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->participantReport = new TestableParticipantReport($this->mentoring, 'id', 6, $this->form, $this->formRecordData);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->participantReport->formRecord = $this->formRecord;
    }
    
    protected function construct()
    {
        return new TestableParticipantReport($this->mentoring, $this->id, $this->mentorRating, $this->form, $this->formRecordData);
    }
    public function test_construct_setProperties()
    {
        $participantReport = $this->construct();
        $this->assertSame($this->mentoring, $participantReport->mentoring);
        $this->assertSame($this->id, $participantReport->id);
        $this->assertSame($this->mentorRating, $participantReport->mentorRating);
        $this->assertInstanceOf(FormRecord::class, $participantReport->formRecord);
    }
    
    protected function update()
    {
        $this->participantReport->update($this->mentorRating, $this->form, $this->formRecordData);
    }
    public function test_update_updateProperties()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->update();
        $this->assertSame($this->mentorRating, $this->participantReport->mentorRating);
    }

}

class TestableParticipantReport extends ParticipantReport
{

    public $mentoring;
    public $id;
    public $mentorRating;
    public $formRecord;

}
