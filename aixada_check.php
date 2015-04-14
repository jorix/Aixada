<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="lang" content="en">
    <title>Aixada Check</title>
	<style type="text/css">
        #controlToggle {padding-left: 1em;}
        #controlToggle li {list-style: none;}
        .inline li {display: inline};
    </style>
  </head>
  <body>
	<ul>
		<li>mysqli: <?php 
			if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
				echo 'no mysqli :(';
			} else {
				echo 'we gots it';
			} ?></li>
		<li>PHP datetime: <?php echo date("Y-m-d H:i:s"); ?></li>
		<li>JavaScript datetime: <script>document.write(Date());</script></li>
		<li>JavaScript TimezoneOffset(hours): <script>
			var d = new Date;
			document.write(d.getTimezoneOffset()/60 + 'h');</script></li>
		<li>JSON datetime: <script>
			var d = new Date;
			document.write(d.toJSON());</script></li>
			<!--
			select date_add('2015-04-10', interval 1 day);
			-->
	</ul>
  </body>
</html>