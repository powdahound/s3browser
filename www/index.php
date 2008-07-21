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

$files = $s3b->getFiles('/');
?>

<h2>S3 Browser</h2>
<ul>
<? foreach ($files as $file): ?>
  <li><?= $file ?></li>
<? endforeach; ?>
</ul>
