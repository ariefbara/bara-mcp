<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
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

    public function submit($consultantId)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new SubmitNote($consultantNoteRepository, $participantRepository);

        $participantId = $this->stripTagsInputRequest('participantId');
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $viewableByParticipant = $this->filterBooleanOfInputRequest('viewableByParticipant');
        $payload = new SubmitNotePayload($participantId, $labelData, $viewableByParticipant);

        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $payload);

        $consultantNoteQueryRepository = $this->em->getRepository(ConsultantNote::class);
        $queryTask = new ViewConsultantNote($consultantNoteQueryRepository);
        $queryPayload = new CommonViewDetailPayload($payload->submittedNoteId);

        $this->executeProgramQueryTaskAsConsultant($consultantId, $queryTask, $queryPayload);
        return $this->commandCreatedResponse($this->arrayDataOfConsultantNote($queryPayload->result));
    }

    public function update($consultantId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new UpdateNote($consultantNoteRepository);

        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new UpdateNotePayload($id, $labelData);

        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $payload);

        return $this->viewConsultantNoteDetail($consultantId, $id);
    }
    
    public function showToParticipant($consultantId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new ShowNoteToParticipant($consultantNoteRepository);
        
        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $id);

        return $this->viewConsultantNoteDetail($consultantId, $id);
    }
    
    public function hideFromParticipant($consultantId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new HideNoteFromParticipant($consultantNoteRepository);

        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $id);

        return $this->viewConsultantNoteDetail($consultantId, $id);
    }

    public function remove($consultantId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote2::class);
        $task = new RemoveNote($consultantNoteRepository);

        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $id);

        return $this->commandOkResponse();
    }

    public function viewCoordinatorNoteDetail($consultantId, $id)
    {
        $coordinatorNoteRepository = $this->em->getRepository(CoordinatorNote::class);
        $task = new ViewCoordinatorNote($coordinatorNoteRepository);
        $payload = new CommonViewDetailPayload($id);

        $this->executeProgramQueryTaskAsConsultant($consultantId, $task, $payload);

        return $this->singleQueryResponse($this->arrayDataOfCoordinatorNote($payload->result));
    }

    public function viewConsultantNoteDetail($consultantId, $id)
    {
        $consultantNoteRepository = $this->em->getRepository(ConsultantNote::class);
        $task = new ViewConsultantNote($consultantNoteRepository);
        $payload = new CommonViewDetailPayload($id);

        $this->executeProgramQueryTaskAsConsultant($consultantId, $task, $payload);

        return $this->singleQueryResponse($this->arrayDataOfConsultantNote($payload->result));
    }

    public function viewParticipantNoteDetail($consultantId, $id)
    {
        $participantNoteRepository = $this->em->getRepository(ParticipantNote::class);
        $task = new ViewParticipantNote($participantNoteRepository);
        $payload = new CommonViewDetailPayload($id);

        $this->executeProgramQueryTaskAsConsultant($consultantId, $task, $payload);

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
