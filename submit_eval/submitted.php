#!/usr/local/bin/php
<?php
/*for some reason, the <? on the xml thing was confusing the 
PHP. Thus, this workaround.*/
print "<?xml version='1.0' encoding='UTF-8'?>";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Scrutiny</title>
<?php
include("include/stylesheets");
?>
</head>
<body>

<?php
//including code to connect to SQL database
include("include/sql_connect");
include("include/header");
//including the navbar, so it's centralized & standard across the site
include("include/navbar");

//getting the user's username (since he just signed in, per htaccess)
$user=$_SERVER['REMOTE_USER'];
?>
<div class="farside"></div>
<div class="contents">
  <form action="../">
  <p>Your evaluation was successfully submitted.</p>
  <p>Thank you for submitting an evaluation!</p>
  <input type="submit" Value="Return to homepage" />
</div>
</body>
</html>