<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\MentorAssign,
    Application\Service\Firm\Program\MentorRemove,
    Application\Service\Firm\Program\MentorView,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Mentor
};

class MentorController extends ManagerBaseController
{

    public function assign($programId)
    {
        $service = $this->buildAssignService();
        $personnelId = $this->stripTagsInputRequest('personnelId');
        $mentor = $service->execute($this->firmId(), $programId, $personnelId);
        
        return $this->singleQueryResponse($this->arrayDataOfMentor($mentor));
    }

    public function remove($programId, $mentorId)
    {
        $service = $this->buildRemoveService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $service->execute($programCompositionId, $mentorId);
        return $this->commandOkResponse();
    }

    public function show($programId, $mentorId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $mentor = $service->showById($programCompositionId, $mentorId);

        return $this->singleQueryResponse($this->arrayDataOfMentor($mentor));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $mentors = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        $result = [];
        $result['total'] = count($mentors);
        foreach ($mentors as $mentor) {
            $result['list'][] = $this->arrayDataOfMentor($mentor);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfMentor(Mentor $mentor)
    {
        return [
            "id" => $mentor->getId(),
            "personnel" => [
                "id" => $mentor->getPersonnel()->getId(),
                "name" => $mentor->getPersonnel()->getName(),
            ],
        ];
    }

    protected function buildAssignService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $personnelRepository = $this->em->getRepository(Personnel::class);

        return new MentorAssign($programRepository, $personnelRepository);
    }

    protected function buildRemoveService()
    {
        $mentorRepository = $this->em->getRepository(Mentor::class);
        return new MentorRemove($mentorRepository);
    }

    protected function buildViewService()
    {
        $mentorRepository = $this->em->getRepository(Mentor::class);
        return new MentorView($mentorRepository);
    }

}
