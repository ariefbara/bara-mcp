<?php

namespace Firm\Domain\Task\Responsive;

use Firm\Domain\Model\ResponsiveTask;
use Firm\Domain\Task\Dependency\Firm\Client\ClientRegistrantRepository;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class GenerateInvoiceForClientRegistrant implements ResponsiveTask
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    /**
     * 
     * @var PaymentGateway
     */
    protected $paymentGateway;

    public function __construct(ClientRegistrantRepository $clientRegistrantRepository, PaymentGateway $paymentGateway)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
        $this->paymentGateway = $paymentGateway;
    }
    
    public function execute($payload): void
    {
        $this->clientRegistrantRepository->ofId($payload)->generateInvoice($this->paymentGateway);
    }

}
