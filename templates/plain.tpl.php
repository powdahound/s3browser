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
	    padding: 10px;
	  }
	  p, li, td, div {
      font-size: 0.9em;
	  }
	  h1 {
	    margin-bottom: 15px;
	  }
	  h2 {
	    font-size: 1em;
	    margin: 5px 0;
	  }
	  ul {
	    margin-left: 5px;
	  }
	  li {
 	    margin-left: 15px;
	  }
	</style>
</head>
<body>
  <h1><?= $config['bucket-name'] ?></h1>
  <? foreach ($s3b->getBreadcrumb($dir) as $key => $name): ?>/<a href="?dir=/<?= $name ?>"><?= $key ?></a><? endforeach ?>
  <ul>
  <? foreach ($files as $key => $info): ?>
    <li>
      <? if ($info['size'] == 16): ?>
      <a href="?dir=/<?= $info['name'] ?>"><?= $key ?>/</a>
      <? else: ?>
      <a href="<?= $config['bucket-url-prefix'] ?>/<?= $info['name'] ?>"><?= $key ?></a>
      <? endif; ?>
    </li>
  <? endforeach; ?>
  </ul>
  
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
