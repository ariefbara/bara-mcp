<?php

namespace Query\Application\Service;

use Query\Domain\Model\User;
use Resources\Exception\RegularException;

class UserLogin
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

    public function execute($email, $password): User
    {
        $errorDetail = 'unauthorized: invalid email or password';
        try {
            $user = $this->userRepository->ofEmail($email);
        } catch (RegularException $ex) {
            throw RegularException::unauthorized($errorDetail);
        }

        if (!$user->passwordMatches($password)) {
            throw RegularException::unauthorized($errorDetail);
        }
        if (!$user->isActivated()) {
            $errorDetail = 'unauthorized: account not activated';
            throw RegularException::unauthorized($errorDetail);
        }

        return $user;
    }

}
