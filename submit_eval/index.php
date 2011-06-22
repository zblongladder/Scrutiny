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

//getting the user's username (since he just signed in, per htaccess)
$user=$_SERVER['REMOTE_USER'];
?>
<div class="farside"></div>
<div class="contents">
<p>Select a class for which to submit an evaluation.
Classes for which you have previously submitted evaluations are marked in
<strong>bold</strong></p>
<?php
$u_query=mysql_query("SELECT sections.section_id,sections.dept_code,sections.number,sections.name,sections.year,sections.semester,sections.professor FROM enrollment,sections WHERE enrollment.section_id=sections.section_id AND enrollment.username=\"$user\"") or die(mysql_error());
?>

<?php
print "<form action='form.php' method='post'><ul>";
while($u_row=mysql_fetch_assoc($u_query)){
  $t_query=mysql_query("SELECT COUNT(*) FROM evaluations WHERE". 
		       " submitted_by='$user' AND section_id=".
		       $u_row['section_id']) or die(mysql_error());
  $exists=mysql_result($t_query,0);
  switch($u_row['semester']){
  case 2:
    $semester="Fall";
    break;
  case 1:
    $semester="Spring";
    break;
  }
  //making "01" level classes and such prettier, with a preceding "0"
  $u_number = chop($u_row['number']);
  if(preg_match("/^\d$/",$u_number)){
    $u_number="0$u_number";
  }
  print "<li>";
  if($exists)
    print "<strong>";
  print "<input name='section_id' type='radio' value='"
    .$u_row['section_id']."'>".
    $u_row['dept_code'].$u_number." ".htmlentities($u_row['name']).", ".
    htmlentities($u_row['professor']).
    ", $semester ".$u_row['year']."</input>\n";
  if($exists)
    print "</strong>";
  print "</li>";
}
?>
</ul>
<?php
if(preg_match("/^\D*(\d\d)$/",$user,$class_year)){
  print '<p><input type="checkbox" name="yr_different" value="yes" />';
  print("Check if you will graduate in a year other than 20".
	$class_year[1]."</p>");
  
 }
?>
<input type="submit" value="Go to evaluation" />
</form>

</div>
</body>
</html>
