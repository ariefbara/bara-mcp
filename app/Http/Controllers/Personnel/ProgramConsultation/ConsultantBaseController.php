<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use Query\Application\Service\Consultant\ExecuteProgramTask;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;

class ConsultantBaseController extends ProgramConsultationBaseController
{

    protected function executeProgramQueryTask($consultantId, ProgramTaskExecutableByConsultant $task, $payload)
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        (new ExecuteProgramTask($consultantRepository))
                ->execute($this->firmId(), $this->personnelId(), $consultantId, $task, $payload);
    }

}
