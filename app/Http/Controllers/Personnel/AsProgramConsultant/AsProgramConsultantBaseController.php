<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\ {
    Application\Auth\Firm\Program\ConsultantAuthorization,
    Domain\Model\Firm\Program\Consultant
};

class AsProgramConsultantBaseController extends PersonnelBaseController
{
    protected function authorizedPersonnelIsProgramConsultant($programId): void
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $authZ = new ConsultantAuthorization($consultantRepository);
        $authZ->execute($this->firmId(), $this->personnelId(), $programId);
    }
}
