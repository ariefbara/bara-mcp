<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\AddMetric,
    Application\Service\Firm\Program\UpdateMetric,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Metric as Metric2
};
use Query\ {
    Application\Service\Firm\Program\ViewMetric,
    Domain\Model\Firm\Program\Metric
};

class MetricController extends ManagerBaseController
{

    public function add($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildAddService();
        $metricId = $service->execute($this->firmId(), $programId, $this->getMetricData());
        
        $viewService = $this->buildViewService();
        $metric = $viewService->showById($programId, $metricId);
        return $this->commandCreatedResponse($this->arrayDataOfMetric($metric));
    }

    public function update($programId, $metricId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUpdateService();
        $service->execute($programId, $metricId, $this->getMetricData());
        return $this->show($programId, $metricId);
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildViewService();
        $metrics = $service->showAll($programId, $this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($metrics);
    }

    public function show($programId, $metricId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildViewService();
        $metric = $service->showById($programId, $metricId);
        return $this->singleQueryResponse($this->arrayDataOfMetric($metric));
    }
    
    protected function getMetricData()
    {
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $minValue = $this->integerOfInputRequest("minValue");
        $maxValue = $this->integerOfInputRequest("maxValue");
        $higherIsBetter = $this->filterBooleanOfInputRequest("higherIsBetter");
        return new Program\MetricData($name, $description, $minValue, $maxValue, $higherIsBetter);
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
    
    protected function buildAddService()
    {
        $metricRepository = $this->em->getRepository(Metric2::class);
        $programRepository = $this->em->getRepository(Program::class);
        return new AddMetric($metricRepository, $programRepository);
    }
    
    protected function buildUpdateService()
    {
        $metricRepository = $this->em->getRepository(Metric2::class);
        return new UpdateMetric($metricRepository);
    }

}
