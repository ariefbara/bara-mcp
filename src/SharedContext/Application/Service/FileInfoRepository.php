<?php

namespace SharedContext\Application\Service;

use Participant\Domain\Service\FileInfoRepository as InterfaceForParticipantBC;
use Personnel\Domain\Service\FileInfoRepository as InterfaceForPersonnelBc;

interface FileInfoRepository extends InterfaceForParticipantBC, InterfaceForPersonnelBc
{
    
}
