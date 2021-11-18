<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Model\Firm\Team\MemberData;
use Firm\Domain\Model\Firm\TeamData;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;

class AddTeamTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var AddTeamPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $addedTeamId;

    public function __construct(
            TeamRepository $teamRepository, ClientRepository $clientRepository, AddTeamPayload $payload)
    {
        $this->teamRepository = $teamRepository;
        $this->clientRepository = $clientRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $this->addedTeamId = $this->teamRepository->nextIdentity();
        $teamData = new TeamData($this->payload->getName());
        foreach ($this->payload->getMemberDataRequestList() as $memberDataRequest) {
            $client = $this->clientRepository->ofId($memberDataRequest->getClientId());
            $client->assertUsableInFirm($firm);
            $teamData->addMemberData(new MemberData($client, $memberDataRequest->getPosition()));
        }
        $team = $firm->createTeam($this->addedTeamId, $teamData);
        $this->teamRepository->add($team);
    }

}
