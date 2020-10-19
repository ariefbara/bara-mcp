<?php

namespace Query\Application\Auth\Firm\Program;

use Resources\Exception\RegularException;

class CoordinatorAuthorization
{

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    function __construct(CoordinatorRepository $coordinatorRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(string $firmId, string $personnelId, string $programId): void
    {
        if (!$this->coordinatorRepository->containRecordOfUnremovedCoordinatorCorrespondWithPersonnel(
                        $firmId, $personnelId, $programId)) {
            $errorDetail = "forbidden: only program coordinator allow to make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
