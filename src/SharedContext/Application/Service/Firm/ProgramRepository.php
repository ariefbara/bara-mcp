<?php

namespace SharedContext\Application\Service\Firm;

use Client\Application\Service\Client\ProgramRepository as InterfaceForClientBC;
use User\Application\Service\User\ProgramRepository as InterfaceForUserBC;

interface ProgramRepository extends InterfaceForClientBC, InterfaceForUserBC
{
    
}
