<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ViewSummaryOfAllDedidatedMentees implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var ViewSummaryOfAllDedidatedMenteesPayload
     */
    protected $payload;

    public function __construct(ParticipantRepository $participantRepository,
            ViewSummaryOfAllDedidatedMenteesPayload $payload)
    {
        $this->participantRepository = $participantRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): void
    {
        $this->payload->result = [
            'total' => $this->participantRepository->countOfAllParticipantsWithDedicatedMentorCorrespondToPersonnel($personnelId),
            'list' => $this->participantRepository->summaryOfAllParticipantsWithDedicatedMentorCorrespondToPersonnel(
                    $personnelId, $this->payload->getPage(), $this->payload->getPageSize(),
                    $this->payload->getQueryOrder()->getValue())
        ];
    }

}
