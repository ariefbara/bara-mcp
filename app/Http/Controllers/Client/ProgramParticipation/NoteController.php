<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Participant\Domain\Model\Participant\ParticipantNote as ParticipantNote2;
use Participant\Domain\Task\Participant\RemoveNote;
use Participant\Domain\Task\Participant\SubmitNote;
use Participant\Domain\Task\Participant\SubmitNotePayload;
use Participant\Domain\Task\Participant\UpdateNote;
use Participant\Domain\Task\Participant\UpdateNotePayload;
use Query\Domain\Model\Firm\Personnel\Consultant\ConsultantNote;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNote;
use Query\Domain\Model\Firm\Program\Participant\ParticipantNote;
use Query\Domain\SharedModel\Note;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Participant\ViewAccessibleConsultantNote;
use Query\Domain\Task\Participant\ViewAccessibleCoordinatorNote;
use Query\Domain\Task\Participant\ViewAllAccessibleNotes;
use Query\Domain\Task\Participant\ViewOwnedParticipantNote;
use Resources\PaginationFilter;
use SharedContext\Domain\ValueObject\LabelData;

class NoteController extends ClientParticipantBaseController
{
    public function submit($programParticipationId)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote2::class);
        $task = new SubmitNote($participantNoteRepository);
        
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new SubmitNotePayload($labelData);
        
        $this->executeClientParticipantTask($programParticipationId, $task, $payload);
        
        $participantNote = $this->executeViewOwnedParticipantNoteTask($programParticipationId, $payload->submittedNoteId);
        return $this->commandCreatedResponse($this->arrayDataOfParticipantNote($participantNote));
    }
    
    public function updateOwnedNote($programParticipationId, $id)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote2::class);
        $task = new UpdateNote($participantNoteRepository);
        
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new UpdateNotePayload($id, $labelData);
        
        $this->executeClientParticipantTask($programParticipationId, $task, $payload);
        
        $participantNote = $this->executeViewOwnedParticipantNoteTask($programParticipationId, $id);
        return $this->singleQueryResponse($this->arrayDataOfParticipantNote($participantNote));
    }
    
    public function removeOwnedNote($programParticipationId, $id)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote2::class);
        $task = new RemoveNote($participantNoteRepository);
        
        $this->executeClientParticipantTask($programParticipationId, $task, $id);
        
        return $this->commandOkResponse();
    }
    
    public function viewOwnedParticipantNote($programParticipationId, $id)
    {
        $participantNote = $this->executeViewOwnedParticipantNoteTask($programParticipationId, $id);
        return $this->singleQueryResponse($this->arrayDataOfParticipantNote($participantNote));
    }
    
    public function viewAccessibleConsultantNote($programParticipationId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewAccessibleConsultantNote($consultantNoteRepository);
        
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultantNote($payload->result));
    }
    
    public function viewAccessibleCoordinatorNote($programParticipationId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewAccessibleCoordinatorNote($coordinatorNoteRepository);
        
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinatorNote($payload->result));
    }
    
    public function viewAllAccessibleNotes($programParticipationId)
    {
        $noteRepository = $this->em->getRepository(Note::class);
        $task = new ViewAllAccessibleNotes($noteRepository);
        
        $from = $this->dateTimeImmutableOfQueryRequest('from');
        $to = $this->dateTimeImmutableOfQueryRequest('to');
        $keyword = $this->stripTagQueryRequest('keyword');
        $source = $this->stripTagQueryRequest('source');
        $order = $this->stripTagQueryRequest('order');

        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());        
        $filter = (new NoteFilter($paginationFilter))
                        ->setFrom($from)
                        ->setTo($to)
                        ->setKeyword($keyword)
                        ->setSource($source)
                        ->setOrder($order);
        
        $payload = new CommonViewListPayload($filter);
        
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    protected function executeViewOwnedParticipantNoteTask($programParticipationId, $id): ParticipantNote
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote::class);
        $task = new ViewOwnedParticipantNote($participantNoteRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $payload->result;
    }
    
    protected function arrayDataOfParticipantNote(ParticipantNote $participantNote): array
    {
        $noteData = $this->arrayDataOfNote($participantNote->getNote());
        $noteData['id'] = $participantNote->getId();
        return $noteData;
    }
    
    protected function arrayDataOfConsultantNote(ConsultantNote $consultantNote): array
    {
        $noteData = $this->arrayDataOfNote($consultantNote->getNote());
        $noteData['id'] = $consultantNote->getId();
        $noteData['consultant'] = [
            'id' => $consultantNote->getConsultant()->getId(),
            'personnel' => [
                'id' => $consultantNote->getConsultant()->getPersonnel()->getId(),
                'name' => $consultantNote->getConsultant()->getPersonnel()->getName(),
            ],
        ];
        return $noteData;
    }
    
    protected function arrayDataOfCoordinatorNote(CoordinatorNote $coordinatorNote): array
    {
        $noteData = $this->arrayDataOfNote($coordinatorNote->getNote());
        $noteData['id'] = $coordinatorNote->getId();
        $noteData['coordinator'] = [
            'id' => $coordinatorNote->getCoordinator()->getId(),
            'personnel' => [
                'id' => $coordinatorNote->getCoordinator()->getPersonnel()->getId(),
                'name' => $coordinatorNote->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
        return $noteData;
    }
    
    protected function arrayDataOfNote(Note $note): array
    {
        return [
            'name' => $note->getLabel()->getName(),
            'description' => $note->getLabel()->getDescription(),
            'createdTime' => $note->getCreatedTime()->format('Y-m-d H:i:s'),
            'modifiedTime' => $note->getModifiedTime()->format('Y-m-d H:i:s'),
        ];
    }
    
}
