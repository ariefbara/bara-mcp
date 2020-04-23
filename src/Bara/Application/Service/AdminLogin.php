<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Admin;
use Resources\Exception\RegularException;

class AdminLogin
{

    protected $adminRepository;

    function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function execute(string $email, string $password): Admin
    {
        $errorDetail = 'unauthorized: invalid email or password';

        try {
            $admin = $this->adminRepository->ofEmail($email);
        } catch (RegularException $ex) {
            throw RegularException::unauthorized($errorDetail);
        }

        if (!$admin->passwordMatch($password)) {
            throw RegularException::unauthorized($errorDetail);
        }
        return $admin;
    }

}
