<?php

namespace Query\Application\Service;

use Query\Domain\Model\User;

class ViewUser
{

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function showById(string $userId): User
    {
        return $this->userRepository->ofId($userId);
    }

    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return User[]
     */
    public function showAll(int $page, int $pageSize)
    {
        return $this->userRepository->all($page, $pageSize);
    }

}
