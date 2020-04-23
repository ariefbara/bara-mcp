<?php
namespace Resources\Domain\ValueObject;

use Tests\TestBase;

class DateIntervalTest extends TestBase
{
    protected $startDate, $endDate;
    protected $vo;
    
    protected function setUp(): void {
        parent::setUp();
        $this->startDate = new \DateTimeImmutable('-7 days');
        $this->endDate = new \DateTimeImmutable('+7 days');
        $this->vo = new TestableDateInterval($this->startDate, $this->endDate);
    }
    
    protected function executeConstruct() {
        return new TestableDateInterval($this->startDate, $this->endDate);
    }
    function test_construct_createDateIntervalVo() {
        $vo = $this->executeConstruct();
        $this->assertInstanceOf("Resources\Domain\ValueObject\DateInterval", $vo);
    }
    function test_construct_setStartDateAsDateNormalizeValue() {
        $year = $this->startDate->format("Y");
        $month = $this->startDate->format("m");
        $date = $this->startDate->format("d");
        $normalizeStartDate = (new \DateTimeImmutable())->setDate($year, $month, $date)->setTime(null, null, null);
        
        $vo = $this->executeConstruct();
        $this->assertEquals($normalizeStartDate, $vo->startDate);
    }
    function test_construct_nullStartDate_constructNormally() {
        $this->startDate = null;
        $vo = $this->executeConstruct();
        $this->assertNull($vo->startDate);
    }
    function test_construct_setEndDateAsDateNormalizeValue() {
        $year = $this->endDate->format("Y");
        $month = $this->endDate->format("m");
        $date = $this->endDate->format("d");
        $normalizeEndDate = (new \DateTimeImmutable())->setDate($year, $month, $date)->setTime(23, 59, 59);
        
        $vo = $this->executeConstruct();
        $this->assertEquals($normalizeEndDate, $vo->endDate);
    }
    function test_construct_nullEndDate_constructNormally() {
        $this->endDate = null;
        $vo = $this->executeConstruct();
        $this->assertNull($vo->endDate);
    }
    function test_getStartTimeStamp_returnTimeStampOfNormalizeStartTime() {
        $year = $this->startDate->format("Y");
        $month = $this->startDate->format("m");
        $date = $this->startDate->format("d");
        $normalizeStartDate = (new \DateTimeImmutable())->setDate($year, $month, $date)->setTime(null, null, null);
        
        $this->assertEquals($normalizeStartDate->getTimestamp(), $this->vo->getStartTimeStamp());
    }
    function test_getStartTimeStamp_nullStartDate_returnNegativeINF() {
        $this->vo->startDate = null;
        $this->assertEquals(-INF, $this->vo->getStartTimeStamp());
    }
    function test_getEndTimeStamp_returnTimeStampOfNormalizeEndTime() {
        $year = $this->endDate->format("Y");
        $month = $this->endDate->format("m");
        $date = $this->endDate->format("d");
        $normalizeEndDate = (new \DateTimeImmutable())->setDate($year, $month, $date)->setTime(23, 59, 59);
        
        $this->assertEquals($normalizeEndDate->getTimestamp(), $this->vo->getEndTimeStamp());
    }
    function test_getEndTimeStamp_nullEndTime_returnINF() {
        $this->vo->endDate = null;
        $this->assertEquals(INF, $this->vo->getEndTimeStamp());
    }
    
    function test_construct_startDateAfterEndDate_throwEx() {
        $this->startDate = $this->endDate->add(new \DateInterval("PT24H"));
        $operation  = function(){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: end date must be bigger than or equals start date";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

}

class TestableDateInterval extends DateInterval{
    public $startDate, $endDate;
    public function getStartTimeStamp(): float {
        return parent::getStartTimeStamp();
    }
    public function getEndTimeStamp(): float {
        return parent::getEndTimeStamp();
    }
}

