<?php

namespace Tests;

use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\LabelData;

class TestBase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function assertRegularExceptionThrowed(callable $operation,
        string $message, string $errorDetail)
    {
        try {
            $operation();
            $this->fail();
        } catch (RegularException $e) {
            $this->assertEquals($message, $e->getMessage());
            $this->assertEquals($errorDetail, $e->getErrorDetail());
        }
    }
    
    protected function YmdHisStringOfCurrentTime(): string
    {
        return (new DateTime())->format('Y-m-d H:i:s');
    }
    
    function markAsSuccess() {
        $this->assertEquals(1, 1);
    }
    protected function buildMockOfClass(string $className) {
        return $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
    }
    protected function buildMockOfInterface(string $className) {
        return $this->getMockBuilder($className)->getMock();
    }
    protected function setMockDataContainValidLabelData(MockObject $mockData): void
    {
        $labelData = $this->buildMockOfClass(LabelData::class);
        $labelData->expects($this->any())
                ->method('getName')
                ->willReturn('label name');
        $mockData->expects($this->any())
                ->method('getLabelData')
                ->willReturn($labelData);
    }
    
}

