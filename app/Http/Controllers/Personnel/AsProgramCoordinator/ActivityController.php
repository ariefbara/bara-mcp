<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\Application\Service\Firm\Program\ViewActivity;
use Query\Domain\Model\Firm\Program\Activity;

class ActivityController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $activities = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($activities);
        foreach ($activities as $activity) {
            $result["list"][] = [
                "id" => $activity->getId(),
                "name" => $activity->getName(),
                "startTime" => $activity->getStartTimeString(),
                "endTime" => $activity->getEndTimeString(),
                "cancelled" => $activity->isCancelled(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    public function show($programId, $activityId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $activity = $this->buildViewService()->showById($this->firmId(), $programId, $activityId);
        return $this->singleQueryResponse($this->arrayDataOfActivity($activity));
    }
    
    protected function arrayDataOfActivity(Activity $activity): array
    {
        return [
            "id" => $activity->getId(),
            "name" => $activity->getName(),
            "description" => $activity->getDescription(),
            "startTime" => $activity->getStartTimeString(),
            "endTime" => $activity->getEndTimeString(),
            "location" => $activity->getLocation(),
            "note" => $activity->getNote(),
            "cancelled" => $activity->isCancelled(),
            "createdTime" => $activity->getCreatedTimeString(),
        ];
    }
    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }
}
