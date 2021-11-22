<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\Application\Service\Firm\Program\ViewActivity;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Infrastructure\QueryFilter\ActivityFilter;

class ActivityController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $activityFilter = (new ActivityFilter())
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'))
                ->setOrder($this->stripTagQueryRequest('order'));
        
        $activityTypeIdLIst = $this->request->query('activityTypeIdList');
        if (is_array($activityTypeIdLIst)) {
            foreach ($activityTypeIdLIst as $activityTypeId) {
                $activityFilter->addActivityTypeId($this->stripTagsVariable($activityTypeId));
            }
        }
        
        $initiatorTypeList = $this->request->query('initiatorTypeList');
        if (is_array($initiatorTypeList)) {
            foreach ($initiatorTypeList as $userType) {
                $activityFilter->addInitiatorTypeList($userType);
            }
        }
        
        $activities = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $activityFilter);
        
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
