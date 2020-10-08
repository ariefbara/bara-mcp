<?php

namespace Resources\Domain\ValueObject;

use DateInterval;
use DateTimeImmutable;
use Tests\TestBase;

class DateTimeIntervalTest extends TestBase
{

    protected $startTime, $endTime;
    protected $vo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startTime = new DateTimeImmutable("-7 days");
        $this->endTime = new DateTimeImmutable("+7 days");
        $this->vo = new TestableDateTimeInterval($this->startTime, $this->endTime);
    }

    private function executeConstruct()
    {
        return new TestableDateTimeInterval($this->startTime, $this->endTime);
    }

    function test_construct_createDateTimeIntervalVo()
    {
        $vo = $this->executeConstruct();
        $this->assertInstanceOf("Resources\Domain\ValueObject\DateTimeInterval", $vo);
    }

    function test_construct_setNormalizeStartDateTime()
    {
        $vo = $this->executeConstruct();
        $this->assertEquals(new DateTimeImmutable($this->startTime->format('Y-m-d H:i:s')), $vo->startDateTime);
    }

    function test_construct_nullStartTime_constructNormally()
    {
        $this->startTime = null;
        $vo = $this->executeConstruct();
        $this->assertNull($vo->startDateTime);
    }

    function test_construct_setNormalizeEndDateTime()
    {
        $vo = $this->executeConstruct();
        $this->assertEquals(new DateTimeImmutable($this->endTime->format('Y-m-d H:i:s')), $vo->endDateTime);
    }

    function test_construct_nullEndTime_constructNormally()
    {
        $this->endTime = null;
        $vo = $this->executeConstruct();
        $this->assertNull($vo->endDateTime);
    }

    function test_getStartTimeStamp_returnStartTimeStamp()
    {
        $this->assertEquals($this->startTime->getTimestamp(), $this->vo->getStartTimeStamp());
    }

    function test_getStartTimeStamp_nullStartDateTime_returnNegativeINF()
    {
        $this->vo->startDateTime = null;
        $this->assertEquals(-INF, $this->vo->getStartTimeStamp());
    }

    function test_getEndTimeStamp_returnEndDateTimeStamp()
    {
        $this->assertEquals($this->endTime->getTimestamp(), $this->vo->getEndTimeStamp());
    }

    function test_getEndTimeStamp_nullEndDateTime_returnINF()
    {
        $this->vo->endDateTime = null;
        $this->assertEquals(INF, $this->vo->getEndTimeStamp());
    }

    function test_construct_startTimeAfterEndTime_throwEx()
    {
        $this->startTime = $this->endTime->add(new DateInterval("PT24H"));
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: end time must be bigger than or equals start time";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    public function test_getStartDayInIndonesianFormat_returnDayFormatInIndonesias()
    {
        $this->vo->startDateTime = new DateTimeImmutable('last monday');
        $this->assertEquals('senin', $this->vo->getStartDayInIndonesianFormat());

        $this->vo->startDateTime = new DateTimeImmutable('last tuesday');
        $this->assertEquals('selasa', $this->vo->getStartDayInIndonesianFormat());

        $this->vo->startDateTime = new DateTimeImmutable('last wednesday');
        $this->assertEquals('rabu', $this->vo->getStartDayInIndonesianFormat());

        $this->vo->startDateTime = new DateTimeImmutable('last thursday');
        $this->assertEquals('kamis', $this->vo->getStartDayInIndonesianFormat());

        $this->vo->startDateTime = new DateTimeImmutable('last friday');
        $this->assertEquals('jumat', $this->vo->getStartDayInIndonesianFormat());

        $this->vo->startDateTime = new DateTimeImmutable('last saturday');
        $this->assertEquals('sabtu', $this->vo->getStartDayInIndonesianFormat());

        $this->vo->startDateTime = new DateTimeImmutable('last sunday');
        $this->assertEquals('minggu', $this->vo->getStartDayInIndonesianFormat());
    }

    public function test_getStartDayInIndonesianFormat_emptyStartDate_returnNull()
    {
        $this->vo->startDateTime = null;
        $this->assertNull($this->vo->getStartDayInIndonesianFormat());
    }

    protected function executeGetTimeDescriptionInIndonesianFormat()
    {
        return $this->vo->getTimeDescriptionInIndonesianFormat();
    }

    public function test_getTimeDescriptionInIndonesianFormat_monday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last monday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "senin, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_tuesday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last tuesday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "selasa, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_wednesday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last wednesday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "rabu, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_thursday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last thursday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "kamis, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_friday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last friday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "jumat, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_saturday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last saturday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "sabtu, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_sunday_returnTimeDescription()
    {
        $startDate = new DateTimeImmutable("last sunday");
        $endDate = $startDate->add(new DateInterval("PT1H"));
        $this->vo->startDateTime = $startDate;
        $this->vo->endDateTime = $endDate;
        $description = "minggu, {$startDate->format("d-M-Y")} pukul {$startDate->format("H:i")} - {$endDate->format("H:i")}";
        $this->assertEquals($description, $this->executeGetTimeDescriptionInIndonesianFormat());
    }

    public function test_getTimeDescriptionInIndonesianFormat_emptyStartTime_returnNull()
    {
        $this->vo->startDateTime = null;
        $this->assertNull($this->executeGetTimeDescriptionInIndonesianFormat());
    }
    public function test_getTimeDescriptionInIndonesianFormat_emptyEndTime_returnNull()
    {
        $this->vo->endDateTime = null;
        $this->assertNull($this->executeGetTimeDescriptionInIndonesianFormat());
    }

}

class TestableDateTimeInterval extends DateTimeInterval
{

    public $startDateTime, $endDateTime;

    public function getStartTimeStamp(): float
    {
        return parent::getStartTimeStamp();
    }

    public function getEndTimeStamp(): float
    {
        return parent::getEndTimeStamp();
    }

}
