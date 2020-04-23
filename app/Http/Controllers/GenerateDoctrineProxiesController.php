<?php

namespace App\Http\Controllers;

class GenerateDoctrineProxiesController extends Controller
{
    function generate()
    {
        $proxyFactory = $this->em->getProxyFactory();
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $proxyFactory->generateProxyClasses($metadatas);
        return $this->commandOkResponse();
    }
}
