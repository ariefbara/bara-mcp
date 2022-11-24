<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteRepository;

class ViewOwnedConsultantNote implements PersonnelTask
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
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->consultantNoteRepository
                ->aConsultantNoteBelongsToPersonnel($personnelId, $payload->getId());
    }

}
