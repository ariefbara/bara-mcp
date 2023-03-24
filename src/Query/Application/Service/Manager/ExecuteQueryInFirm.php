<?php

namespace Query\Application\Service\Manager;

class ExecuteQueryInFirm
{

    protected ManagerRepository $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $managerId, $query, $payload): void
    {
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->executeQueryInFirm($query, $payload);
    }

}
