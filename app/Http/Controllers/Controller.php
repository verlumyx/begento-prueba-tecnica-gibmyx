<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API de Gestión de Productos",
    description: "Documentación Swagger para la prueba técnica de productos y divisas."
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Servidor Local"
)]

abstract class Controller
{
    //
}
