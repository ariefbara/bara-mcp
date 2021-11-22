<?php

namespace Tests\src\Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class FirmTaskTestBase extends TestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $firm;


    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
    }

    /**
     * 
     * @var MockObject
     */
    protected $clientRepository;
    /**
     * 
     * @var MockObject
     */
    protected $client;
    protected $clientId = 'clientId';
    protected function setClientRelatedDependency(): void
    {
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
    }

    /**
     * 
     * @var MockObject
     */
    protected $programRepository;
    /**
     * 
     * @var MockObject
     */
    protected $program;
    protected $programId = 'programId';
    protected function setProgramRelatedDependency(): void
    {
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);
    }

    /**
     * 
     * @var MockObject
     */
    protected $teamRepository;
    /**
     * 
     * @var MockObject
     */
    protected $team;
    protected $teamId = 'teamId';
    protected function setTeamRelatedDependency(): void
    {
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamRepository = $this->buildMockOfInterface(TeamRepository::class);
        $this->teamRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamId)
                ->willReturn($this->team);
    }

}
