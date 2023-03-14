<?php

global $_SWAGGER;
$module = "guide";
array_push($_SWAGGER, ["name" => "{$module}", "url" => "/?/api/swagger/{$module}", "path" => __DIR__]);

use Langnang\Module\Root\RootController;

use phpspider\core\requests;
use phpspider\core\selector;

require_once __DIR__ . '/../.mysql/mysql.php';
// require_once __DIR__ . '/models.php';

class Guide extends RootController
{
  protected $_class = __CLASS__;
  protected $_table_path = __DIR__ . '/table.json';


  function before($method, $vars)
  {
    if (!isset($vars['_user']) && in_array($method, ['select_list', 'select_item', 'select_tree'])) {
      $vars['status'] = 'public';
    }
    return $vars;
  }

  function crawler_item($vars)
  {
    $url = $vars['url'];
    $parse_url = parse_url($url);
    $html = requests::get($url);
    $title = selector::select($html, "//head/title");
    $meta_contents = selector::select($html, "//head/meta[contains(@name,'desc')]/@content");
    // 拼接图标路径
    $icons = array_map(function ($item) use ($parse_url) {
      $parse_icon = parse_url($item);
      if (!isset($parse_icon['host'])) {
        return $parse_url["scheme"] . "://" . $parse_url["host"] . "/" . $parse_icon['path'];
      }
      return $item;
    }, (array)selector::select($html, "//head/link[contains(@rel,'icon')]/@href"));
    // TODO
    if ($vars['id']) {
      // TODO 更新数据
    }
    return [
      "title" => $title,
      "icons" => (array)$icons,
      "descriptions" => (array)$meta_contents,
      // "parse_url" => $parse_url,
      // "parse_icon" => parse_url(((array)$icons)[0]),
      // "pathinfo_icon" => pathinfo(((array)$icons)[0]),
    ];
  }

  function crawler_list($vars)
  {
    $ids = $vars['id'];
    $rows = [];
    foreach ($ids as $id) {
      $row = $this->select_item(["id" => $id]);
      $crawler_item = $this->crawler_item(["url" => $row['url']]);
      $row['name'] = $crawler_item['title'] ? $crawler_item['title'] : null;
      $row['icon'] = $crawler_item['icons'] ? $crawler_item['icons'][0] : null;
      $row['description'] = $crawler_item['descriptions'] ? $crawler_item['descriptions'][0] : null;
      $this->update_item($row);
      array_push($rows, $row);
    }
    return $rows;
  }
}

/**
 * @OA\Info(
 *   title="guide APIs",
 *   description="guide APIs",
 *   version="0.0.1",
 * )
 */
$router->addGroup("/{$module}", function (FastRoute\RouteCollector $router) use ($module) {


  $router->addRoute('GET', '/initialize', function ($vars) use ($module) {
    global $_TWIG, $_CONNECTION, $_CONFIG;
    $template = $_TWIG->load("{$module}/{$module}.sql");
    $sql = $template->renderBlock("initialize", ["dbname" => $_CONFIG['db']['dbname'], "prefix" => $_CONFIG['db']['prefix']]);
    print($sql);
  });

  $router->addRoute('GET', '/cleanup', function ($vars) use ($module) {
    global $_TWIG, $_CONNECTION;
    $template = $_TWIG->load("{$module}/{$module}.sql");
  });

  // $meta = new GuideMeta();
  // $content = new GuideContent();
  // $root_meta = $meta->select_item(["type" => "branch", "slug" => "guide"]);
  // $root_content = $content->select_item(["type" => "branch", "slug" => "guide"]);
  // $root = ["mid" => $root_meta["mid"], "cid" => $root_content["cid"]];
  // var_dump($root);
  $router->addRoute('POST', '/insert_item', [new Guide(), 'insert_item']);
  $router->addRoute('POST', '/delete_item', [new Guide(), 'delete_item']);
  $router->addRoute('POST', '/delete_list', [new Guide(), 'delete_list']);
  $router->addRoute('POST', '/update_item', [new Guide(), 'update_item']);
  $router->addRoute('POST', '/update_list', [new Guide(), 'update_list']);

  $router->addRoute('POST', '/crawler_list', [new Guide(), 'crawler_list']);

  $router->addRoute('POST', '/crawler_item', [new Guide(), 'crawler_item']);

  /**
   * @OA\Post(
   *     path="/api/guide/select_list",
   *     @OA\RequestBody(
   *         @OA\MediaType(
   *             mediaType="application/json",
   *             @OA\Schema(
   *                 @OA\Property(
   *                     property="parent",
   *                     type="integer",
   *                 ),
   *                 @OA\Property(
   *                     property="type",
   *                     type="string"
   *                 ),
   *                 example={"parent": 0, "type": "category"}
   *             )
   *         )
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/select_list', [new Guide(), 'select_list']);
  /**
   * @OA\Post(
   *     path="/api/guide/select_tree",
   *     @OA\RequestBody(
   *         @OA\MediaType(
   *             mediaType="application/json",
   *             @OA\Schema(
   *                 @OA\Property(
   *                     property="parent",
   *                     type="integer",
   *                 ),
   *                 @OA\Property(
   *                     property="type",
   *                     type="object",
   *                 ),
   *                 example={"parent": 0, "type": "category"}
   *             )
   *         )
   *     ),
   *     @OA\Response(response="200", description="")
   * )
   */
  $router->addRoute('POST', '/select_tree', [new Guide(), 'select_tree']);
});
