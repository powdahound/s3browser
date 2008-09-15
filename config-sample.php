<?php

$c = array();

/**********************************
 * Browser config
 **********************************/

// Base path to directory the browser is running in. Leave blank if running out of a subdomain.
$c['base-path'] = null;

// Name of theme to use for display. Themes are found in the themes/ directory.
$c['theme'] = 'plain';

// Text to use as page header
$c['page-title'] = 'My Amazon S3 files';

// File size in bytes over which to serve files as torrents
$c['torrent-threshold'] = null;

// Amazon S3 access information
$c['s3-access-key'] = '';
$c['s3-secret-key'] = '';

// Bucket information should be cached so your S3 account doesn't need to be queried for every user request. Default cache-time is 10 minutes.
$c['cache-time'] = 60 * 10;
$c['cache-dir'] = '/tmp';

// Bucket
$c['bucket-name'] = '';
$c['bucket-url-prefix'] = 'http://'.$c['bucket-name'].'.s3.amazonaws.com';

// Google analytics id to use for tracking
// Download links will also be recorded in Google Analytics
$c['google-analytics-id'] = null;

return $c;
