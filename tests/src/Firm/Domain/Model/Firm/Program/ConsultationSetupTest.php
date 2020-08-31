<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    FeedbackForm,
    Program
};
use Query\Domain\Model\FirmWhitelableInfo;
use Tests\TestBase;
use TypeError;

class ConsultationSetupTest extends TestBase
{

    protected $program, $participantFeedbackForm, $consultantFeedbackForm;
    protected $consultationSetup;
    protected $id = 'consultanting-id', $name = 'new consultanting name', $sessionDuration = 60;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);

        $this->consultationSetup = new TestableConsultationSetup($this->program, 'id', 'name', 90,
            $this->participantFeedbackForm, $this->consultantFeedbackForm);

    }

    protected function executeConstruct()
    {
        return new TestableConsultationSetup($this->program, $this->id, $this->name, $this->sessionDuration,
            $this->participantFeedbackForm, $this->consultantFeedbackForm);
    }

    public function test_construct_setProperties()
    {
        $consultanting = $this->executeConstruct();
        $this->assertEquals($this->program, $consultanting->program);
        $this->assertEquals($this->id, $consultanting->id);
        $this->assertEquals($this->name, $consultanting->name);
        $this->assertEquals($this->sessionDuration, $consultanting->sessionDuration);
        $this->assertEquals($this->participantFeedbackForm, $consultanting->participantFeedbackForm);
        $this->assertEquals($this->consultantFeedbackForm, $consultanting->consultantFeedbackForm);
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

    public function test_remove_setRemovedFlagTrue()
    {
        $this->consultationSetup->remove();
        $this->assertTrue($this->consultationSetup->removed);
    }
    
    public function test_getFirmWhitelableInfo_returnProgramsGetFirmWhitelableInfoResult()
    {
        $this->program->expects($this->once())
                ->method('getFirmWhitelableInfo')
                ->willReturn($whitelableInfo = $this->buildMockOfClass(FirmWhitelableInfo::class));
        $this->assertEquals($whitelableInfo, $this->consultationSetup->getFirmWhitelableInfo());
    }
    
    public function test_getProgramId_returnProgramsGetIdResult()
    {
        $this->program->expects($this->once())
                ->method('getId')
                ->willReturn($programId = 'programId');
        $this->assertEquals($programId, $this->consultationSetup->getProgramId());
    }

}

class TestableConsultationSetup extends ConsultationSetup
{

    public $program, $id, $name, $sessionDuration, $participantFeedbackForm, $consultantFeedbackForm,
        $removed;

}
