<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Query\Application\Auth\Firm\ManagerAuthorization;
use Query\Application\Service\Manager\ExecuteTaskInFirm;
use Query\Application\Service\Manager\ExecuteTaskInProgram;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Query\Domain\Model\Firm\Manager;
use Query\Domain\Model\Firm\Program;

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

    protected function executeFirmQueryTask(ITaskInFirmExecutableByManager $task): void
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        (new ExecuteTaskInFirm($managerRepository))
                ->execute($this->firmId(), $this->managerId(), $task);
    }

    protected function executeQueryTaskInProgram(string $programId, ITaskInProgramExecutableByManager $task): void
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $programRepository = $this->em->getRepository(Program::class);
        (new ExecuteTaskInProgram($managerRepository, $programRepository))
                ->execute($this->firmId(), $this->managerId(), $programId, $task);
    }

    protected function executeCommandTaskInProgramOfFirmContext(string $programId,
            \Firm\Domain\Model\Firm\ITaskInProgramExecutableByManager $task): void
    {
        $managerRepository = $this->em->getRepository(\Firm\Domain\Model\Firm\Manager::class);
        $programRepository = $this->em->getRepository(\Firm\Domain\Model\Firm\Program::class);
        (new \Firm\Application\Service\Manager\ExecuteTaskInProgram($managerRepository, $programRepository))
                ->execute($this->firmId(), $this->managerId(), $programId, $task);
    }

    protected function executeFirmTaskExecutableByManager(\Firm\Domain\Model\Firm\FirmTaskExecutableByManager $task): void
    {
        $managerRepository = $this->em->getRepository(\Firm\Domain\Model\Firm\Manager::class);
        (new \Firm\Application\Service\Manager\ExecuteFirmTask($managerRepository))
                ->execute($this->firmId(), $this->managerId(), $task);
    }

}
