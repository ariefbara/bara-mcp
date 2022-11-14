<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
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
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Participant\ViewAccessibleConsultantNote;
use Query\Domain\Task\Participant\ViewAccessibleCoordinatorNote;
use Query\Domain\Task\Participant\ViewAllAccessibleNotes;
use Query\Domain\Task\Participant\ViewAllAccessibleNotesPayload;
use Query\Domain\Task\Participant\ViewOwnedParticipantNote;
use Resources\PaginationFilter;
use Resources\QueryOrder;

class NoteController extends AsTeamMemberBaseController
{
    public function submit($teamId, $teamProgramParticipationId)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote2::class);
        $task = new SubmitNote($participantNoteRepository);
        
        $content = $this->stripTagsInputRequest('content');
        $payload = new SubmitNotePayload($content);
        
        $this->executeTeamParticipantExtendedTask($teamId, $teamProgramParticipationId, $task, $payload);
        
        $participantNote = $this->executeViewOwnedParticipantNoteTask($teamId, $teamProgramParticipationId, $payload->submittedNoteId);
        return $this->commandCreatedResponse($this->arrayDataOfParticipantNote($participantNote));
    }
    
    public function updateOwnedNote($teamId, $teamProgramParticipationId, $id)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote2::class);
        $task = new UpdateNote($participantNoteRepository);
        
        $content = $this->stripTagsInputRequest('content');
        $payload = new UpdateNotePayload($id, $content);
        
        $this->executeTeamParticipantExtendedTask($teamId, $teamProgramParticipationId, $task, $payload);
        
        $participantNote = $this->executeViewOwnedParticipantNoteTask($teamId, $teamProgramParticipationId, $id);
        return $this->singleQueryResponse($this->arrayDataOfParticipantNote($participantNote));
    }
    
    public function removeOwnedNote($teamId, $teamProgramParticipationId, $id)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote2::class);
        $task = new RemoveNote($participantNoteRepository);
        
        $this->executeTeamParticipantExtendedTask($teamId, $teamProgramParticipationId, $task, $id);
        
        return $this->commandOkResponse();
    }
    
    public function viewOwnedParticipantNote($teamId, $teamProgramParticipationId, $id)
    {
        $participantNote = $this->executeViewOwnedParticipantNoteTask($teamId, $teamProgramParticipationId, $id);
        return $this->singleQueryResponse($this->arrayDataOfParticipantNote($participantNote));
    }
    
    public function viewAccessibleConsultantNote($teamId, $teamProgramParticipationId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewAccessibleConsultantNote($consultantNoteRepository);
        
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeTeamParticipantExtentedQueryTask($teamId, $teamProgramParticipationId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultantNote($payload->result));
    }
    
    public function viewAccessibleCoordinatorNote($teamId, $teamProgramParticipationId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewAccessibleCoordinatorNote($coordinatorNoteRepository);
        
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeTeamParticipantExtentedQueryTask($teamId, $teamProgramParticipationId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinatorNote($payload->result));
    }
    
    public function viewAllAccessibleNotes($teamId, $teamProgramParticipationId)
    {
        $noteRepository = $this->em->getRepository(Note::class);
        $task = new ViewAllAccessibleNotes($noteRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $createdTimeOrder = $this->stripTagQueryRequest('createdTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('createdTimeOrder')) : null;
        $modifiedTimeOrder = $this->stripTagQueryRequest('modifiedTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('modifiedTimeOrder')) : null;
        $noteFilter = (new NoteFilter($paginationFilter))
                ->setCreatedTimeOrder($createdTimeOrder)
                ->setModifiedTimeOrder($modifiedTimeOrder);
        $payload = new ViewAllAccessibleNotesPayload($noteFilter);
        
        $this->executeTeamParticipantExtentedQueryTask($teamId, $teamProgramParticipationId, $task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    protected function executeViewOwnedParticipantNoteTask($teamId, $teamProgramParticipationId, $id): ParticipantNote
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote::class);
        $task = new ViewOwnedParticipantNote($participantNoteRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeTeamParticipantExtentedQueryTask($teamId, $teamProgramParticipationId, $task, $payload);
        
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
            'content' => $note->getContent(),
            'createdTime' => $note->getCreatedTime()->format('Y-m-d H:i:s'),
            'modifiedTime' => $note->getModifiedTime()->format('Y-m-d H:i:s'),
        ];
    }
    
}
