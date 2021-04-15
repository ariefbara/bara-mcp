<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use Query\Application\Auth\Firm\AuthorizeUserIsActiveFirmPersonnel;
use Query\Domain\Model\Firm\Personnel;

class PersonnelBaseController extends Controller
{
    protected function firmId()
    {
        return $this->request->firmId;
    }
    protected function personnelId()
    {
        return $this->request->personnelId;
    }
    
    protected function authorizedRequestFromActivePersonnel()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $authZ = new AuthorizeUserIsActiveFirmPersonnel($personnelRepository);
        $authZ->execute($this->firmId(), $this->personnelId());
    }
    
    protected function personnelQueryRepository()
    {
        return $this->em->getRepository(Personnel::class);
    }
}
