<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Registrant;

class ViewRegistrant
{

    /**
     *
     * @var RegistrantRepository
     */
    protected $registrantRepository;

    public function __construct(RegistrantRepository $registrantRepository)
    {
        $this->registrantRepository = $registrantRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return Registrant[]
     */
    public function showAll(string $firmId, string $programId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        return $this->registrantRepository->all($firmId, $programId, $page, $pageSize, $concludedStatus);
    }

    public function showById(string $firmId, string $programId, string $registrantId): Registrant
    {
        return $this->registrantRepository->ofId($firmId, $programId, $registrantId);
    }

}
