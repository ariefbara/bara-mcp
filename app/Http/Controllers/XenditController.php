<?php

namespace App\Http\Controllers;

use Config\EventList;
use ExternalResource\Domain\Task\NotifyInvoiceSettlement;
use ExternalResource\Infrastructure\Xendit\XenditAccount;
use Firm\Application\Listener\SettleClientRegistrantInvoicePayment;
use Firm\Application\Listener\SettleTeamRegistrantInvoicePayment;
use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Resources\Application\Event\AdvanceDispatcher;

class XenditController extends Controller
{
    public function settleInvoice()
    {
        $token = $this->request->header('x-callback-token');
        $xendit = XenditAccount::withToken($token);

        $dispatcher = new AdvanceDispatcher();
        
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant::class);
        $settleClientRegistrantInvoicePayment = new SettleClientRegistrantInvoicePayment($clientRegistrantRepository);
        $dispatcher->addImmediateListener(EventList::PAYMENT_RECEIVED, $settleClientRegistrantInvoicePayment);
        
        $teamRegistrantRepository = $this->em->getRepository(TeamRegistrant::class);
        $settleTeamRegistrantInvoicePayment = new SettleTeamRegistrantInvoicePayment($teamRegistrantRepository);
        $dispatcher->addImmediateListener(EventList::PAYMENT_RECEIVED, $settleTeamRegistrantInvoicePayment);

        $task = new NotifyInvoiceSettlement($dispatcher);
        $invoiceId = $this->request->input('external_id');
        $xendit->executeExternalTask($task, $invoiceId);
        
        return $this->commandOkResponse();
    }
}
