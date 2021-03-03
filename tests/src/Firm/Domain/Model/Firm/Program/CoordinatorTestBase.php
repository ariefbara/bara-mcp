<?php

namespace Tests\src\Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Coordinator;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class CoordinatorTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $program;
    /**
     * 
     * @var MockObject
     */
    protected $personnel;
    /**
     * 
     * @var TestableCoordinator
     */
    protected $coordinator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnel->expects($this->any())->method('isActive')->willReturn(true);
        $this->coordinator = new TestableCoordinator($this->program, 'id', $this->personnel);
    }
    protected function assertInactiveCoordinator(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: only active coordinator can make this request');
    }
    protected function assertUnmanageAsset(callable $operation, string $assetName): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', "forbidden: unable to manage $assetName");
    }
    protected function setAssetManageable(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(true);
    }
    protected function setAssetUnmanageable(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(false);
    }

}

class TestableCoordinator extends Coordinator
{
    public $program;
    public $id;
    public $personnel;
    public $active;
    public $meetingInvitations;
}
