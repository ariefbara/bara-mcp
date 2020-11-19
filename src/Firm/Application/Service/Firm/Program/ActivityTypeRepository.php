<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\ {
    Manager\ActivityTypeRepository as InterfaceForManager,
    Personnel\ActivityTypeRepository as InterfaceForPersonnel
};

interface ActivityTypeRepository extends InterfaceForPersonnel, InterfaceForManager
{
    
}
