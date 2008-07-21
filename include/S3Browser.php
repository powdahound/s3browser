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
    $bContents = $this->getBucketContents();
    $contents = array();
    
    $keys = array_keys($bContents);
    foreach ($keys as $key) {
      $parts = explode('/', $key);
      $contents[$parts[0]] = $parts[0];
    }
    
    return $contents;
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