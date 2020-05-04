<?php

namespace Query\Application\Auth\Firm\Program;

use Resources\Exception\RegularException;

class ConsultantAuthorization
{

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    function __construct(ConsultantRepository $consultantRepository)
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(string $firmId, string $personnelId, string $programId): void
    {
        if (!$this->consultantRepository->containRecordOfUnremovedConsultantCorrespondWithPersonnel(
                        $firmId, $personnelId, $programId)) {
            $errorDetail = "unauthorized: only program consultant can make this request";
            throw RegularException::unauthorized($errorDetail);
        }
    }

}
