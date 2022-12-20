<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model as OaModel;
use ApiPlatform\OpenApi\OpenApi;

class JwtDecorator implements OpenApiFactoryInterface
{
    public function __construct(private readonly OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $schemas = $openApi->getComponents()->getSchemas();

        // Describe the token result
        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        // Describe the credentials input
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'user@domain.tld',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'aVerySecuredPassword',
                ],
            ],
        ]);

        $securitySchemes = $openApi->getComponents()->getSecuritySchemes() ?? [];
        $securitySchemes['JWT'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ]);

        $pathItem = new OaModel\PathItem(
            ref: 'JWT Token',
            post: new OaModel\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Player'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get JWT token to login.',
                requestBody: new OaModel\RequestBody(
                    description: 'The credentials information',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ])
                ),
                security: [],
            ),
        );

        $openApi->getPaths()->addPath('/api/login', $pathItem);

        return $openApi;
    }
}
