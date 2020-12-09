<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;
use TypeError;

class ConsultationSetupTest extends TestBase
{

    protected $program, $participantFeedbackForm, $consultantFeedbackForm;
    protected $consultationSetup;
    protected $id = 'consultanting-id', $name = 'new consultanting name', $sessionDuration = 60;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);

        $this->consultationSetup = new TestableConsultationSetup($this->program, 'id', 'name', 90,
                $this->participantFeedbackForm, $this->consultantFeedbackForm);
        $this->consultationSetup->participantFeedbackForm = null;
        $this->consultationSetup->consultantFeedbackForm = null;
        
        $this->firm = $this->buildMockOfClass(Firm::class);
    }

    protected function executeConstruct()
    {
        return new TestableConsultationSetup($this->program, $this->id, $this->name, $this->sessionDuration,
                $this->participantFeedbackForm, $this->consultantFeedbackForm);
    }

    public function test_construct_setProperties()
    {
        $consultationSetup = $this->executeConstruct();
        $this->assertEquals($this->program, $consultationSetup->program);
        $this->assertEquals($this->id, $consultationSetup->id);
        $this->assertEquals($this->name, $consultationSetup->name);
        $this->assertEquals($this->sessionDuration, $consultationSetup->sessionDuration);
        $this->assertEquals($this->participantFeedbackForm, $consultationSetup->participantFeedbackForm);
        $this->assertEquals($this->consultantFeedbackForm, $consultationSetup->consultantFeedbackForm);
    }

    public function test_construct_emtpyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: consultation setup name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_emptySessionDuration_throwEx()
    {
        $this->sessionDuration = 0;
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: consultation setup session duration is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_construct_nonIntegerSessionDuration_throwEx()
    {
        $this->sessionDuration = 'non integer value';
        $this->expectException(TypeError::class);
        $this->executeConstruct();
    }

    protected function executeUpdate()
    {
        $this->consultationSetup->update(
                $this->name, $this->sessionDuration, $this->participantFeedbackForm, $this->consultantFeedbackForm);
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->consultationSetup->name);
        $this->assertEquals($this->sessionDuration, $this->consultationSetup->sessionDuration);
        $this->assertEquals($this->participantFeedbackForm, $this->consultationSetup->participantFeedbackForm);
        $this->assertEquals($this->consultantFeedbackForm, $this->consultationSetup->consultantFeedbackForm);
    }
    public function test_update_emptyName_badRequest()
    {
        $this->name = '';
        $operation = function () {
            $this->executeUpdate();
        };
        $errorDetail = 'bad request: consultation setup name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->consultationSetup->remove();
        $this->assertTrue($this->consultationSetup->removed);
    }
    
    public function test_belongsToFirm_returnProgramsBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->consultationSetup->belongsToFirm($this->firm);
    }

}

class TestableConsultationSetup extends ConsultationSetup
{

    public $program, $id, $name, $sessionDuration, $participantFeedbackForm, $consultantFeedbackForm,
            $removed;

}
