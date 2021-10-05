<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program\Sponsor;
use Firm\Domain\Task\InProgram\AddSponsorTask;
use Firm\Domain\Task\InProgram\DisableSponsorTask;
use Firm\Domain\Task\InProgram\EnableSponsorTask;
use Firm\Domain\Task\InProgram\SponsorRequest;
use Firm\Domain\Task\InProgram\UpdateSponsorTask;
use Query\Domain\Model\Firm\FirmFileInfo as FirmFileInfo2;
use Query\Domain\Model\Firm\Program\Sponsor as Sponsor2;
use Query\Domain\Task\InProgram\ViewAllSponsorsPayload;
use Query\Domain\Task\InProgram\ViewAllSponsorsTask;
use Query\Domain\Task\InProgram\ViewSponsorDetailTask;

class SponsorController extends ManagerBaseController
{
    
    protected function buildSponsorRequest()
    {
        $name = $this->stripTagsInputRequest("name");
        $website = $this->stripTagsInputRequest("website");
        $firmFileInfoId = $this->stripTagsInputRequest("firmFileInfoIdOfLogo");
        return new SponsorRequest($name, $website, $firmFileInfoId);
    }
    
    protected function viewSponsorDetail($programId, $sponsorId): array
    {
        $sponsorRepository = $this->em->getRepository(Sponsor2::class);
        $task = new ViewSponsorDetailTask($sponsorRepository, $sponsorId);
        $this->executeQueryTaskInProgram($programId, $task);
        $sponsor = $task->result;
        return [
            'id' => $sponsor->getId(),
            'disabled' => $sponsor->isDisabled(),
            'name' => $sponsor->getName(),
            'website' => $sponsor->getWebsite(),
            'logo' => $this->arrayDataOfLogo($sponsor->getLogo()),
        ];
    }
    protected function arrayDataOfLogo(?FirmFileInfo2 $logo): ?array
    {
        return empty($logo) ? null : [
            "id" => $logo->getId(),
            "url" => $logo->getFullyQualifiedFileName(),
        ];
    }

    public function add($programId)
    {
        $sponsorRepository = $this->em->getRepository(Sponsor::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        $task = new AddSponsorTask($sponsorRepository, $firmFileInfoRepository, $this->buildSponsorRequest());
        $this->executeCommandTaskInProgramOfFirmContext($programId, $task);
        
        return $this->commandCreatedResponse($this->viewSponsorDetail($programId, $task->createdSponsorId));
    }

    public function update($programId, $sponsorId)
    {
        $sponsorRepository = $this->em->getRepository(Sponsor::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        $task = new UpdateSponsorTask(
                $sponsorRepository, $firmFileInfoRepository, $sponsorId, $this->buildSponsorRequest());
        $this->executeCommandTaskInProgramOfFirmContext($programId, $task);
        
        return $this->singleQueryResponse($this->viewSponsorDetail($programId, $sponsorId));
    }

    public function enable($programId, $sponsorId)
    {
        $sponsorRepository = $this->em->getRepository(Sponsor::class);
        $task = new EnableSponsorTask($sponsorRepository, $sponsorId);
        $this->executeCommandTaskInProgramOfFirmContext($programId, $task);
        
        return $this->singleQueryResponse($this->viewSponsorDetail($programId, $sponsorId));
    }

    public function disable($programId, $sponsorId)
    {
        $sponsorRepository = $this->em->getRepository(Sponsor::class);
        $task = new DisableSponsorTask($sponsorRepository, $sponsorId);
        $this->executeCommandTaskInProgramOfFirmContext($programId, $task);
        
        return $this->singleQueryResponse($this->viewSponsorDetail($programId, $sponsorId));
    }

    public function show($programId, $sponsorId)
    {
        return $this->singleQueryResponse($this->viewSponsorDetail($programId, $sponsorId));
    }

    public function showAll($programId)
    {
        $sponsorRepository = $this->em->getRepository(Sponsor2::class);
        $activeStatus = $this->filterBooleanOfQueryRequest('activeStatus');
        $payload = new ViewAllSponsorsPayload($this->getPage(), $this->getPageSize(), $activeStatus);
        $task = new ViewAllSponsorsTask($sponsorRepository, $payload);
        $this->executeQueryTaskInProgram($programId, $task);
        
        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $sponsor) {
            $result['list'][] = [
                'id' => $sponsor->getId(),
                'disabled' => $sponsor->isDisabled(),
                'name' => $sponsor->getName(),
                'website' => $sponsor->getWebsite(),
            ];
        }
        return $this->listQueryResponse($result);
    }

}
