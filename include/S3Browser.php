<?php

require ROOT_DIR.'/libs/s3-php/S3.php';

class S3Browser {
  private $s3Bucket;
  private $s3AccessKey;
  private $s3SecretKey;
  private $cacheDir;
  private $cacheDuration;
  private $bucketContents;
  
  function __construct($bucketName, $accessKey, $secretKey) {
    $this->s3Bucket = $bucketName;
    $this->s3AccessKey = $accessKey;
    $this->s3SecretKey = $secretKey;
  }

  public function enableCaching($dir, $duration) {
    $this->cacheDir = $dir;
    $this->cacheDuration = $duration;
  }
  
  public function getFiles($path = '/') {
    $path = rtrim($path, '/');

    $bContents = $this->getBucketContents();
    
    $contents = array();
    $keys = array_keys($bContents);

    $regexPath = ($path == '/') ? '' : preg_quote($path, '/');
    $regex = '/'.$regexPath.'\/([^\/]*)/';
    foreach ($keys as $key) {
      $absKey = '/'.$key;
      preg_match($regex, $absKey, $matches);
      // echo "<h2>".$absKey."</h2>";
      // echo "<pre>";
      // print_r($matches);
      // echo "</pre>";
      if (!isset($matches[1]) || $matches[1] == '') continue;
      
      $file = $matches[1];
      if (!isset($contents[$file])) {
        $contents[$file] = $bContents[$key];
      }
    }
    
    return $contents;
  }
  
  public function getBreadcrumb($path = '/') {
    $path = trim($path, '/'); // so we don't get nulls when exploding
    $parts = explode('/', $path);
    $crumbs = array();
    
    for ($i = 0; $i < count($parts); $i++) {
      $crumbs[$parts[$i]] = implode('/', array_slice($parts, 0, $i+1)).'/';
    }
    
    return $crumbs;
  }
  
  private function getBucketContents() {
    $cacheFile = $this->cacheDir.'/s3browser-'.$this->s3Bucket;
    $contents = null;
    
    // get from cache if valid
    if ($this->cacheDuration && file_exists($cacheFile)) {
      $cacheAge = time() - filectime($cacheFile);
      
      if ($cacheAge < $this->cacheDuration) {
        $contents = unserialize(file_get_contents($cacheFile));
      }
    }
    
    if (!$contents) {
      $s3 = new S3($this->s3AccessKey, $this->s3SecretKey);
      $contents = $s3->getBucket($this->s3Bucket);
      
      // save if caching is enabled
      if ($this->cacheDuration) {
        file_put_contents($cacheFile, serialize($contents));
      }
    }
    
    return $contents;
  }
}