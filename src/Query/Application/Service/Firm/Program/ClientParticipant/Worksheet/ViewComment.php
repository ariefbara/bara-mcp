<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant\Worksheet;

class ViewComment
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function showAll(string $firmId, string $programId, string $clientId, string $worksheetId, int $page,
            int $pageSize)
    {
        return $this->commentRepository->all($firmId, $programId, $clientId,
                        $worksheetId, $page, $pageSize);
    }

}
