<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User\UserRegistrant;


class ViewProgramRegistration
{

    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;

    public function __construct(ProgramRegistrationRepository $programRegistrationRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
    }

    /**
     * 
     * @param string $userId
     * @param int $page
     * @param int $pageSize
     * @return UserRegistrant[]
     */
    public function showAll(string $userId, int $page, int $pageSize)
    {
        return $this->programRegistrationRepository->allProgramRegistrationsOfUser($userId, $page, $pageSize);
    }

    public function showById(string $userId, string $programRegistrationId): UserRegistrant
    {
        return $this->programRegistrationRepository->aProgramRegistrationOfUser($userId, $programRegistrationId);
    }

}
