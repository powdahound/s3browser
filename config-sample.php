<?php

$c = array();

// Browser config
$c['base-path'] = null;

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
