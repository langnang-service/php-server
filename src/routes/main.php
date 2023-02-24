<?php

$router->addRoute('GET', '/', function ($vars) {
  header("location:/swagger");
  // echo file_get_contents(__DIR__ . '/../views/index.html');
});
