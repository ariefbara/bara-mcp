<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;


class ViewInvitationForConsultant
{

    /**
     *
     * @var ConsultantInvitationRepository
     */
    protected $consultantInvitataionRepository;

    function __construct(ConsultantInvitationRepository $consultantInvitataionRepository)
    {
        $this->consultantInvitataionRepository = $consultantInvitataionRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $consultantId
     * @param int $page
     * @param int $pageSize
     * @return ConsultantInvitee[]
     */
    public function showAll(string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize)
    {
        return $this->consultantInvitataionRepository->allInvitationsForConsultant(
                        $firmId, $personnelId, $consultantId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $invitationId): ConsultantInvitee
    {
        return $this->consultantInvitataionRepository->anInvitationForConsultant($firmId, $personnelId, $invitationId);
    }

}
