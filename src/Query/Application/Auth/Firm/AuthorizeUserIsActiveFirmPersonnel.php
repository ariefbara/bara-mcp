<?php

namespace Query\Application\Auth\Firm;

use Resources\Exception\RegularException;

class AuthorizeUserIsActiveFirmPersonnel
{

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }
    
    public function execute(string $firmId, string $personnelId): void
    {
        if (!$this->personnelRepository->containRecordOfActivePersonnelInFirm($firmId, $personnelId)) {
            $errorDetail = "forbidden: only active personnel can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
