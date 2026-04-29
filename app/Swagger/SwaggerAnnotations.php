<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="HostYourHackathon API",
 *      description="REST API documentation for HostYourHackathon platform"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 */
class SwaggerAnnotations
{
}
