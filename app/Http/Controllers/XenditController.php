<?php

namespace App\Http\Controllers;

use Config\EventList;
use ExternalResource\Domain\Task\NotifyInvoiceSettlement;
use ExternalResource\Infrastructure\Xendit\XenditAccount;
use Firm\Application\Listener\ListeningEventsToSettleClientRegistrantPayment;
use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Firm\Domain\Task\Responsive\SettleClientRegistrantInvoicePayment;
use Firm\Infrastructure\Persistence\Doctrine\Repository\DoctrineGenericRepository;
use Resources\Application\Event\AdvanceDispatcher;

class XenditController extends Controller
{
    public function settleInvoice()
    {
        $token = $this->request->header('x-callback-token');
        $xendit = XenditAccount::withToken($token);

        $dispatcher = new AdvanceDispatcher();

        $executeResponsiveTaskService = new ExecuteResponsiveTask(new DoctrineGenericRepository($this->em));
        $settleClientRegistrantInvoicePayment = new SettleClientRegistrantInvoicePayment(
                $this->em->getRepository(ClientRegistrant::class));
        $listener = new ListeningEventsToSettleClientRegistrantPayment(
                $executeResponsiveTaskService, $settleClientRegistrantInvoicePayment);

        $dispatcher->addImmediateListener(EventList::PAYMENT_RECEIVED, $listener);

        $task = new NotifyInvoiceSettlement($dispatcher);
        $invoiceId = $this->request->input('external_id');
        $xendit->executeExternalTask($task, $invoiceId);
        
        return $this->commandOkResponse();
    }
}
