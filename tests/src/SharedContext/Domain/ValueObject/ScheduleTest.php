<?php

namespace SharedContext\Domain\ValueObject;

use DateTimeImmutable;
use Tests\TestBase;

class ScheduleTest extends TestBase
{

    protected $schedule;
    protected $startTime;
    protected $endTime;
    protected $mediaType = 'online';
    protected $location = 'meet.google.com';

    protected function setUp(): void
    {
        parent::setUp();
        $scheduleData = new ScheduleData(
                new DateTimeImmutable('+24 hours'), new DateTimeImmutable('+25 hours'), 'offline', 'rumah tawa');
        $this->schedule = new Schedule($scheduleData);
        
        $this->startTime = new \DateTimeImmutable('+48 hours');
        $this->endTime = new \DateTimeImmutable('+50 hours');
    }
    
    protected function getScheduleData()
    {
        return new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location);
    }
    
    protected function construct()
    {
        return new TestableSchedule($this->getScheduleData());
    }
    public function test_construct_setProperties()
    {
        $schedule = $this->construct();
        $this->assertSame($this->startTime, $schedule->startTime);
        $this->assertSame($this->endTime, $schedule->endTime);
        $this->assertSame($this->mediaType, $schedule->mediaType);
        $this->assertSame($this->location, $schedule->location);
    }
    public function test_construct_nullStartTime_TypeError()
    {
        $this->startTime = null;
        $this->expectException(\TypeError::class);
        $this->construct();
    }
    public function test_construct_nullEndTime_TypeError()
    {
        $this->endTime = null;
        $this->expectException(\TypeError::class);
        $this->construct();
    }

}

class TestableSchedule extends Schedule
{

    public $startTime;
    public $endTime;
    public $mediaType;
    public $location;

}
