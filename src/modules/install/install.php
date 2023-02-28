<?php

namespace Langnang\PhpServer\Install;

use Exception;

class Install
{
  public $_path = __DIR__ . '/../../../config.inc.php';
  public $appConfig;
  public $viewConfig;
  public $routerConfig;
  public $storageConfig;
  public $dbConfig;

  function __construct($path)
  {
    if (!file_exists($path)) {
      return false;
    }
  }
  function get()
  {
  }
  function read()
  {
  }
  function write()
  {
  }
}

class InstallDb
{
}

class InstallApp
{
}
// 读取配置信息
function _get($name)
{
  global $config;
  $keys = explode("_", $name);
  foreach ($keys as $i => $value) :
    $i == 0 ? ($keys[$i] .= "_config") : NULL;
  endforeach;
  return array_reduce($keys, function ($array, $key) {
    return $array[$key];
  }, $config);
}
