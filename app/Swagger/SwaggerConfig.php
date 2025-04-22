<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel hakaton project",
 *     description="Я люблю Александра Ширяева",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Development server"
 * )
 */
class SwaggerConfig
{
    // Этот класс нужен только для аннотаций Swagger
}
