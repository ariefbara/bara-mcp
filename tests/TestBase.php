<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Resources\Exception\RegularException;

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
        return (new \DateTime())->format('Y-m-d H:i:s');
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
    
}

