<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\ConsultationSetup;

class ViewConsultationSetup
{

    /**
     *
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    public function __construct(ConsultationSetupRepository $consultationSetupRepository)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSetup[]
     */
    public function showAll(string $firmId, string $programId, int $page, int $pageSize)
    {
        return $this->consultationSetupRepository->all($firmId, $programId, $page, $pageSize);
    }

    public function showById(string $firmId, string $programId, string $consultationSetupId): ConsultationSetup
    {
        return $this->consultationSetupRepository->ofId($firmId, $programId, $consultationSetupId);
    }

}
