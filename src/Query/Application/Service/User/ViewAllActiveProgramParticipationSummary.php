<?php

namespace Query\Application\Service\User;

use Query\Application\Service\UserRepository;
use Query\Domain\Service\DataFinder;

class ViewAllActiveProgramParticipationSummary
{

    /**
     * 
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * 
     * @var DataFinder
     */
    protected $dataFinder;

    public function __construct(UserRepository $userRepository, DataFinder $dataFinder)
    {
        $this->userRepository = $userRepository;
        $this->dataFinder = $dataFinder;
    }

    public function execute(string $userId, int $page, int $pageSize): array
    {
        return $this->userRepository->ofId($userId)
                        ->viewAllActiveProgramParticipationSummary($this->dataFinder, $page, $pageSize);
    }

}
