<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Firm\Domain\Model\Firm\Program\Consultant as Consultant2;
use Query\Application\Auth\Firm\Program\ConsultantAuthorization;
use Query\Domain\Model\Firm\Program\Consultant;

class AsProgramConsultantBaseController extends PersonnelBaseController
{
    protected function authorizedPersonnelIsProgramConsultant($programId): void
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $authZ = new ConsultantAuthorization($consultantRepository);
        $authZ->execute($this->firmId(), $this->personnelId(), $programId);
    }
    
    protected function consultantFirmRepository()
    {
        return $this->em->getRepository(Consultant2::class);
    }
    protected function consultantQueryRepository()
    {
        return $this->em->getRepository(Consultant::class);
    }
}
