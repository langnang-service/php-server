<?php
global $_SWAGGER;
$module = "typecho/user";
array_push($_SWAGGER, ["name" => "{$module}", "url" => "/?/api/swagger/{$module}", "path" => __DIR__]);

use Langnang\Module\User\User;

require_once __DIR__ . '/controllers.php';
/**
 * @OA\Info(
 *   title="User APIs",
 *   description="Log",
 *   version="0.0.1",
 * )
 */
$router->addGroup("/{$module}", function (FastRoute\RouteCollector $router) {
  /**
   * @OA\Post(
   *     path="/api/typecho/user/login",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\MediaType(
   *             mediaType="application/json",
   *             @OA\Schema(
   *                 @OA\Property(property="name", type="string", default="guest",),
   *                 @OA\Property(property="password", type="string", default="123456",),
   *             )
   *         )
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/login', [new User(), 'login']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/logout",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\MediaType(
   *             mediaType="application/json",
   *             @OA\Schema(
   *                 @OA\Property(property="authCode", type="string", default="",),
   *             )
   *         )
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/logout', [new User(), 'logout']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/insert",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/UserModel")
   *     ),     
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/insert', [new User(), 'insert_item']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/delete",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/UserModel")
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/delete', [new User(), 'delete_list']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/update",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/UserModel")
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/update', [new User(), 'update_item']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/count",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/UserModel")
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/count', [new User(), 'select_count']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/list",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\MediaType(
   *             mediaType="application/json",
   *             @OA\Schema(
   *                 @OA\Property(property="name", type="string", default="",),
   *             )
   *         )
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/list', [new User(), 'select_list']);
  /**
   * @OA\Post(
   *     path="/api/typecho/user/info",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\MediaType(
   *             mediaType="application/json",
   *             @OA\Schema(
   *                 @OA\Property(property="uid", type="int", default="1",),
   *             )
   *         )
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/info', [new User(), 'select_item']);
});
