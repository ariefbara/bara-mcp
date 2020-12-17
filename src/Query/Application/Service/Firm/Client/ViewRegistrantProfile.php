<?php

namespace Query\Application\Service\Firm\Client;

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
     * @param string $clientId
     * @param string $programRegistrationId
     * @param int $page
     * @param int $pageSize
     * @return RegistrantProfile[]
     */
    public function showAll(string $firmId, string $clientId, string $programRegistrationId, int $page, int $pageSize)
    {
        return $this->registrantProfileRepository->allRegistrantProfilesInProgramRegistrationBelongsToClient(
                        $firmId, $clientId, $programRegistrationId, $page, $pageSize);
    }

    public function showByProgramsProfileFormId(
            string $firmId, string $clientId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        return $this->registrantProfileRepository
                        ->aRegistrantProfileBelongsToClientCorrespondWithProgramsProfileForm(
                                $firmId, $clientId, $programRegistrationId, $programsProfileFormId);
    }

}
