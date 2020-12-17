<?php

namespace Query\Application\Service\Firm\Program;

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
     * @param string $programId
     * @param string $registrantId
     * @param int $page
     * @param int $pageSize
     * @return RegistrantProfile[]
     */
    public function showAll(string $firmId, string $programId, string $registrantId, int $page, int $pageSize)
    {
        return $this->registrantProfileRepository
                        ->allRegistrantProfilesInBelongsToRegistrant($firmId, $programId, $registrantId, $page,
                                $pageSize);
    }

    public function showById(string $firmId, string $programId, string $registrantProfileId): RegistrantProfile
    {
        return $this->registrantProfileRepository->aRegistrantProfileInProgram($firmId, $programId, $registrantProfileId);
    }

}
