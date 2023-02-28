<?php

global $_SWAGGER;
$module = "main";
// array_push($_SWAGGER, ["name" => "{$module}", "url" => "/?/api/swagger/{$module}", "path" => __FILE__]);

/**
 * @OA\Info(
 *     title="PhpServer APIs",
 *     version="1.0",
 * )
 */

$router->addGroup("/api", function (FastRoute\RouteCollector $router) {

  require_once __DIR__ . '/example.php';

  $router->addGroup("/swagger", function (FastRoute\RouteCollector $router) {

    $router->addRoute('GET', '', function ($vars) {
      global $_SWAGGER;

      header('Content-Type: application/json');
      echo json_encode(array_map(function ($item) {
        unset($item['path']);
        return $item;
      }, $_SWAGGER), JSON_UNESCAPED_UNICODE);
      exit;
    });

    $router->addRoute('GET', '/{module:.+}', function ($vars) {
      global $_SWAGGER;
      $index = false;
      foreach ($_SWAGGER as $key => $item) {
        if ($item['name'] == $vars['module']) $index = $key;
      }
      if ($index === false) throw new Exception("error find swagger.");

      $openapi = \OpenApi\Generator::scan([$_SWAGGER[$index]['path']]);

      $openapi = json_decode($openapi->toJson(), true);
      $paths = $openapi['paths'];
      $openapi['paths'] = [];
      foreach ($paths as $path => $value) {
        $openapi['paths']['/?' . $path] = $value;
      }
      header('Content-Type: application/json');
      echo json_encode($openapi, JSON_UNESCAPED_UNICODE);
      exit;
      // return false;
    });
  });
  $router->addRoute("POST", "/s", function ($vars) {
    global $dispatcher, $_CONFIG;
    $result = [];
    for ($i = 0; $i < count($vars['_post']); $i++) {
      $_result = null;
      $data = $vars['_post'][$i];
      $uri = $data["url"];
      $httpMethod = strtoupper($data["method"] ? $data["method"] : "get");
      $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
      $handler = $routeInfo[1];
      switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
          $_result = new Exception("404 Not Found", 404);
          break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
          $_result = new Exception("405 Method Not Allowed", 405);
          // ... 405 Method Not Allowed
          break;
        case FastRoute\Dispatcher::FOUND:
          $handler = $routeInfo[1];
          // Request Verify
          if ($_CONFIG['request_verify'] === true && in_array($httpMethod, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!in_array($uri, $_CONFIG['request_verify_ignore_urls'])) {
              try {
                if (is_string($_CONFIG['request_verify_func']) && function_exists($_CONFIG['request_verify_func'])) {
                  $user = call_user_func($_CONFIG['request_verify_func']);
                } else if (is_array($_CONFIG['request_verify_func']) && method_exists(...$_CONFIG['request_verify_func'])) {
                  $user = call_user_func($_CONFIG['request_verify_func']);
                } else {
                  $user = new Exception("400 Verify Method Not Exist.", 400);
                }
              } catch (Exception $e) {
                $result = $e;
                break;
              }
            }
          }

          $_get = $data['params'] ? $data['params'] : [];
          $_post = $data['data'] ? $data['data'] : [];

          $_vars = array_merge([
            "_method" => $httpMethod,
            "_path" => $uri,
            "_files" => $_FILES,
            "_user" => $user,
            "_get" => $_get,
            "_post" => $_post,
          ], $_get, $_post, $routeInfo[2],);
          try {
            if (is_object($handler)) {
              $_result = $handler($_vars);
            } else if (is_string($handler) && function_exists($handler)) {
              $_result = call_user_func($handler, $_vars);
            } else if (is_array($handler) && method_exists($handler[0], $handler[1])) {
              $_result = call_user_func($handler, $_vars);
            } else {
              throw new Exception("error handler method.", 404);
            }
          } catch (Exception $e) {
            $_result = $e;
          }
          break;
      }
      /** 异常 */
      if ($result instanceof Exception) {
        $_result = [
          "status" => empty($_result->getCode()) ? 400 : $_result->getCode(),
          "statusText" => "Error",
          "message" => $_result->getMessage(),
        ];
      } elseif (is_null($_result)) {
        $_result = array(
          "status" => 400,
          "statusText" => 'Error',
        );
      } else {
        $_result = array(
          "status" => 200,
          "statusText" => 'Success',
          "data" => $_result,
        );
      }
      $_result["url"] = $uri;
      $_result["method"] = $httpMethod;
      array_push($result, $_result,);
    }
    return $result;
  });
  // require swagger apis
  foreach (scandir(__DIR__ . '/../modules') as $path) {
    if (is_dir(__DIR__ . '/../modules/' . $path)) {
      if (file_exists(__DIR__ . '/../modules/' . $path . '/api.php')) {
        // level 1
        require_once __DIR__ . '/../modules/' . $path . '/api.php';
      } else {
        // level 2
        foreach (scandir(__DIR__ . '/../modules/' . $path) as $pat) {
          if (file_exists(__DIR__ . '/../modules/' . $path . "/" . $pat . '/api.php')) {
            require_once __DIR__ . '/../modules/' . $path . "/" . $pat . '/api.php';
          }
        }
      }
    }
  }
});
