<?php

namespace Notification\Application\Service;

class CreatePersonnelResetPasswordMail
{

    /**
     *
     * @var PersonnelMailRepository
     */
    protected $personnelMailRepository;

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;
    
    function __construct(PersonnelMailRepository $personnelMailRepository, PersonnelRepository $personnelRepository)
    {
        $this->personnelMailRepository = $personnelMailRepository;
        $this->personnelRepository = $personnelRepository;
    }
    
    public function execute(string $personnelId): void
    {
        $id = $this->personnelMailRepository->nextIdentity();
        $personnelMail = $this->personnelRepository->ofId($personnelId)->createResetPasswordMail($id);
        $this->personnelMailRepository->add($personnelMail);
    }


}
