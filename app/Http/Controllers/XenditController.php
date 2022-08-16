<?php

namespace App\Http\Controllers;

use Config\EventList;
use Payment\Application\Listener\SettleParticipantInvoice;
use Payment\Application\Service\PaymentGatewayAccount\ExecuteTask;
use Payment\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use Payment\Domain\Task\PaymentGatewayAccount\SettleInvoice;
use Payment\Infrastructure\Xendit\XenditAccountRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;

//use Config\EventList;
//use ExternalResource\Domain\Task\NotifyInvoiceSettlement;
//use Firm\Application\Listener\ListeningEventsToSettleClientRegistrantPayment;
//use Firm\Application\Service\ExecuteResponsiveTask;
//use Firm\Domain\Model\Firm\Client\ClientRegistrant;
//use Firm\Domain\Task\Responsive\SettleClientRegistrantInvoicePayment;
//use Firm\Infrastructure\Persistence\Doctrine\Repository\DoctrineGenericRepository;
//use Resources\Application\Event\AdvanceDispatcher;

class XenditController extends Controller
{
    public function settleInvoice()
    {
//        $xendit = XenditAccount::withToken($token);
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        
        $dispatcher = new AdvanceDispatcher();

        $participantInvoiceRepository = $this->em->getRepository(ParticipantInvoice::class);
        $settleParticipantInvoice = new SettleParticipantInvoice($participantInvoiceRepository);
        $dispatcher->addPostponedListener(EventList::INVOICE_SETTLED, $settleParticipantInvoice);

        $paymentGatewayAccountRepository = new XenditAccountRepository();
        $service = new ExecuteTask($paymentGatewayAccountRepository, $this->em);

        
        $transactionalSession->executeAtomically(function () use ($service, $dispatcher) {
            $token = $this->request->header('x-callback-token');
            $task = new SettleInvoice($dispatcher);
            $payload = $this->request->input('external_id');
            $service->execute($token, $task, $payload);
            
            $dispatcher->finalize();
        });
        
        return $this->commandOkResponse();
//
//
//        $executeResponsiveTaskService = new ExecuteResponsiveTask(new DoctrineGenericRepository($this->em));
//        $settleClientRegistrantInvoicePayment = new SettleClientRegistrantInvoicePayment(
//                $this->em->getRepository(ClientRegistrant::class));
//        $listener = new ListeningEventsToSettleClientRegistrantPayment(
//                $executeResponsiveTaskService, $settleClientRegistrantInvoicePayment);
//
//        $dispatcher->addImmediateListener(EventList::PAYMENT_RECEIVED, $listener);
//
//        $task = new NotifyInvoiceSettlement($dispatcher);
//        $invoiceId = $this->request->input('external_id');
//        $xendit->executeExternalTask($task, $invoiceId);
//        
//        return $this->commandOkResponse();
    }
}
