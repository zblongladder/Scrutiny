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
<div class="farside"></div>
<?php
//including code to connect to SQL database
include("include/sql_connect");
include("include/header");
//including the navbar, so it's centralized & standard across the site
include("include/navbar");
?>
<div class="contents">
  <p>3 March 2008: Fixed last two semesters not appearing.</p>
  <p>1/7/08: Fixed some bugs pertaining to submit form. If you've had 
trouble submitting evaluations, try resubmitting. If you still can't
  submit, <a href="contact.php">contact us</a> so we can fix it.</p>
  <p>9/24/07: Fiddled around with styles and general look. Drop us a note; tell
  us what you think.</p>
  <p>9/5/07: Added "what's new" section. Changed navbar color from former ugly
  "purple" to the darker #660066, trying for more of an Amherst purple that 
  isn't hard on the eyes. Still working on the color scheme.</p> 
</div>
</html>