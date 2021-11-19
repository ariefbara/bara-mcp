<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Model\Firm\Team\MemberData;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;

class AddTeamMemberTask implements FirmTaskExecutableByManager
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
     * @var AddTeamMemberPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $addedMemberId;

    public function __construct(
            TeamRepository $teamRepository, ClientRepository $clientRepository, AddTeamMemberPayload $payload)
    {
        $this->teamRepository = $teamRepository;
        $this->clientRepository = $clientRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $team = $this->teamRepository->ofId($this->payload->getTeamId());
        $team->assertManageableInFirm($firm);
        
        $client = $this->clientRepository->ofId($this->payload->getClientId());
        $client->assertUsableInFirm($firm);
        
        $this->addedMemberId = $team->addMember(new MemberData($client, $this->payload->getPosition()));
    }

}
