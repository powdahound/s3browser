<pre>
<?php

require '../libs/s3-php/S3.php';

if (!file_exists('../config.inc.php')) {
  die('config.inc.php is missing. See config.inc.php-sample');
}

$config = include('../config.inc.php');
$cacheFile = $config['cache-dir'].'/s3-directory-browser-cache';

if (file_exists($cacheFile)) {
  $contents = unserialize(file_get_contents($cacheFile));
} else {
  $s3 = new S3($config['s3-access-key'], $config['s3-secret-key']);
  $contents = $s3->getBucket($config['bucket-name']);
  file_put_contents($cacheFile, serialize($contents));
}

$keys = array_keys($contents);
foreach ($keys as $key) {
  $parts = explode('/', $key);
  $dirs[$parts[0]] = $parts[0];
}

print_r($dirs);

