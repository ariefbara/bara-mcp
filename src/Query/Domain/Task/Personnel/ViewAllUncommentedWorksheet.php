<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetRepository;

class ViewAllUncommentedWorksheet implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     * 
     * @var ViewAllUncommentedWorksheetPayload
     */
    protected $viewAllUncommentedWorksheetPayload;

    public function __construct(
            WorksheetRepository $worksheetRepository,
            ViewAllUncommentedWorksheetPayload $viewAllUncommentedWorksheetPayload)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->viewAllUncommentedWorksheetPayload = $viewAllUncommentedWorksheetPayload;
    }

    public function execute(string $personnelId): void
    {
        $this->viewAllUncommentedWorksheetPayload->result = $this->worksheetRepository->allUncommentedWorksheetCommentableByPersonnel(
                $personnelId, $this->viewAllUncommentedWorksheetPayload->getPage(),
                $this->viewAllUncommentedWorksheetPayload->getPageSize());
    }

}
