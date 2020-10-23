<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\ {
    Application\Service\Firm\Program\ViewMetric,
    Domain\Model\Firm\Program\Metric
};

class MetricController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $metrics = $service->showAll($programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($metrics);
        foreach ($metrics as $metric) {
            $result["list"][] = $this->arrayDataOfMetric($metric);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($programId, $metricId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $metric = $service->showById($programId, $metricId);
        return $this->arrayDataOfMetric($metric);
    }
    
    protected function arrayDataOfMetric(Metric $metric): array
    {
        return [
            "id" => $metric->getId(),
            "name" => $metric->getName(),
            "description" => $metric->getDescription(),
            "minValue" => $metric->getMinValue(),
            "maxValue" => $metric->getMaxValue(),
            "higherIsBetter" => $metric->getHigherIsBetter(),
        ];
    }
    protected function buildViewService()
    {
        $metricRepository = $this->em->getRepository(Metric::class);
        return new ViewMetric($metricRepository);
    }
}
