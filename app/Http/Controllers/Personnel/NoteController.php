<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\SharedModel\Note;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Dependency\NoteFilterForConsultant;
use Query\Domain\Task\Dependency\NoteFilterForCoordinator;
use Query\Domain\Task\Personnel\ViewNoteListInCoordinatedProgram;
use Query\Domain\Task\Personnel\ViewNoteListInMentoredProgram;
use Resources\PaginationFilter;

class NoteController extends PersonnelBaseController
{

    public function viewTaskListInCoordinatedPrograms()
    {
        $noteRepository = $this->em->getRepository(Note::class);
        $task = new ViewNoteListInCoordinatedProgram($noteRepository);

        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        
        $filter = (new NoteFilterForCoordinator($this->buildNoteFilter()))
                ->setProgramId($programId)
                ->setParticipantId($participantId);
        $payload = new CommonViewListPayload($filter);

        $this->executePersonalQueryTask($task, $payload);

        return $payload->result;
    }

    public function viewTaskListInConsultedPrograms()
    {
        $noteRepository = $this->em->getRepository(Note::class);
        $task = new ViewNoteListInMentoredProgram($noteRepository);

        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        $noteOwnership = $this->stripTagQueryRequest('noteOwnership');

        $filter = (new NoteFilterForConsultant($this->buildNoteFilter()))
                ->setProgramId($programId)
                ->setParticipantId($participantId)
                ->setNoteOwnership($noteOwnership);
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $payload->result;
    }

    protected function buildNoteFilter()
    {
        $from = $this->dateTimeImmutableOfQueryRequest('from');
        $to = $this->dateTimeImmutableOfQueryRequest('to');
        $keyword = $this->stripTagQueryRequest('keyword');
        $source = $this->stripTagQueryRequest('source');
        $order = $this->stripTagQueryRequest('order');

        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());        
        return (new NoteFilter($paginationFilter))
                        ->setFrom($from)
                        ->setTo($to)
                        ->setKeyword($keyword)
                        ->setSource($source)
                        ->setOrder($order);
    }

}
