<?php
define('ROOT_DIR', dirname(__FILE__).'/..');
require ROOT_DIR.'/include/S3Browser.php';

// Load config
$configFile = ROOT_DIR.'/config.php';
if (!file_exists($configFile)) {
  die('config.php is missing. See config-sample.php');
}
$config = include($configFile);

// S3Browser
$s3b = new S3Browser($config['bucket-name'], $config['s3-access-key'], $config['s3-secret-key']);
$s3b->enableCaching($config['cache-dir'], $config['cache-time']);

$dir = isset($_GET['dir']) ? $_GET['dir'] : '/';

// $scriptDir = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
// $request = $_SERVER['REQUEST_URI'];
// $dir = str_replace($scriptDir, '', $request);

$files = $s3b->getFiles($dir);
?>

<? include ROOT_DIR.'/templates/plain.tpl.php'; ?>
