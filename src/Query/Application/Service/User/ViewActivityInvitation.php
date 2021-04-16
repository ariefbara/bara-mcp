<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

class ViewActivityInvitation
{

    /**
     * 
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * 
     * @var ParticipantInviteeRepository
     */
    protected $participantInviteeRepository;

    public function __construct(UserRepository $userRepository,
            ParticipantInviteeRepository $participantInviteeRepository)
    {
        $this->userRepository = $userRepository;
        $this->participantInviteeRepository = $participantInviteeRepository;
    }

    /**
     * 
     * @param string $userId
     * @param int $page
     * @param int $pageSize
     * @param InviteeFilter|null $inviteeFilter
     * @return ParticipantInvitee[]
     */
    public function showAll(string $userId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        return $this->userRepository->ofId($userId)
                        ->viewAllActivityInvitations(
                                $this->participantInviteeRepository, $page, $pageSize, $inviteeFilter);
    }

}
