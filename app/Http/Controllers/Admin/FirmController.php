<?php

namespace App\Http\Controllers\Admin;

use Bara\{
    Application\Service\FirmAdd,
    Application\Service\FirmSuspend,
    Domain\Model\Firm,
    Domain\Model\Firm\ManagerData,
    Domain\Model\FirmData
};
use Query\{
    Application\Service\FirmView,
    Domain\Model\Firm as QueryFirm
};

class FirmController extends AdminBaseController
{

    public function add()
    {
        $this->authorizeUserIsAdmin();

        $service = $this->buildAddService();
        $firmId = $service->execute($this->getFirmData(), $this->getManagerData());

        $viewService = $this->buildViewService();
        $firm = $viewService->showById($firmId);
        return $this->commandCreatedResponse($this->arrayDataOfFirm($firm));
    }

    public function suspend($firmId)
    {
        $this->authorizeUserIsAdmin();

        $service = $this->buildSuspendService();
        $service->execute($firmId);
        return $this->commandOkResponse();
    }

    public function show($firmId)
    {
        $this->authorizeUserIsAdmin();

        $service = $this->buildViewService();
        $firm = $service->showById($firmId);
        return $this->singleQueryResponse($this->arrayDataOfFirm($firm));
    }

    public function showAll()
    {
        $this->authorizeUserIsAdmin();

        $service = $this->buildViewService();
        $firms = $service->showAll($this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($firms);
    }

    private function getFirmData()
    {
        $name = $this->stripTagsInputRequest('name');
        $identifier = $this->stripTagsInputRequest('identifier');
        $whitelableUrl = urldecode($this->stripTagsInputRequest('whitelableUrl'));
        $whitelableMailSenderAddress = $this->stripTagsInputRequest('whitelableMailSenderAddress');
        $whitelableMailSenderName = $this->stripTagsInputRequest('whitelableMailSenderName');
        $sharingPercentage = $this->stripTagsInputRequest('sharingPercentage');
        return new FirmData(
                $name, $identifier, $whitelableUrl, $whitelableMailSenderAddress, $whitelableMailSenderName,
                $sharingPercentage);
    }

    private function getManagerData()
    {
        $name = $this->stripTagsVariable($this->request->input("manager")['name']);
        $email = $this->stripTagsVariable($this->request->input("manager")['email']);
        $password = $this->stripTagsVariable($this->request->input("manager")['password']);
        $phone = $this->stripTagsVariable($this->request->input("manager")['phone']);
        return new ManagerData($name, $email, $password, $phone);
    }

    private function arrayDataOfFirm(QueryFirm $firm)
    {
        return [
            "id" => $firm->getId(),
            "name" => $firm->getName(),
            "identifier" => $firm->getIdentifier(),
            'whitelableUrl' => $firm->getWhitelableUrl(),
            'whitelableMailSenderAddress' => $firm->getWhitelableMailSenderAddress(),
            'whitelableMailSenderName' => $firm->getWhitelableMailSenderName(),
            'sharingPercentage' => $firm->getSharingPercentage(),
        ];
    }

    private function buildAddService()
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FirmAdd($firmRepository);
    }

    private function buildSuspendService()
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FirmSuspend($firmRepository);
    }

    private function buildViewService()
    {
        $firmRepository = $this->em->getRepository(QueryFirm::class);
        return new FirmView($firmRepository);
    }

}
