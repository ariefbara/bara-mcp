<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Firm\{
    Application\Auth\ManagerAuthorization,
    Domain\Model\Firm\Manager
};
use Illuminate\Http\Request;

class ManagerBaseController extends Controller
{

    public function __construct(EntityManager $em, Request $request)
    {
        parent::__construct($em, $request);
        $this->authorizedUserIsFirmManager();
    }

    protected function authorizedUserIsFirmManager(): void
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $authZ = new ManagerAuthorization($managerRepository);
        $authZ->execute($this->firmId(), $this->managerId());
    }

    protected function firmId()
    {
        return $this->request->firmId;
    }

    protected function managerId()
    {
        return $this->request->managerId;
    }

}
