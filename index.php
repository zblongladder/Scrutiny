#!/usr/local/bin/php

<?php
/*for some reason, the <? on the xml thing was confusing the 
PHP. Thus, this workaround.*/
print "<?xml version='1.0' encoding='UTF-8'?>";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<!--

NB:

Per the instructions I've been given, the index page doubles as a search page.

It makes sense if you're navigating the site.
-->
<head>
<title>Scrutiny</title>
<?php
include("include/stylesheets");
?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19272049-3']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>
<body>
<?php
//including code to connect to SQL database
include("include/sql_connect");

include("include/header");
//including the navbar, so it's centralized & standard across the site
include("include/navbar");
?>
<div class="contents">

<p>1/8/11: The course data have been updated. Five College courses are now
included as well. For example, social science courses from Hampshire College
are listed under H/SS, and math courses from Mt. Holyoke are listed under
M/MATH. Submit your evaluations now!</p>

<p>3/3/09: Last two semesters now appear in the database. Sorry for the
inconvenience.</p>

<p>1/21/08: Several severe layout bugs pertaining to Internet Explorer 7 and
lower have recently been reported. These come from Microsoft's erratic
implementation of HTML standards.  If you experience layout problems (such as
the main panel shrinking unusably small), try using <a
href="http://www.getfirefox.com">Mozilla Firefox</a> until we have resolved
these issues.</p>

  <p>1/7/08: Fixed some bugs pertaining to submit form. If you've had 
trouble submitting evaluations, try resubmitting. If you still can't
  submit, <a href="contact.php">contact us</a> so we can fix it.</p>
  <p>5/9: Added a "What's New" section, changed the color scheme to a 
  darker purple, per the complaints we were getting about the color scheme.
  Good luck starting classes, and we hope you find Scrutiny helpful in 
  choosing courses!</p>
<p>Welcome to the new Scrutiny! Everything's new, shiny, and rewritten, so
be sure to look around and see how you like it. Feel free to 
<a href="contact.php">drop us a note</a> &amp; tell us what you think
 (<em>especially</em> if you find
something you think isn't working right--bug reports are much appreciated!).
</p>
<form action="search_results.php" method="get">
<p>Department code 
<select name="dept_code">
<option selected="selected">--</option>
<?php
//making a drop-down menu of the dept. codes
//MySQL query. Pretty self-explanatory.
$query=mysql_query("SELECT DISTINCT dept_code FROM sections");
//iterating through result
$amherst_courses = array();
$five_college_courses = array();
while($result=mysql_fetch_assoc($query)){
  if (strpos($result['dept_code'], "/") === false)
      array_push($amherst_courses, $result['dept_code']);
  else
      array_push($five_college_courses, $result['dept_code']);
}
sort($amherst_courses);
sort($five_college_courses);
foreach($amherst_courses as $course)
  print "<option>$course</option>";
foreach($five_college_courses as $course)
  print "<option>$course</option>";
?>
</select>
</p>
<p>Class number 
<input type="text" name="class_number" value="##" onfocus="this.value='';" size="2" maxlength="2"/></p>
<p>Class name <input type="text" name="class_name" /></p>
<p>Professor 
<input type="text" name="professor" value="Professor" onfocus="this.value='';"/></p>
<p><input type="submit" /></p>
</form>
</div>
<div class="farside"></div>
</body>
</html>
