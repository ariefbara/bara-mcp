<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\RegistrantRepository;

class ViewAllNewProgramApplicant implements PersonnelTask
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
     * @param string $personnelId
     * @param ViewAllNewProgramApplicantPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->registrantRepository->allNewRegistrantManageableByPersonnel(
                $personnelId, $payload->getSearchFilter(), $payload->getOffsetLimit());
    }

}
