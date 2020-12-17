<?php

namespace Query\Application\Service\User;

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
     * @param string $userId
     * @param string $programRegistrationId
     * @param int $page
     * @param int $pageSize
     * @return RegistrantProfile[]
     */
    public function showAll(string $userId, string $programRegistrationId, int $page, int $pageSize)
    {
        return $this->registrantProfileRepository
                        ->allRegistrantProfilesBelongsToUser($userId, $programRegistrationId, $page, $pageSize);
    }

    public function showByProgramsProfileFormId(
            string $userId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        return $this->registrantProfileRepository->aRegistrantProfileBelongsToUserCorrespondWithProgramsProfileForm(
                        $userId, $programRegistrationId, $programsProfileFormId);
    }

}
