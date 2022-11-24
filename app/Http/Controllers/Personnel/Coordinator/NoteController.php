<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
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
use Query\Domain\Model\Firm\Personnel\Consultant\ConsultantNote;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNote;
use Query\Domain\Model\Firm\Program\Participant as Participant2;
use Query\Domain\Model\Firm\Program\Participant\ParticipantNote;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\SharedModel\Note;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\InProgram\ViewConsultantNote;
use Query\Domain\Task\InProgram\ViewCoordinatorNote;
use Query\Domain\Task\InProgram\ViewParticipantNote;
use SharedContext\Domain\ValueObject\LabelData;

class NoteController extends PersonnelBaseController
{

    public function submit($coordinatorId)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new SubmitNote($coordinatorNoteRepository, $participantRepository);

        $participantId = $this->stripTagsInputRequest('participantId');
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $viewableByParticipant = $this->filterBooleanOfInputRequest('viewableByParticipant');
        $payload = new SubmitNotePayload($participantId, $labelData, $viewableByParticipant);

        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $payload);

        $coordinatorNoteQueryRepository = $this->em->getRepository(CoordinatorNote::class);
        $queryTask = new ViewCoordinatorNote($coordinatorNoteQueryRepository);
        $queryPayload = new CommonViewDetailPayload($payload->submittedNoteId);

        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $queryTask, $queryPayload);
        return $this->commandCreatedResponse($this->arrayDataOfCoordinatorNote($queryPayload->result));
    }

    public function update($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new UpdateNote($coordinatorNoteRepository);

        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new UpdateNotePayload($id, $labelData);

        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $payload);

        return $this->viewCoordinatorNoteDetail($coordinatorId, $id);
    }
    
    public function showToParticipant($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new ShowNoteToParticipant($coordinatorNoteRepository);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);

        return $this->viewCoordinatorNoteDetail($coordinatorId, $id);
    }
    
    public function hideFromParticipant($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new HideNoteFromParticipant($coordinatorNoteRepository);

        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);

        return $this->viewCoordinatorNoteDetail($coordinatorId, $id);
    }

    public function remove($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote2::class);
        $task = new RemoveNote($coordinatorNoteRepository);

        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);

        return $this->commandOkResponse();
    }

    public function viewCoordinatorNoteDetail($coordinatorId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewCoordinatorNote($coordinatorNoteRepository);
        $payload = new CommonViewDetailPayload($id);

        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);

        return $this->singleQueryResponse($this->arrayDataOfCoordinatorNote($payload->result));
    }

    public function viewConsultantNoteDetail($coordinatorId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewConsultantNote($consultantNoteRepository);
        $payload = new CommonViewDetailPayload($id);

        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);

        return $this->singleQueryResponse($this->arrayDataOfConsultantNote($payload->result));
    }

    public function viewParticipantNoteDetail($coordinatorId, $id)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote::class);
        $task = new ViewParticipantNote($participantNoteRepository);
        $payload = new CommonViewDetailPayload($id);

        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);

        return $this->singleQueryResponse($this->arrayDataOfParticipantNote($payload->result));
    }
    //
    protected function arrayDataOfCoordinatorNote(CoordinatorNote $coordinatorNote): array
    {
        return array_merge($this->arrayDataOfNote($coordinatorNote->getNote()),
                [
            'coordinator' => [
                'id' => $coordinatorNote->getCoordinator()->getId(),
                'personnel' => [
                    'id' => $coordinatorNote->getCoordinator()->getPersonnel()->getId(),
                    'name' => $coordinatorNote->getCoordinator()->getPersonnel()->getName(),
                ],
            ],
            'participant' => $this->arrayDataOfParticipant($coordinatorNote->getParticipant()),
            'viewableByParticipant' => $coordinatorNote->isViewableByParticipant(),
        ]);
    }
    protected function arrayDataOfConsultantNote(ConsultantNote $consultantNote): array
    {
        return array_merge($this->arrayDataOfNote($consultantNote->getNote()),
                [
            'consultant' => [
                'id' => $consultantNote->getConsultant()->getId(),
                'personnel' => [
                    'id' => $consultantNote->getConsultant()->getPersonnel()->getId(),
                    'name' => $consultantNote->getConsultant()->getPersonnel()->getName(),
                ],
            ],
            'participant' => $this->arrayDataOfParticipant($consultantNote->getParticipant()),
            'viewableByParticipant' => $consultantNote->isViewableByParticipant(),
        ]);
    }
    protected function arrayDataOfParticipantNote(ParticipantNote $participantNote): array
    {
        return array_merge($this->arrayDataOfNote($participantNote->getNote()),
                [
            'participant' => $this->arrayDataOfParticipant($participantNote->getParticipant()),
        ]);
    }
    protected function arrayDataOfNote(Note $note): array
    {
        return [
            'id' => $note->getId(),
            'name' => $note->getLabel()->getName(),
            'description' => $note->getLabel()->getDescription(),
            'createdTime' => $note->getCreatedTime()->format('Y-m-d H:i:s'),
            'modifiedTime' => $note->getModifiedTime()->format('Y-m-d H:i:s'),
        ];
    }
    //
    protected function arrayDataOfParticipant(Participant2 $participant): array
    {
        return [
            'id' => $participant->getId(),
            'client' => $this->arrayDataOfClient($participant->getClientParticipant()),
            'team' => $this->arrayDataOfTeam($participant->getTeamParticipant()),
            'user' => $this->arrayDataOfUser($participant->getUserParticipant()),
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
