<?php

require ROOT_DIR.'/libs/s3-php/S3.php';

class S3Browser {
  private $s3Bucket;
  private $s3AccessKey;
  private $s3SecretKey;
  private $cacheDir;
  private $cacheDuration;
  private $bucketContents;

  function __construct($bucketName, $accessKey, $secretKey, $useSSL = true, $endPoint = 's3.amazonaws.com') {
    $this->s3Bucket = $bucketName;
    $this->s3AccessKey = $accessKey;
    $this->s3SecretKey = $secretKey;
    $this->s3useSSL = $useSSL;
    $this->s3endPoint = $endPoint;
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
    $tree = $this->getTree($path);
    if ($tree === null) {
      return null;
    }

    $path = trim($path, '/');
    if ($path) {
      $parts = explode('/', $path);

      // walk to correct point in tree
      foreach ($parts as $part) {
        if (!isset($tree[$part])) {
          return array();
        }
        $tree = $tree[$part]['files'];
      }
    }

    uasort($tree, array($this, 'sort'));
    return $tree;
  }

  /**
   * Get S3 bucket contents (from cache if possible)
   *
   * @return array
   */
  private function getBucketContents($tree_path) {
    $cacheFile = $this->cacheDir.'/s3browser-'.$this->s3Bucket.str_replace('/', '-', $tree_path);
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
      $s3 = new S3($this->s3AccessKey, $this->s3SecretKey, $this->s3useSSL, $this->s3endPoint);
      if ($tree_path == '/') {
        $contents = $s3->getBucket($this->s3Bucket, null, null, null, $tree_path, true);
      }
      else {
        $contents = $s3->getBucket($this->s3Bucket, ltrim($tree_path, '/'));
      }

      // we weren't able to access the bucket
      if (!is_array($contents)) {
        return null;
      }

      // save if caching is enabled
      if ($this->cacheDuration) {
        file_put_contents($cacheFile, serialize($contents));
      }
    }

    return $contents;
  }

  /**
   * Build a tree representing the directory structure of the bucket's
   * contents.
   *
   * @return array
   */
  public function getTree($tree_path) {
    $tree = array();
    $contents = $this->getBucketContents($tree_path);
    if ($contents === null) {
      return null;
    }

    foreach ($contents as $key => $data) {
      $isFolder = false;

      // S3Hub and S3Fox append this suffix to folders
      if (substr($key, -9) == '_$folder$') {
        $key = substr($key, 0, -9);
        $isFolder = true;
      }
      // Assume any key ending with / is a folder
      else if (substr($key, -1) == '/') {
        $key = substr($key, 0, -1);
        $isFolder = true;
      }

      $parts = explode('/', $key);

      // add to tree
      $cur = &$tree;
      $numParts = count($parts);
      for ($i = 0; $i < $numParts; $i++) {
        $part = $parts[$i];

        // file
        if (!$isFolder && $i == $numParts-1 && !isset($cur[$part])) {
          $cur[$part] = $data;
          $cur[$part]['hsize'] = self::formatSize($data['size']);
          $cur[$part]['path'] = $cur[$part]['name'];
          $cur[$part]['name'] = $part;
        }
        // directory
        else {
          if (!isset($cur[$part])) {
            $path = implode('/', array_slice($parts, 0, $i+1));
            $cur[$part] = array(
              'path' => $path,
              'name' => $part,
              'files' => array());
          }
          $cur = &$cur[$part]['files'];
        }
      }
    }

    return $tree;
  }


  /////////////////////////////////////////////////////////////////////////////
  // Static functions
  /////////////////////////////////////////////////////////////////////////////

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

    return number_format($size, ($unit ? 2 : 0)).''.$units[$unit];
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

  // Sort with dirs first, then alphabetical ascending
  private static function sort($a, $b) {
    $a_is_dir = isset($a['files']);
    $b_is_dir = isset($b['files']);

    // dir > file
    if ($a_is_dir && !$b_is_dir) {
      return -1;
    } else if (!$a_is_dir && $b_is_dir) {
      return 1;
    }

    return strcasecmp($a['name'], $b['name']);
  }

}
