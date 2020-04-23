<?php

namespace Firm\Application\Auth;

use Resources\Exception\RegularException;

class ManagerAuthorization
{

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $managerId): void
    {
        if (!$this->managerRepository->containRecordOfId($firmId, $managerId)) {
            $errorDetail = "unauthorized: only firm manager can make this request";
            throw RegularException::unauthorized($errorDetail);
        }
    }

}
