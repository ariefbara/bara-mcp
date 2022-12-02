<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Personnel\ViewConsultedProgramList;
use Query\Domain\Task\Personnel\ViewListOfCoordinatedProgram;

class ProgramController extends PersonnelBaseController
{

    public function listOfCoordinatedProgram()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $task = new ViewListOfCoordinatedProgram($programRepository);
        $payload = new CommonViewListPayload();
        $this->executePersonalQueryTask($task, $payload);

        return $this->listQueryResponse($payload->result);
    }

    public function listOfConsultedProgram()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $task = new ViewConsultedProgramList($programRepository);
        $payload = new CommonViewListPayload();
        $this->executePersonalQueryTask($task, $payload);

        return $this->listQueryResponse($payload->result);
    }

}
