<?php

namespace Query\Application\Service\Personnel;

use Query\Infrastructure\QueryFilter\InviteeFilter;

class ViewActivityInvitation
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var InviteeRepository
     */
    protected $inviteeRepository;
    
    public function __construct(PersonnelRepository $personnelRepository, InviteeRepository $inviteeRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->inviteeRepository = $inviteeRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param int $page
     * @param int $pageSize
     * @param InviteeFilter|null $inviteeFilter
     * @return invitee[]
     */
    public function showAll(string $firmId, string $personnelId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        return $this->personnelRepository->aPersonnelInFirm($firmId, $personnelId)
                ->viewAllActivityInvitation($this->inviteeRepository, $page, $pageSize, $inviteeFilter);
    }


}
