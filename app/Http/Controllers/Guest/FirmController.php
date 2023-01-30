<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Bara\Application\Service\FirmAdd;
use Bara\Domain\Model\Firm as Firm2;
use Bara\Domain\Model\Firm\ManagerData;
use Bara\Domain\Model\FirmData;
use Query\Application\Service\FirmView;
use Query\Domain\Model\Firm;

class FirmController extends Controller
{
    
    private function getFirmData()
    {
        $name = $this->stripTagsInputRequest('name');
        $identifier = $this->stripTagsInputRequest('identifier');
        $whitelableInfo = $this->request->input('whitelableInfo') ?? [];
        $whitelableUrl = $whitelableInfo['url'] ?? null;
        $whitelableMailSenderAddress = $whitelableInfo['mailSenderAddress'] ?? null;
        $whitelableMailSenderName = $whitelableInfo['mailSenderName'] ?? null;
        $sharingPercentage = $this->stripTagsInputRequest('sharingPercentage');
        
        $firmData = new FirmData(
                $name, $identifier, $whitelableUrl, $whitelableMailSenderAddress, $whitelableMailSenderName,
                $sharingPercentage);
        
        $listOfManager = $this->request->input('managers') ?? [];
        foreach ($listOfManager as $managerInput) {
            $firmData->addManager($this->getManagerData($managerInput));
        }
        
        return $firmData;
    }

    private function getManagerData($managerInput)
    {
        $name = $this->stripTagsVariable($managerInput['name']);
        $email = $this->stripTagsVariable($managerInput['email']);
        $password = $this->stripTagsVariable($managerInput['password']);
        $phone = $this->stripTagsVariable($managerInput['phone']);
        return new ManagerData($name, $email, $password, $phone);
    }
    
    public function add()
    {
        $firmRepository = $this->em->getRepository(Firm2::class);
        $service = new FirmAdd($firmRepository);
        $addedFirmId = $service->execute($this->getFirmData());
        
        $firm = $this->buildViewService()->showById($addedFirmId);
        return $this->commandCreatedResponse($this->arrayDataOfFirm($firm));
    }
    
    

    public function show($firmId)
    {
        $service = $this->buildViewService();
        $firm = $service->showById($firmId);
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $firms = $service->showAll($this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($firms);
    }

    protected function arrayDataOfFirm(Firm $firm): array
    {
        $managers = [];
        foreach ($firm->getActiveManagerList() as $manager) {
            $managers[] = [
                'id' => $manager->getId(),
                'name' => $manager->getName(),
                'email' => $manager->getEmail(),
                'phone' => $manager->getPhone(),
            ];
        }
        return [
            "id" => $firm->getId(),
            "name" => $firm->getName(),
            "identifier" => $firm->getIdentifier(),
            "sharingPercentage" => $firm->getSharingPercentage(),
            'whitelableInfo' => [
                "url" => $firm->getWhitelableUrl(),
                "mailSenderAddress" => $firm->getWhitelableMailSenderAddress(),
                "mailSenderName" => $firm->getWhitelableMailSenderName(),
            ],
            'managers' => $managers,
        ];
    }

    protected function buildViewService()
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FirmView($firmRepository);
    }

}
