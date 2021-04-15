<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Model\Firm\Program\Registrant;

class ViewRegistrant
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var RegistrantRepository
     */
    protected $registrantRepository;

    public function __construct(PersonnelRepository $personnelRepository, RegistrantRepository $registrantRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->registrantRepository = $registrantRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $concludedStatus
     * @return Registrant[]
     */
    public function showAll(string $firmId, string $personnelId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        return $this->personnelRepository->aPersonnelInFirm($firmId, $personnelId)
                        ->viewAllAccessibleRegistrant($this->registrantRepository, $page, $pageSize, $concludedStatus);
    }

}
