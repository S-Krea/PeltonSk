<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;

class BaseUrlDecorator implements OpenApiFactoryInterface
{
    public function __construct(protected readonly OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $servers = $openApi->getServers();

        if (count($servers) > 0) {
            /** @var Server $server */
            $server = $servers[0];
            $updatedServer = $server->withUrl('https://'.$_SERVER['SERVER_NAME']);
            $servers[0] = $updatedServer;
            $openApi = $openApi->withServers($servers);
        }

        return $openApi;
    }
}
