<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateInterval
};
use Tests\TestBase;

class RegistrationPhaseTest extends TestBase
{
    protected $registrationPhase, $dateInterval;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationPhase = new TestableRegistrationPhase();
        $this->registrationPhase->removed = false;
        $this->dateInterval = $this->buildMockOfClass(DateInterval::class);
        $this->registrationPhase->startEndDate = $this->dateInterval;
    }

    public function test_isOpen_returnStartEndDateContainCurrentTimeResult()
    {
        $this->dateInterval->expects($this->once())
                ->method('contain')
                ->with(DateTimeImmutableBuilder::buildYmdHisAccuracy())
                ->willReturn(true);
        $this->assertTrue($this->registrationPhase->isOpen());
    }

    public function test_isOpen_alreadyRemoved_returnFalse()
    {
        $this->registrationPhase->removed = true;
        $this->dateInterval->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->assertFalse($this->registrationPhase->isOpen());
    }

}

class TestableRegistrationPhase extends RegistrationPhase
{
    public $program;
    public $id;
    public $startEndDate;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
