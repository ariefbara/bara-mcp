<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

class ViewActivityInvitation
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ParticipantInviteeRepository
     */
    protected $participantInviteeRepository;

    public function __construct(ClientRepository $clientRepository,
            ParticipantInviteeRepository $participantInviteeRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->participantInviteeRepository = $participantInviteeRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @param InviteeFilter|null $inviteeFilter
     * @return ParticipantInvitee[]
     */
    public function showAll(string $firmId, string $clientId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                ->viewAllAccessibleActivityInvitations(
                        $this->participantInviteeRepository, $page, $pageSize, $inviteeFilter);
    }

}
