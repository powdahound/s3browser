<?/*
 * 
 * Light blue: #1a52d6
 * Dark blue:  #011826
 * Green:      #2f7360
 * 
 */?>
<? if (empty($files)) header("HTTP/1.0 404 Not Found"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?= $config['bucket-name'] ?></title>
	<style type="text/css" media="screen">
	  * {
	    font-family: verdana, sans-serif;
	    padding: 0;
      margin: 0;
	  }
	  
	  body {
	    background-color: #fff;
	  }
	  
	  p, li, td, div {
      font-size: 13px;
	  }
	  
    img {
      border: 0;
    }
    
	  h2 {
	    font-size: 1em;
	    margin: 5px 0;
	  }

	  ul {
 	    list-style: none;
	  }
  	  ul li {
   	    list-style: none;
  	  }
    	  ul li img {
    	    vertical-align: middle;
    	  }
	  
	  span.size {
	    color: #ccc;
	    font-size: 10px;
	  }
	  
	  a {
	    text-decoration: none;
 	    color: #666;
	  }
  	  a:hover {
  	    color: #000;
  	  }
  	  a span {
  	    text-decoration: underline;
  	  }
	  
	  div.breadcrumb {
	    margin-bottom: 5px;
	    border-bottom: 1px solid #eee;
	  }
	    div.breadcrumb, div.breadcrumb a {
	      color: #000;
              font-size: 18px;
              font-weight: normal;
	    }
	    div.breadcrumb ul {
	      display: inline;
	      margin-left: 5px;
	    }
	    div.breadcrumb ul li {
	      display: inline;
	      margin-left: -5px;
	    }
              div.breadcrumb ul li a:hover {
                background-color: #efefef;
              }
	  
	  #header {
	    background-color: #999;
	    padding: 10px 15px;
	    border-bottom: 1px solid #ccc;
	  }
	    #header h1 {
	      color: #000;
	      font-size: 26px;
	    }
	  
	  #contents {
	    border-top: 1px solid #eee;
 	    padding: 15px 20px;
	  }
	  
	  #footer {
	    border-top: 1px solid #ddd;
	    padding: 2px 5px;
	  }
	    #footer p {
	      font-size: 11px;
	      color: #999;
	      text-align: right;
	    }
	    #footer a {
              color: #999;
 	      text-decoration: underline;
	    }
	</style>
</head>
<body>
  <div id="header">
    <h1><?= $config['page-title'] ?></h1>
  </div>
  
  <div id="contents">
    
    <div class="breadcrumb">
      Index of 
      <ul>
        <li><a href="<?= $c['base-path'] ?>/"><?= $config['bucket-name'] ?>/</a></li>
        <? foreach (S3Browser::getBreadcrumb($dir) as $key => $name): ?>
        <? if ($key != '/'): ?>
        <li><a href="<?= $c['base-path'] ?>/<?= $name ?>"><?= $key ?>/</a></li>
        <? endif; ?>
        <? endforeach ?>
      </ul>
    </div>
  
    <? if (empty($files)): ?>
      <p>No files found with that prefix.</p>
    <? else: ?>
    <ul>

    <? if (S3Browser::getParent($dir) !== null): ?>
      <li>
        <a href="<?= $c['base-path'] ?>/<?= S3Browser::getParent($dir) ?>">
          <img src="<?= $c['base-path'] ?>/themes/plain/img/arrow_top.gif">
          <span>..</span>
        </a>
      </li>
    <? endif; ?>
    <? foreach ($files as $key => $info): ?>
         <? $asTorrent = (!is_null($c['torrent-threshold']) && $info['size'] > $c['torrent-threshold']); ?>
      <li>
        <? if ($info['size'] == 16): ?>
        <a href="<?= $c['base-path'] ?>/<?= $info['name'] ?>">
          <img src="<?= $c['base-path'] ?>/themes/plain/img/folder.gif">
          <span><?= $key ?></span>
        </a>
        <? else: ?>
           <a href="<?= $config['bucket-url-prefix'] ?>/<?= $info['name'] ?><? if ($asTorrent): ?>?torrent<? endif; ?>" <? if (isset($c['google-analytics-id'])): ?>onclick="javascript:pageTracker._trackPageview('<?= $info['name'] ?>');"<? endif; ?>>
          <img src="<?= $c['base-path'] ?>/themes/plain/img/file.gif">
          <span><?= $key ?></span>
        </a>
        <span class="size"><?= $info['hsize'] ?></span>
        <? endif; ?>
      </li>
    <? endforeach; ?>
    </ul>
    <? endif; ?>
  
  </div>
  
  <div id="footer">
    <p>Powered by <a href="http://code.google.com/p/s3-directory-browser/" target="_blank">s3-directory-browser</a></p>
  </div>

  <? if (isset($config['google-analytics-id'])): ?>
  <script type="text/javascript">
  var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
  document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
  </script>
  <script type="text/javascript">
  var pageTracker = _gat._getTracker("<?= $config['google-analytics-id'] ?>");
  pageTracker._initData();
  pageTracker._trackPageview();
  </script>
  <? endif; ?>
</body>
</html>
