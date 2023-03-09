<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Task\Personnel\ViewAllNewProgramApplicant;
use Query\Domain\Task\Personnel\ViewAllNewProgramApplicantPayload;
use Resources\OffsetLimit;
use Resources\OffsetLimit\Order;
use Resources\SearchFilter;
use Resources\SearchFilter\EqualsCriteria;

class ManageableNewApplicantController extends PersonnelBaseController
{

    public function viewAll()
    {
        $registrantRepository = $this->em->getRepository(Registrant::class);
        $task = new ViewAllNewProgramApplicant($registrantRepository);

        $searchFilter = (new SearchFilter)
                ->addCriteria(new EqualsCriteria('Program_id', $this->request->query('programId'), 'Registrant'));
        $offsetLimit = (new OffsetLimit($this->getPage(), $this->getPageSize()))
                ->addOrder(new Order('registeredTime'));

        $payload = (new ViewAllNewProgramApplicantPayload())
                ->setSearchFilter($searchFilter)
                ->setOffsetLimit($offsetLimit);

        $this->executePersonalQueryTask($task, $payload);

        return $this->listQueryResponse($payload->result);
    }

}
