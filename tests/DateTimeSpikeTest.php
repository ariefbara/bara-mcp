<?php

namespace Tests;

use DateTimeImmutable;

class DateTimeSpikeTest extends TestBase
{
    protected $startTime;
    protected $endTime;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->startTime = new DateTimeImmutable('+1 months');
        $this->endTime = new DateTimeImmutable('+3 months');
    }
    
    protected function diff()
    {
        return $this->startTime->diff($this->endTime);
    }
    public function test_daysValue()
    {
        $this->assertEquals($this->diff()->days, 61);
    }
    public function test_mValue()
    {
        $this->assertEquals($this->diff()->m, 2);
    }
    public function test_dValue()
    {
        $this->assertEquals($this->diff()->d, 0);
    }
    public function test_yValue()
    {
        $this->assertEquals($this->diff()->y, 0);
    }
    public function test_intervalEvaluation()
    {
        $this->assertTrue($this->startTime->add(new \DateInterval('P2M1D')) > $this->endTime);
    }
}
