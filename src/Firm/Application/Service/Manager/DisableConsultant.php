<?php

namespace Firm\Application\Service\Manager;

class DisableConsultant
{

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ConsultantRepository $consultantRepository, ManagerRepository $managerRepository)
    {
        $this->consultantRepository = $consultantRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableConsultant($consultant);
        $this->consultantRepository->update();
    }

}
