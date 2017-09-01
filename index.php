<?php

require('config.php');
require('db.php');
require('api.php');

$script = $_SERVER['PHP_SELF'];
$dirname = pathinfo($script, PATHINFO_DIRNAME);
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri)['path'];

if ($path === '/') {
  $html = file_get_contents('public/index.html');

  echo $html;
} else {
  if (is_file($request_uri)) {
    if (file_exists($request_uri)) {
      $asset = file_get_contents($request_uri);

      echo $asset;
    }
  } else {
    api($path, $db);
  }
}
