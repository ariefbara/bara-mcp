<?php

namespace App\Http\Controllers\Personnel;

use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorNote as CoordinatorNote2;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Coordinator\HideNoteFromParticipant;
use Personnel\Domain\Task\Coordinator\RemoveNote;
use Personnel\Domain\Task\Coordinator\ShowNoteToParticipant;
use Personnel\Domain\Task\Coordinator\SubmitNote;
use Personnel\Domain\Task\Coordinator\SubmitNotePayload;
use Personnel\Domain\Task\Coordinator\UpdateNote;
use Personnel\Domain\Task\Coordinator\UpdateNotePayload;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNote;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteFilter;
use Query\Domain\Task\Dependency\NoteFilter;
use Query\Domain\Task\Personnel\ViewAllOwnedCoordinatorNotes;
use Query\Domain\Task\Personnel\ViewAllOwnedCoordinatorNotesPayload;
use Query\Domain\Task\Personnel\ViewOwnedCoordinatorNote;
use Resources\PaginationFilter;
use Resources\QueryOrder;

class CoordinatorNoteController extends PersonnelBaseController
{

    public function submit($coordinatorId)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new SubmitNote($coordinatorNoteRepository, $participantRepository);

        $participantId = $this->stripTagsInputRequest('participantId');
        $content = $this->stripTagsInputRequest('content');
        $viewableByParticipant = $this->stripTagsInputRequest('viewableByParticipant');
        $payload = new SubmitNotePayload($participantId, $content, $viewableByParticipant);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $payload);

        return $this->commandCreatedResponse($this->viewOwnedCoordinatorNote($payload->submittedNoteId));
    }

    public function update($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new UpdateNote($coordinatorNoteRepository);

        $content = $this->stripTagsInputRequest('content');
        $payload = new UpdateNotePayload($id, $content);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $payload);

        return $this->singleQueryResponse($this->viewOwnedCoordinatorNote($id));
    }

    public function showToParticipant($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new ShowNoteToParticipant($coordinatorNoteRepository);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);

        return $this->singleQueryResponse($this->viewOwnedCoordinatorNote($id));
    }

    public function hideFromParticipant($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new HideNoteFromParticipant($coordinatorNoteRepository);

        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);
        
        return $this->singleQueryResponse($this->viewOwnedCoordinatorNote($id));
    }

    public function remove($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new RemoveNote($coordinatorNoteRepository);

        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);
        
        return $this->commandOkResponse();
    }

    public function show($id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewOwnedCoordinatorNote($coordinatorNoteRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executePersonalQueryTask($task, $payload);

        return $this->singleQueryResponse($this->viewOwnedCoordinatorNote($id));
    }

    public function showAll()
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewAllOwnedCoordinatorNotes($coordinatorNoteRepository);

        $modifiedTimeOrder = $this->stripTagQueryRequest('modifiedTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('modifiedTimeOrder')) : null;
        $createdTimeOrder = $this->stripTagQueryRequest('createdTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('createdTimeOrder')) : null;
        $noteFilter = (new NoteFilter())
                ->setModifiedTimeOrder($modifiedTimeOrder)
                ->setCreatedTimeOrder($createdTimeOrder);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $coordinatorNoteFilter = (new CoordinatorNoteFilter($noteFilter, $paginationFilter))
                ->setCoordinatorId($this->stripTagQueryRequest('coordinatorId'));
        $payload = new ViewAllOwnedCoordinatorNotesPayload($coordinatorNoteFilter);
        $this->executePersonalQueryTask($task, $payload);
        
        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $coordinatorNote) {
            $result['list'][] = $this->arrayDataOfCoordinatorNote($coordinatorNote);
        }
        return $this->listQueryResponse($result);
    }

    protected function viewOwnedCoordinatorNote(string $coordinatorNoteId)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewOwnedCoordinatorNote($coordinatorNoteRepository);
        $payload = new CommonViewDetailPayload($coordinatorNoteId);
        $this->executePersonalQueryTask($task, $payload);
        return $this->arrayDataOfCoordinatorNote($payload->result);
    }

    protected function arrayDataOfCoordinatorNote(CoordinatorNote $coordinatorNote): array
    {
        return [
            'id' => $coordinatorNote->getId(),
            'content' => $coordinatorNote->getContent(),
            'createdTime' => $coordinatorNote->getCreatedTime()->format('Y-m-d H:i:s'),
            'modifiedTime' => $coordinatorNote->getModifiedTime()->format('Y-m-d H:i:s'),
            'viewableByParticipant' => $coordinatorNote->isViewableByParticipant(),
            'participant' => [
                'id' => $coordinatorNote->getParticipant()->getId(),
                'client' => $this->arrayDataOfClient($coordinatorNote->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfTeam($coordinatorNote->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfUser($coordinatorNote->getParticipant()->getUserParticipant()),
            ],
            'coordinator' => [
                'id' => $coordinatorNote->getCoordinator()->getId(),
                'program' => [
                    'id' => $coordinatorNote->getCoordinator()->getProgram()->getId(),
                    'name' => $coordinatorNote->getCoordinator()->getProgram()->getName(),
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
