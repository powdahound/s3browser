<?php

function getenv_default($name, $default = null) {
  $value = getenv($name);
  return ($value === false) ? $default : $value;
}

$c = array();

// Base path to directory the browser is running in. Leave blank if running out of a subdomain (like on Heroku)
$c['base-path'] = getenv_default('BASE_PATH');

// Name of theme to use for display. Themes are found in the themes/ directory.
$c['theme'] = getenv_default('THEME', 'plain');

// Text to use as page header
$c['page-header'] = getenv_default('PAGE_HEADER', 'My Amazon S3 files');

// File size in bytes over which to serve files as torrents
$c['torrent-threshold'] = getenv_default('TORRENT_THRESHOLD', null);

// Amazon S3 access information
$c['s3-access-key'] = getenv_default('S3_ACCESS_KEY');
$c['s3-secret-key'] = getenv_default('S3_SECRET_KEY');

// Bucket information should be cached so your S3 account doesn't need to be queried for every user request. Default cache-time is 10 minutes.
$c['cache-time'] = getenv_default('CACHE_TIME', 60 * 10);
$c['cache-dir'] = getenv_default('CACHE_DIR', '/tmp');

// Bucket
$c['bucket-name'] = getenv_default('BUCKET_NAME');
$c['bucket-url-prefix'] = getenv_default('BUCKET_URL_PREFIX', 'http://'.$c['bucket-name'].'.s3.amazonaws.com');

// Google analytics id to use for tracking
// Download links will also be recorded in Google Analytics
$c['google-analytics-id'] = getenv_default('GOOGLE_ANALYTICS_ID');

return $c;
