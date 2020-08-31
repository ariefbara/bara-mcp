<?php

namespace Firm\Application\Service\Firm\Program;

class RejectClientRegistration
{
    /**
     *
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;
    
    public function __construct(ClientRegistrantRepository $clientRegistrantRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
    }
    
    public function execute(string $firmId, string $programId, string $clientRegistrantId): void
    {
        $this->clientRegistrantRepository->ofId($firmId, $programId, $clientRegistrantId)->reject();
        $this->clientRegistrantRepository->update();
    }

}
