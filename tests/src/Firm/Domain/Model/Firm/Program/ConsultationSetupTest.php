<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    ConsultationFeedbackForm,
    Program
};
use Tests\TestBase;
use TypeError;

class ConsultationSetupTest extends TestBase
{

    protected $program, $participantFeedbackForm, $consultantFeedbackForm;
    protected $mentoring;
    protected $id = 'mentoring-id', $name = 'new mentoring name', $sessionDuration = 60;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participantFeedbackForm = $this->buildMockOfClass(ConsultationFeedbackForm::class);
        $this->consultantFeedbackForm = $this->buildMockOfClass(ConsultationFeedbackForm::class);

        $this->mentoring = new TestableConsultationSetup($this->program, 'id', 'name', 90,
            $this->participantFeedbackForm, $this->consultantFeedbackForm);

    }

    protected function executeConstruct()
    {
        return new TestableConsultationSetup($this->program, $this->id, $this->name, $this->sessionDuration,
            $this->participantFeedbackForm, $this->consultantFeedbackForm);
    }

    public function test_construct_setProperties()
    {
        $mentoring = $this->executeConstruct();
        $this->assertEquals($this->program, $mentoring->program);
        $this->assertEquals($this->id, $mentoring->id);
        $this->assertEquals($this->name, $mentoring->name);
        $this->assertEquals($this->sessionDuration, $mentoring->sessionDuration);
        $this->assertEquals($this->participantFeedbackForm, $mentoring->participantFeedbackForm);
        $this->assertEquals($this->consultantFeedbackForm, $mentoring->mentorFeedbackForm);
    }

    public function test_construct_emtpyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: mentoring name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_emptySessionDuration_throwEx()
    {
        $this->sessionDuration = 0;
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: mentoring session duration is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_nonIntegerSessionDuration_throwEx()
    {
        $this->sessionDuration = 'non integer value';
        $this->expectException(TypeError::class);
        $this->executeConstruct();
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->mentoring->remove();
        $this->assertTrue($this->mentoring->removed);
    }

}

class TestableConsultationSetup extends ConsultationSetup
{

    public $program, $id, $name, $sessionDuration, $participantFeedbackForm, $mentorFeedbackForm,
        $removed;

}
