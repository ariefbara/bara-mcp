<?php

namespace App\Http\Controllers\User;

use Participant\ {
    Application\Service\UserQuitParticipation,
    Domain\Model\UserParticipant as UserParticipant2
};
use Query\ {
    Application\Service\User\ViewProgramParticipation,
    Domain\Model\User\UserParticipant
};

class ProgramParticipationController extends UserBaseController
{

    public function quit($programParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->userId(), $programParticipationId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipation = $service->showById($this->userId(), $programParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramParticipation($programParticipation));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $programParticipations = $service->showAll($this->userId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($programParticipations);
        foreach ($programParticipations as $programParticipation) {
            $result['list'][] = $this->arrayDataOfProgramParticipation($programParticipation);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfProgramParticipation(UserParticipant $programParticipation): array
    {
        return [
            "id" => $programParticipation->getId(),
            'program' => [
                'id' => $programParticipation->getProgram()->getId(),
                'name' => $programParticipation->getProgram()->getName(),
                'removed' => $programParticipation->getProgram()->isRemoved(),
                'firm' => [
                    'id' => $programParticipation->getProgram()->getFirm()->getId(),
                    'name' => $programParticipation->getProgram()->getFirm()->getName(),
                ],
            ],
            'enrolledTime' => $programParticipation->getEnrolledTimeString(),
            'active' => $programParticipation->isActive(),
            'note' => $programParticipation->getNote(),
        ];
    }
    protected function buildViewService()
    {
        $programParticipationRepository = $this->em->getRepository(UserParticipant::class);
        return new ViewProgramParticipation($programParticipationRepository);
    }
    protected function buildQuitService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        return new UserQuitParticipation($userParticipantRepository);
    }

}
 