<?php

namespace Firm\Domain\Task\Responsive;

use Firm\Domain\Model\ResponsiveTask;
use Firm\Domain\Task\Dependency\Firm\Client\ClientRegistrantRepository;

class SettleClientRegistrantInvoicePayment implements ResponsiveTask
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
    
    /**
     * 
     * @param string $payload invoiceId
     * @return void
     */
    public function execute($payload): void
    {
        $this->clientRegistrantRepository->aClientRegistrantOwningInvoiceId($payload)->settleInvoicePayment();
    }

}
