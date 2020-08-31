<?php

namespace Firm\Application\Service\Firm\Program;

class RejectUserRegistration
{
    /**
     *
     * @var UserRegistrantRepository
     */
    protected $userRegistrantRepository;
    
    public function __construct(UserRegistrantRepository $userRegistrantRepository)
    {
        $this->userRegistrantRepository = $userRegistrantRepository;
    }
    
    public function execute(string $firmId, string $programId, string $userRegistrantId): void
    {
        $this->userRegistrantRepository->ofId($firmId, $programId, $userRegistrantId)->reject();
        $this->userRegistrantRepository->update();
    }

}
