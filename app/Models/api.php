<?php

/**
 * @license Apache 2.0
 */

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Your API Title",
 *         version="1.0.0",
 *         description="This is a sample Petstore server. You can find more about Swagger at [http://swagger.io](http://swagger.io) or on [irc.freenode.net, #swagger](http://swagger.io/irc/).",
 *         termsOfService="http://swagger.io/terms/",
 *         @OA\Contact(
 *             email="apiteam@swagger.io"
 *         ),
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * description="Nhập token JWT vào đây"
 * )
 */