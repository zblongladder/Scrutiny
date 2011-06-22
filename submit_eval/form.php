#!/usr/local/bin/php
<?php
   //all the usual header crap, in a single include file. 
include("include/init_template");
?>
<head>
<?php
//including code to connect to SQL database
include("include/sql_connect");
$s_query=mysql_query("SELECT * FROM sections WHERE section_id=".$_POST['section_id']) or die(mysql_error());
$s_row=mysql_fetch_assoc($s_query);
$s_number = chop($s_row['number']);
if(preg_match("/^\d$/",$s_number)){
  $s_number="0$s_number";
 }
switch($s_row['semester']){
 case 1:
   $semester="Spring";
   break;
 case 2:
   $semester="Fall";
   break;
 }
print("<title>Submit evaluation for "
      .$s_row['dept_code'].$s_number.
      ", ".$s_row['professor'].
      ", $semester ".$s_row['year']." - Scrutiny</title>");

include("include/stylesheets");

?>
</head>
<body>

<?php
include("include/header");
//including the navbar, so it's centralized & standard across the site
include("include/navbar");

$id=$_POST['section_id'];

//getting the user's username (since he just signed in, per htaccess)
$user=$_SERVER['REMOTE_USER'];
//here one determines if this has been evaluated before by this user.
//if so, values will default to previously-submitted values.
$t_query=mysql_query("SELECT COUNT(*) FROM evaluations".
		     " WHERE section_id=$id".
		     " AND submitted_by='$user'") or die(mysql_error());
$exists=mysql_result($t_query,0);
?>
<div class="farside"></div>
<div class="contents">
<?php
print("<h1>Evaluation for "
      .$s_row['dept_code'].$s_number.", ".htmlentities($s_row['name']).
      ", taught by ".htmlentities($s_row['professor']).", $semester "
      .htmlentities($s_row['year'])."</h1>");
?>
<form action="submit.php" method="post">
<?php
print "<input type='hidden' name='section_id' value=$id />"
?>
<?php
if(preg_match("/^\D*(\d\d)$/", $user, $classnum)
   && $_POST['yr_different']!="yes"){
  /*if user has a username in the form of 
   first_initial+last_name+last_two_digits_of_class_year (currently, everyone
   post-2007), use username to determine class year, and take no input*/
  print("<input type='hidden' name='class_year' value='20".
	$classnum[1]."'/>");
}
 else{
   $curr_year=date("Y");
   if($exists){
     $y_query=mysql_query("SELECT class_year FROM evaluations ".
			  "WHERE submitted_by='$user' AND section_id=$id")
       or die(mysql_error());
     $entered_year=mysql_result($y_query,0);
   }
   print("<p>Enter your class year:<select name='class_year'>");
   for($i=$curr_year+4; $i>2001; $i--){
     print("<option value='$i'");
     if($entered_year)
       if($entered_year==$i)
	 print "selected='selected'";
     print(">$i</option>");
   }
   print("</select></p>");
 }
?>
<?php
if($exists){
  $m_query=mysql_query("SELECT major_status FROM evaluations ".
		       "WHERE submitted_by='$user' AND section_id=$id")
    or die(mysql_error());
  $m_status=mysql_result($m_query,0);
 }
?>
<p>Are you majoring in the subject this class was in?</p>
<ul style="padding:2">
<li><input type="radio" value=3 name="major_status" 
<?php
if($exists)
  if($m_status==3)
    print "checked='checked' ";
?>
/> Yes.</li>
<li><input type="radio" value=1  name="major_status" 
<?php
if(($m_status==1)||(!$exists))
    print "checked='checked' ";
?>
/> No.</li>
<li><input type="radio" value=2  name="major_status" 
<?php
if($exists)
  if($m_status==2)
    print "checked='checked' ";
?>
/> Not yet, but I'm thinking about it.</li>
</ul>
<p>How many hours per week outside of class did you work for this class?
<input type="text" size=2 name="work_hours" 
<?php
if($exists){
  $wh_query=mysql_query("SELECT work_hours FROM evaluations ".
			"WHERE section_id=$id AND submitted_by='$user'");
  $hours=mysql_result($wh_query,0);
  if($hours)
    print "value=$hours ";
}
?>
/></p>
<!--NB: Do NOT give a default numerical value to the work-hours textbox.
The system should let the DB default to NULL if no value is given

A default value should ONLY be given if the user has previously submitted
an evaluation for that section.-->

<ul>
<h2>Ratings (1 &ndash; strongly disagree; 5 &ndash; strongly agree)</h2>
<?php
$r_query=mysql_query("SELECT question_id, question_text ".
		     "FROM rating_questions WHERE is_active=1");

while($r_row=mysql_fetch_row($r_query)){
  if($exists){
  $rr_query=mysql_query("SELECT rating FROM ratings ".
			"WHERE section_id=$id AND submitted_by='$user'".
			"AND question_id=".$r_row[0]) or die(mysql_error());
  $prev_rating=mysql_result($rr_query,0);
  }
  print("<li><p>".$r_row[1]." <select name='rating".
	$r_row[0]."'>");
  for($i=0; $i<6; $i++){
    print("$<option value=$i");
    if($exists)
      if($i==$prev_rating){
	print " selected='selected'";
      }
    print ">";
    if($i)
      print "$i";
    else
      print "N/A";
    print "</option>\n";
  }
  print "</select><p></li>";
 }
print "</ul>";
?>
<h2>Comments</h2>
<?php
$c_query=mysql_query("SELECT question_id, question_text ".
		     "FROM comment_questions WHERE is_active=1".
" ORDER BY ordering") 
  or die(mysql_error());
print "<ul>";
while($c_row=mysql_fetch_row($c_query)){/*As only one result per row,
					     mysql_fetch_row, so result is
					     $c_row[0] (easier to type than
					     $c_row['question_text']*/
  if($exists){
    $cr_query=mysql_query("SELECT comment FROM comments WHERE ".
			  "section_id=$id AND submitted_by='$user' ".
			  "AND question_id=".$c_row[0]) or die(mysql_error());
    $prev_comment=mysql_result($cr_query,0);
  }
  print("<li><p>".$c_row[1]."</p><textarea name='comment".
	$c_row[0]."' rows=10 cols=50>");
  if($exists)
    print $prev_comment;
  print"</textarea></li>";
 }
print "</ul>";
print "<input type='submit' value='Submit' /></form>";
?>
</div>
</body>
</html>
