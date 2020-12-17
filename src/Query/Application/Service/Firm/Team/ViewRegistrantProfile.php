<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;

class ViewRegistrantProfile
{

    /**
     * 
     * @var RegistrantProfileRepository
     */
    protected $registrantProfileRepository;

    function __construct(RegistrantProfileRepository $registrantProfileRepository)
    {
        $this->registrantProfileRepository = $registrantProfileRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $teamId
     * @param string $programRegistrationId
     * @param int $page
     * @param int $pageSize
     * @return RegistrantProfile[]
     */
    public function showAll(string $firmId, string $teamId, string $programRegistrationId, int $page, int $pageSize)
    {
        return $this->registrantProfileRepository->allRegistrantProfilesBelongsToTeamCorrespondWithProgramsProfileForm(
                        $firmId, $teamId, $programRegistrationId, $page, $pageSize);
    }

    public function showByProgramsProfileFormId(
            string $firmId, string $teamId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        return $this->registrantProfileRepository->aRegistrantProfileBelongsToTeamCorrespondWithProgramsProfileForm(
                        $firmId, $teamId, $programRegistrationId, $programsProfileFormId);
    }

}
