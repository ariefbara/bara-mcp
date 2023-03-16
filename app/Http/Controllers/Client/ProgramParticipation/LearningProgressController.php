<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Participant\Domain\DependencyModel\Firm\Program\Mission\LearningMaterial;
use Participant\Domain\Model\Participant\LearningProgress as LearningProgress2;
use Participant\Domain\Model\Participant\LearningProgressData;
use Participant\Domain\Task\Participant\LearningProgress\MarkLearningProgressComplete;
use Participant\Domain\Task\Participant\LearningProgress\SubmitLearningProgress;
use Participant\Domain\Task\Participant\LearningProgress\SubmitLearningProgressPayload;
use Participant\Domain\Task\Participant\LearningProgress\UnmarkLearningProgressCompleteStatus;
use Participant\Domain\Task\Participant\LearningProgress\UpdateLearningProgressMark;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial as LearningMaterial2;
use Query\Domain\Model\Firm\Program\Participant\LearningProgress;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Participant\ViewLearningProgressDetail;
use Query\Domain\Task\Participant\ViewLearningProgressList;
use Query\Domain\Task\ViewListPayload;

class LearningProgressController extends ClientParticipantBaseController
{

    protected function repository()
    {
        return $this->em->getRepository(LearningProgress2::class);
    }

    protected function queryRepository()
    {
        return $this->em->getRepository(LearningProgress::class);
    }

    public function submit($programParticipationId)
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $task = new SubmitLearningProgress($learningMaterialRepository);

        $learningProgressData = (new LearningProgressData())
                ->setProgressMark($this->stripTagsInputRequest('progressMark'))
                ->setMarkAsCompleted($this->filterBooleanOfInputRequest('markAsCompleted'));
        $learningProgressData = (new LearningProgressData())
                ->setProgressMark($this->stripTagsInputRequest('progressMark'))
                ->setMarkAsCompleted($this->filterBooleanOfInputRequest('markAsCompleted'));
        $payload = (new SubmitLearningProgressPayload())
                ->setLearningMaterialId($this->request->input('learningMaterialId') ?? null)
                ->setLearningProgressData($learningProgressData);

        $this->executeClientParticipantTask($programParticipationId, $task, $payload);

        return $this->viewDetail($programParticipationId, $learningProgressData->id);
    }

    public function updateProgressMark($programParticipationId, $id)
    {
        $task = new UpdateLearningProgressMark($this->repository());
        $learningProgressData = (new LearningProgressData())
                ->setProgressMark($this->stripTagsInputRequest('progressMark'));
        $learningProgressData->id = $id;
        $this->executeClientParticipantTask($programParticipationId, $task, $learningProgressData);

        return $this->viewDetail($programParticipationId, $id);
    }

    public function markComplete($programParticipationId, $id)
    {
        $task = new MarkLearningProgressComplete($this->repository());
        $this->executeClientParticipantTask($programParticipationId, $task, $id);

        return $this->viewDetail($programParticipationId, $id);
    }

    public function unmarkCompleteStatus($programParticipationId, $id)
    {
        $task = new UnmarkLearningProgressCompleteStatus($this->repository());
        $this->executeClientParticipantTask($programParticipationId, $task, $id);

        return $this->viewDetail($programParticipationId, $id);
    }

    public function viewDetail($programParticipationId, $id)
    {
        $task = new ViewLearningProgressDetail($this->queryRepository());
        $payload = new CommonViewDetailPayload($id);

        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        return $this->singleQueryResponse($this->arrayDataOfLearningProgress($payload->result));
    }

    public function viewList($programParticipationId)
    {
        $task = new ViewLearningProgressList($this->queryRepository());
        $payload = (new ViewListPayload())
                ->setPage($this->getPage())
                ->setPageSize($this->getPageSize());

        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);

        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $learningProgress) {
            $result['list'][] = $this->arrayDataOfLearningProgress($learningProgress);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfLearningProgress(LearningProgress $learningProgress): array
    {
        return [
            'id' => $learningProgress->getId(),
            'lastModifiedTime' => $learningProgress->getLastModifiedTime()->format('Y-m-d H:i:s'),
            'markAsCompleted' => $learningProgress->getMarkAsCompleted(),
            'progressMark' => $learningProgress->getProgressMark(),
            'learningMaterial' => $this->arrayDataOfLearningMaterial($learningProgress->getLearningMaterial()),
        ];
    }

    protected function arrayDataOfLearningMaterial(LearningMaterial2 $learningMaterial): array
    {
        return [
            'id' => $learningMaterial->getId(),
            'name' => $learningMaterial->getName(),
        ];
    }

}
