<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\MentoringRepository;

class OwnedMentoringList implements PersonnelTask
{

    /**
     * 
     * @var MentoringRepository
     */
    protected $mentoringRepository;

    public function __construct(MentoringRepository $mentoringRepository)
    {
        $this->mentoringRepository = $mentoringRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->mentoringRepository->mentoringListOwnedOfPersonnel($personnelId, $payload->getFilter());
    }

}
