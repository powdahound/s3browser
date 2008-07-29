<?php

$c = array();

// Browser config
$c['base-path'] = null;
$c['theme'] = 'plain';
$c['page-title'] = 'My Amazon S3 files';
$c['torrent-threshold'] = null; // file size in bytes over which to serve files as torrents

// S3 config
$c['s3-access-key'] = '';
$c['s3-secret-key'] = '';

// Caching
$c['cache-time'] = 0;
$c['cache-dir'] = '/tmp';

// Bucket
$c['bucket-name'] = '';
$c['bucket-url-prefix'] = 'http://'.$c['bucket-name'].'.s3.amazonaws.com';

// Google analytics
$c['google-analytics-id'] = null;

return $c;
