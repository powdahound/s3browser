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

  /**
   * Enable caching of files list so S3 doesn't have to be queried on every
   * request
   *
   * @param string $dir       Location to cache listing
   * @param string $duration  Time in seconds before expiring cache
   * @return void
   */
  public function enableCaching($dir, $duration) {
    $this->cacheDir = $dir;
    $this->cacheDuration = $duration;
  }
  
  /**
   * Returns list of file objects in the given path
   *
   * @param string $path  Directory path
   * @return array        Directory contents
   */
  public function getFiles($path = '/') {
    $path = rtrim($path, '/');

    // get list of all files
    $bContents = $this->getBucketContents();
    
    $contents = array();
    $keys = array_keys($bContents);

    // build regex to help search for files with given $path
    $regexPath = ($path == '/') ? '' : preg_quote($path, '/');
    $regex = '/'.$regexPath.'\/([^\/]*)/';
    
    // find all the files with keys matching our regex and store them
    foreach ($keys as $key) {
      $absKey = '/'.$key;
      preg_match($regex, $absKey, $matches);
      if (!isset($matches[1]) || $matches[1] == '') continue;
      
      $file = $matches[1];
      if (!isset($contents[$file])) {
        $contents[$file] = $bContents[$key];
        
        // store human-readable size
        $contents[$file]['hsize'] = self::formatSize($bContents[$key]['size']);
      }
    }
    
    return $contents;
  }
  
  /**
   * Get S3 bucket contents (from cache if possible)
   *
   * @return array
   */
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
    
    // hit s3 if we didn't have anything cached
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
  
  /**
   * Returns directory data for all levels of the given path to be used when
   * displaying a breadcrumb.
   *
   * @param string $path
   * @return array
   */
  public static function getBreadcrumb($path = '/') {
    if ($path == '/')
      return array('/' => '');
    
    $path = trim($path, '/'); // so we don't get nulls when exploding
    $parts = explode('/', $path);
    $crumbs = array('/' => '');

    for ($i = 0; $i < count($parts); $i++) {
      $crumbs[$parts[$i]] = implode('/', array_slice($parts, 0, $i+1)).'/';
    }
    
    return $crumbs;
  }
  
  /**
   * Returns parent directory 
   *
   * @param string $path
   * @return array
   */
  public static function getParent($path = '/') {
    $crumbs = self::getBreadcrumb($path);
    
    $current = array_pop($crumbs);
    $parent = array_pop($crumbs);

    return $parent;
  }
  
  /**
   * Takes a size in bytes and converts it to a more human-readable format
   *
   * @param string $bytes   Size in bytes
   * @return string
   */
  private static function formatSize($bytes) {
    $size = (int)$bytes;
    $units = array("B", "K", "M", "G", "T", "P");
    $unit = 0;
    while ($size >= 1024) {
      $unit++;
      $size = $size/1024;
    }

    if ($unit == 0 && $size == 16) return null;

    return number_format($size, ($unit ? 2 : 0)).''.$units[$unit];
  }
}