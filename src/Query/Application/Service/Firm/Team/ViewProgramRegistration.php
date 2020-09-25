<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\TeamProgramRegistration;

class ViewProgramRegistration
{

    /**
     *
     * @var TeamProgramRegistrationRepository
     */
    protected $teamProgramRegistrationRepository;

    public function __construct(TeamProgramRegistrationRepository $teamProgramRegistrationRepository)
    {
        $this->teamProgramRegistrationRepository = $teamProgramRegistrationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $teamId
     * @param int $page
     * @param int $pageSize
     * @return TeamProgramRegistration[]
     */
    public function showAll(string $firmId, string $teamId, int $page, int $pageSize)
    {
        return $this->teamProgramRegistrationRepository->all($firmId, $teamId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId, string $teamProgramRegistrationId): TeamProgramRegistration
    {
        return $this->teamProgramRegistrationRepository->ofId($firmId, $teamId, $teamProgramRegistrationId);
    }

}
