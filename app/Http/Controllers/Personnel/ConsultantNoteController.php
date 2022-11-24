<?php

namespace App\Http\Controllers\Personnel;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantNote as ConsultantNote2;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Mentor\HideNoteFromParticipant;
use Personnel\Domain\Task\Mentor\RemoveNote;
use Personnel\Domain\Task\Mentor\ShowNoteToParticipant;
use Personnel\Domain\Task\Mentor\SubmitNote;
use Personnel\Domain\Task\Mentor\SubmitNotePayload;
use Personnel\Domain\Task\Mentor\UpdateNote;
use Personnel\Domain\Task\Mentor\UpdateNotePayload;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\Firm\Personnel\Consultant\ConsultantNote;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteFilter;
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Personnel\ViewAllOwnedConsultantNote;
use Query\Domain\Task\Personnel\ViewAllOwnedConsultantNotePayload;
use Query\Domain\Task\Personnel\ViewOwnedConsultantNote;
use Resources\PaginationFilter;
use Resources\QueryOrder;

class ConsultantNoteController extends PersonnelBaseController
{

    public function submit($mentorId)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new SubmitNote($consultantNoteRepository, $participantRepository);

        $participantId = $this->stripTagsInputRequest('participantId');
        $content = $this->stripTagsInputRequest('content');
        $viewableByParticipant = $this->stripTagsInputRequest('viewableByParticipant');
        $payload = new SubmitNotePayload($participantId, $content, $viewableByParticipant);
        $this->executeExtendedMentorTaskInPersonnelContext($mentorId, $task, $payload);

        return $this->commandCreatedResponse($this->viewOwnedConsultantNote($payload->submittedNoteId));
    }

    public function update($mentorId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new UpdateNote($consultantNoteRepository);

        $content = $this->stripTagsInputRequest('content');
        $payload = new UpdateNotePayload($id, $content);
        $this->executeExtendedMentorTaskInPersonnelContext($mentorId, $task, $payload);

        return $this->singleQueryResponse($this->viewOwnedConsultantNote($id));
    }

    public function showToParticipant($mentorId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new ShowNoteToParticipant($consultantNoteRepository);
        $this->executeExtendedMentorTaskInPersonnelContext($mentorId, $task, $id);

        return $this->singleQueryResponse($this->viewOwnedConsultantNote($id));
    }

    public function hideFromParticipant($mentorId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new HideNoteFromParticipant($consultantNoteRepository);
        $this->executeExtendedMentorTaskInPersonnelContext($mentorId, $task, $id);

        return $this->singleQueryResponse($this->viewOwnedConsultantNote($id));
    }

    public function remove($mentorId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new RemoveNote($consultantNoteRepository);
        $this->executeExtendedMentorTaskInPersonnelContext($mentorId, $task, $id);

        return $this->commandOkResponse();
    }

    public function show($id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewOwnedConsultantNote($consultantNoteRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executePersonalQueryTask($task, $payload);

        return $this->singleQueryResponse($this->viewOwnedConsultantNote($id));
    }

    public function showAll()
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewAllOwnedConsultantNote($consultantNoteRepository);

        $createdTimeOrder = $this->stripTagQueryRequest('createdTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('createdTimeOrder')) : null;
        $modifiedTimeOrder = $this->stripTagQueryRequest('modifiedTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('modifiedTimeOrder')) : null;
        $noteFilter = (new NoteFilter())
                ->setCreatedTimeOrder($createdTimeOrder)
                ->setModifiedTimeOrder($modifiedTimeOrder);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $consultantNoteFilter = (new ConsultantNoteFilter($noteFilter, $paginationFilter))
                ->setConsultantId($this->stripTagQueryRequest('consultantId'));
        $payload = new ViewAllOwnedConsultantNotePayload($consultantNoteFilter);
        $this->executePersonalQueryTask($task, $payload);
        
        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $consultantNote) {
            $result['list'][] = $this->arrayDataOfConsultantNote($consultantNote);
        }
        return $this->listQueryResponse($result);
    }

    protected function viewOwnedConsultantNote(string $consultantNoteId)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewOwnedConsultantNote($consultantNoteRepository);
        $payload = new CommonViewDetailPayload($consultantNoteId);
        $this->executePersonalQueryTask($task, $payload);
        return $this->arrayDataOfConsultantNote($payload->result);
    }

    protected function arrayDataOfConsultantNote(ConsultantNote $consultantNote): array
    {
        return [
            'id' => $consultantNote->getId(),
            'content' => $consultantNote->getContent(),
            'createdTime' => $consultantNote->getCreatedTime()->format('Y-m-d H:i:s'),
            'modifiedTime' => $consultantNote->getModifiedTime()->format('Y-m-d H:i:s'),
            'viewableByParticipant' => $consultantNote->isViewableByParticipant(),
            'participant' => [
                'id' => $consultantNote->getParticipant()->getId(),
                'client' => $this->arrayDataOfClient($consultantNote->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfTeam($consultantNote->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfUser($consultantNote->getParticipant()->getUserParticipant()),
            ],
            'consultant' => [
                'id' => $consultantNote->getConsultant()->getId(),
                'program' => [
                    'id' => $consultantNote->getConsultant()->getProgram()->getId(),
                    'name' => $consultantNote->getConsultant()->getProgram()->getName(),
                ],
            ],
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }
    

}
