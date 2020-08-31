<?php

namespace User\Application\Service\User;

use User\Application\Service\UserRepository;

class RegisterToProgram
{

    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(
            ProgramRegistrationRepository $programRegistrationRepository, UserRepository $userRepository,
            ProgramRepository $programRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
        $this->userRepository = $userRepository;
        $this->programRepository = $programRepository;
    }
    
    public function execute(string $userId, string $firmId, string $programId): string
    {
        $id = $this->programRegistrationRepository->nextIdentity();
        $program = $this->programRepository->ofId($firmId, $programId);
        $programRegistration = $this->userRepository->ofId($userId)->registerToProgram($id, $program);
        $this->programRegistrationRepository->add($programRegistration);
        return $id;
    }

}
