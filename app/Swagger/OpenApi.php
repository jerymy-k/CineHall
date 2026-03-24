<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Cinema API",
    version: "1.0.0",
    description: "API for cinema management"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000/api",
    description: "Local Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApi {}