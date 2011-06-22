#!/usr/local/bin/php
<?php
include("include/sql_connect");
  

$r_query=mysql_query("SELECT MAX(question_id) FROM rating_questions");
$rating_count=mysql_result($r_query,0);


$c_query=mysql_query("SELECT MAX(question_id) FROM comment_questions");
$comment_count=mysql_result($c_query,0);

$user=$_SERVER['REMOTE_USER'];
$query_text=("REPLACE INTO evaluations SET section_id=".$_POST['section_id'].
	     ", submitted_by='$user', major_status=".$_POST['major_status'].
	     ", class_year=".$_POST['class_year'].", time_submitted='".
	     date("Y-m-d H:i:s")."'");

if(isset($_POST['work_hours'])) {
    if ($_POST['work_hours'] == '')
        $_POST['work_hours'] = "NULL";
  $query_text.=(", work_hours=".$_POST['work_hours']);
mysql_query($query_text) or die(mysql_error());
}

for($i=1; $i<=$rating_count; $i++){
  if(isset($_POST["rating$i"])){
    mysql_query("".
		"REPLACE INTO ratings SET question_id=$i, ".
		"submitted_by='$user', section_id=".
		$_POST['section_id'].", rating=".
		$_POST["rating$i"]) or die(mysql_error());
  }
 }

for($i=1; $i<=$comment_count; $i++){
  if(isset($_POST["comment$i"])){
    mysql_query("".
		"REPLACE INTO comments SET question_id=$i, ".
		"submitted_by='$user', section_id=".
		$_POST['section_id'].", comment='".
					 $_POST["comment$i"]."'")
      or die(mysql_error() . " 40");
  }
 }
//redirecting back to index
header("Location: ./submitted.php");
exit;

?>
