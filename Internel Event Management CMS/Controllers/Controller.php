<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     host="dev.psc-lms.com/api",
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="This is my website cool API",
 *         description="Api description...",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="contact@mysite.com"
 *         ),
 *         @SWG\License(
 *             name="Private License",
 *             url="URL to the license"
 *         )
 *     ),
 *   @SWG\SecurityScheme(
 *      securityDefinition="Bearer",
 *      description="Add Bearer before token",
 *      type="apiKey",
 *      name="Authorization",
 *      in="header",
 *  ),
 * )
 */
class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;
}
