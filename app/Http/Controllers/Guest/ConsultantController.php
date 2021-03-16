<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Query\Application\Service\ViewConsultant;
use Query\Domain\Model\Firm\Program\Consultant;

class ConsultantController extends Controller
{
    public function showAll($programId)
    {
        $consultants = $this->buildViewService()->showAll($programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($consultants);
        foreach ($consultants as $consultant) {
            $result['list'][] = $this->arrayDataOfConsultant($consultant);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($id)
    {
        $consultant = $this->buildViewService()->showById($id);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }
    
    protected function arrayDataOfConsultant(Consultant $consultant): array
    {
        return [
            'id' => $consultant->getId(),
            'personnel' => [
                'id' => $consultant->getPersonnel()->getId(),
                'name' => $consultant->getPersonnel()->getName(),
                'bio' => $consultant->getPersonnel()->getBio(),
            ],
        ];
    }
    
    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new ViewConsultant($consultantRepository);
    }
}
