<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteRepository;

class ViewAllOwnedConsultantNote implements PersonnelTask
{

    /**
     * 
     * @var ConsultantNoteRepository
     */
    protected $consultantNoteRepository;

    public function __construct(ConsultantNoteRepository $consultantNoteRepository)
    {
        $this->consultantNoteRepository = $consultantNoteRepository;
    }
    
    /**
     * 
     * @param string $personnelId
     * @param ViewAllOwnedConsultantNotePayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->consultantNoteRepository->allConsultantNotesBelongsToPersonnel($personnelId, $payload->getConsultantNoteFilter());
    }

}
