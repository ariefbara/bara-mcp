<?php

namespace App\Http\Controllers\Manager;

use Firm\Domain\Model\Firm\FirmFileInfo as FirmFileInfo2;
use Firm\Domain\Task\InFirm\FirmFileInfo\CreateFirmFileInfo;
use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\InFirm\FirmFileInfo\ViewFirmFileInfoDetail;
use Resources\Application\Event\Dispatcher;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Application\Listener\CreateSignedUploadListener;
use SharedContext\Domain\Event\FileInfoCreatedEvent;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class FirmFileInfoController extends ManagerBaseController
{

    protected function repository()
    {
        return $this->em->getRepository(FirmFileInfo2::class);
    }

    protected function queryRepository()
    {
        return $this->em->getRepository(FirmFileInfo::class);
    }

    //
    public function create()
    {
        $dispatcher = new Dispatcher(false);
        $listener = new CreateSignedUploadListener($this->createGoogleStorage());
        $dispatcher->addListener(FileInfoCreatedEvent::EVENT_NAME, $listener);
        
        $task = new CreateFirmFileInfo($this->repository(), $dispatcher);
        
        $name = $this->request->input('name');
        $payload = new FileInfoData($name, null);
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($task, $payload, $dispatcher){
            $this->executeTaskInFirm($task, $payload);
            $dispatcher->execute();
        });
        
        $queryPayload = new CommonViewDetailPayload($payload->id);
        $query = new ViewFirmFileInfoDetail($this->queryRepository());
        $this->executeQueryInFirm($query, $queryPayload);
        
        $response = $this->arrayDataOfFirmFileInfo($queryPayload->result);
        $response['signedUploadUrl'] = $listener->getSignedUploadUrl();
        return $this->commandCreatedResponse($response);
    }
    
    protected function arrayDataOfFirmFileInfo(FirmFileInfo $firmFileInfo): array
    {
        return [
            'id' => $firmFileInfo->getId(),
            'name' => $firmFileInfo->getFileInfo()->getName(),
        ];
    }

}
