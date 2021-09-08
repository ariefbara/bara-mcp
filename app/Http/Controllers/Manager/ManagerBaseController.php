<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Query\Application\Auth\Firm\ManagerAuthorization;
use Query\Application\Service\Manager\ExecuteTaskInFirm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Manager;
use Query\Domain\Model\Firm\Program\Coordinator;

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
    
    protected function executeTaskInFirm(ITaskInFirmExecutableByManager $task): void
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        (new ExecuteTaskInFirm($managerRepository))
                ->execute($this->firmId(), $this->managerId(), $task);
    }

}
