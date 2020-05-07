<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Query\ {
    Application\Service\FirmView,
    Domain\Model\Firm
};

class FirmController extends Controller
{

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
        return [
            "id" => $firm->getId(),
            "name" => $firm->getName(),
        ];
    }

    protected function buildViewService()
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        return new FirmView($firmRepository);
    }

}
