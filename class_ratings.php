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
<title>
<?php
$prof=$_GET["prof"];
$class_number=$_GET["class_number"];
$dept_code=$_GET["dept_code"];
$class_name=$_GET["class_name"];
print"$dept_code $class_number, $prof";
?>
</title>
<?php
include("stylesheets");
?>
</head>
<body>
<?php
include("include/sql_connect");
include("include/header");
//including the navbar, so it's centralized & standard across the site
include("include/navbar");
?>
<div class="contents">
<?php
print"<h1 class=\"rating_title\">$dept_code $class_number: $class_name, $prof</h1>";
?>
<?php
/*NB that the page may display multiple sections, depending on whether
the professor taught the same class multiple years since 2002. Thus,
be prepared to deal with arbitrary numbers of section IDs.

In other words, fun with MySQL queries.*/

//a query to get relevant section IDs from DB
$query=mysql_query("SELECT section_id FROM sections WHERE dept_code='$dept_code' AND number=$class_number AND name='$class_name' AND professor='$prof' ORDER BY section_id") or die(mysql_error());

//now to iterate through the section IDs, and put them into an array.
//NB: this array will be reused later.
$ids=array();
while($sections=mysql_fetch_assoc($query)){
  $ids[]=$sections['section_id'];
}
?>

<div class="ratings">
<h2 style="text-align:center">Quantitative Analysis</h2>
<p>
<table border="1">
  <tr>
      <th rowspan="2">All sections</th>
      <th colspan="2">Agree</th>
      <td></td>
      <th colspan="2">Disagree</th>
      <th colspan="2"></th>
  </tr>
  <tr>
      <th width="6%">5</th>
      <th width="6%">4</th>
      <th width="6%">3</th>
      <th width="6%">2</th>
      <th width="6%">1</th>

      <th width="6%">NA</th>
      <th width="6%">Avg</th> 
  </tr>
<?php
/*constructing a table from the SQL database, fetching questions and results
from ratings. NB: each page will display multiple sections, and the
database arranges ratings by section*/

$question_query=mysql_query("SELECT question_id, question_text FROM rating_questions WHERE is_active=1 ORDER BY question_id") or die(mysql_error());
while($result=mysql_fetch_assoc($question_query)){
  print("<tr><td>" . $result['question_text'] . "</td>");
  $q_id=$result['question_id'];
  $ratings=array(0,0,0,0,0,0);
  $r_query_text="SELECT rating FROM ratings WHERE question_id=$q_id AND (section_id=".$ids[0];
  foreach($ids as $id){
    $r_query_text="$r_query_text OR section_id=$id";
  }
  $r_query=mysql_query("$r_query_text) ORDER BY rating") or die(mysql_error());
  
  while($results=mysql_fetch_assoc($r_query)){
    $rating=$results['rating'];
    $ratings[$rating]++;
  }
  //since array is in ascending order, must reverse before putting in table
  $r_ratings=array_reverse($ratings);

  foreach($r_ratings as $rating){
    print "<td>$rating</td>";
  }
  $sum=0;
  $num=0;
  for($i=5;$i>0;$i--){
    $sum+=$ratings[$i]*$i;
    $num+=$ratings[$i];
  }
  if($num!=0){
    $avg=round($sum/$num,2);
  }
  else{
    $avg=0;
  }
  print "<td>$avg</td>";
  print"</tr>";
}
?>

</table>
<table border="1">
  <tr>
      <th></th>
      <th>N/A</th>
      <th>0-2</th>
      <th>2-4</th>
      <th>4-6</th>
      <th>6-8</th>
      <th>8-10</th>

      <th>10+</th>
      <th>Avg</th>
   </tr>
   <tr>
       <th>Hours of work per week:</th>
<?php
//putting together a section of a future MySQL query that will have
//all the needed section numbers, grouped together in parentheses,
//ready for a WHERE clause.
$id_query_section="(section_id=$ids[0]";
foreach($ids as $id){
  //This will produce a query that starts "$id[0] or $id[0]".
  //Since that's logically equivalent to just "$id[0]", I left it like that
  //for simplicity's sake. The question of which inefficiency (on the
  //code side or the query side) seems minor enough to be largely academic.
  $id_query_section="$id_query_section || section_id=$id";
}
$id_query_section="$id_query_section)";//finishing id boolean off with paren

//Due to a bug in the original implementation, those entries submitted
//before Fall 2007 that should have been null seem to have been
//entered as zero in the DB--which is too bad (not what the user
//intended) but can't be helped without falsifying 
//statistics more than we already are. The original coders were morons.

//Generic query. Would fetch all evals where the section IDs are for this
//class--in this implementation, it gets amended before each query, selecting
//only the evals with the proper number of work hours.
//One only needs to know number of such entries.
$query_text=("SELECT COUNT(*) FROM evaluations WHERE $id_query_section");

//null query--i.e., "N/A"
$query=mysql_query("$query_text AND work_hours IS NULL");
$result=mysql_fetch_array($query);
print "<td>".$result['COUNT(*)']."</td>";

//iterating through the six middle queries
for($i=1;$i<=5;$i++){
  $query=mysql_query("$query_text AND work_hours>=".(2*$i-2)." AND work_hours<".(2*$i));
  $result=mysql_fetch_array($query);
  print "<td>".$result['COUNT(*)']."</td>";
}
//greater than 10 query
$query=mysql_query("$query_text AND work_hours>=10");
$result=mysql_fetch_array($query);
print "<td>".$result['COUNT(*)']."</td>";

//now for the average.
$query=mysql_query("SELECT COUNT(*) FROM evaluations WHERE $id_query_section && work_hours IS NOT NULL") or die(mysql_error());
$num=mysql_result($query,0);
if($num!=0){
  $query=mysql_query("SELECT work_hours FROM evaluations WHERE work_hours IS NOT NULL AND $id_query_section");
  $sum=0;
  while($result=mysql_fetch_row($query)){
    $sum+=$result[0];
  }
  $avg=round($sum/$num,2);
}
else{
  $avg=0;
}
print "<td>$avg</td>";
?>
</tr>
</table>
</p>
<?php
foreach($ids as $id){
  print'
<p>
<table border="1">
  <tr>';
  $y_query=mysql_query("select year,semester from sections where section_id=$id");
  $y_row=mysql_fetch_assoc($y_query);
  switch($y_row['semester']){
  case 2:
    $semester="fall";
    break;
  case 1:
    $semester="spring";
    break;
  }
  $year=$y_row['year'];
  print"<th rowspan=\"2\">$semester $year</th>";
  print'
      <th colspan="2">Agree</th>
      <td></td>
      <th colspan="2">Disagree</th>
      <th colspan="2"></th>
  </tr>
  <tr>
      <th width="6%">5</th>
      <th width="6%">4</th>
      <th width="6%">3</th>
      <th width="6%">2</th>
      <th width="6%">1</th>

      <th width="6%">NA</th>
      <th width="6%">Avg</th> 
  </tr>';
  /*constructing a table from the SQL database, fetching questions and results
from ratings. NB: each page will display multiple sections, and the
database arranges ratings by section*/

  $question_query=mysql_query("SELECT question_id, question_text FROM rating_questions WHERE is_active=1 ORDER BY question_id") or die(mysql_error());
  while($result=mysql_fetch_assoc($question_query)){
    print("<tr><td>" . $result['question_text'] . "</td>");
    $q_id=$result['question_id'];
    
    for($i=5;$i>=0;$i--){
      $q=mysql_query("SELECT COUNT(*) FROM ratings WHERE question_id=$q_id ".
		     "AND section_id=$id AND rating=$i");
      $n=mysql_result($q,0);
      print "<td>$n</td>";
    }
    $r_query_text="SELECT rating FROM ratings WHERE question_id=$q_id AND (section_id=$id";
    $r_query=mysql_query("$r_query_text) ORDER BY rating") or die(mysql_error());
    $sum=0;
    while($row=mysql_fetch_row($r_query)){
      $sum+=$row[0];

    }
    $n_query=mysql_query("SELECT COUNT(*) FROM ratings".
			 " WHERE question_id=$q_id AND section_id=$id")
      or die(mysql_error());
    $num=mysql_result($n_query,0);
    if($num)
      $avg=round($sum/$num,2);
    else
      $avg=0;
    print "<td>$avg</td>";
    print"</tr>";
  }
  print '
</table>
<table border="1">
  <tr>
      <th></th>
      <th>N/A</th>
      <th>0-2</th>
      <th>2-4</th>
      <th>4-6</th>
      <th>6-8</th>
      <th>8-10</th>

      <th>10+</th>
      <th>Avg</th>
   </tr>
   <tr>
       <th>Hours of work per week:</th>';
  
  //Due to a bug in the original implementation, those entries submitted
  //before Fall 2007 that should have been null seem to have been
  //entered as zero in the DB--which is too bad (not what the user
  //intended) but can't be helped without falsifying 
  //statistics more than we already are. The original coders were morons.

  //Generic query. Would fetch all evals where the section IDs are for this
  //class--in this implementation, it gets amended before each query, selecting
  //only the evals with the proper number of work hours.
  //One only needs to know number of such entries.
  $query_text=("SELECT COUNT(*) FROM evaluations WHERE section_id=$id");

  //null query--i.e., "N/A"
  $query=mysql_query("$query_text AND work_hours IS NULL");
  $result=mysql_fetch_array($query);
  print "<td>".$result['COUNT(*)']."</td>";

  //iterating through the six middle queries
  for($i=1;$i<=5;$i++){
    $query=mysql_query("$query_text AND work_hours>=".(2*$i-2)." AND work_hours<".(2*$i));
    $result=mysql_fetch_array($query);
    print "<td>".$result['COUNT(*)']."</td>";
  }
  //greater than 10 query
  $query=mysql_query("$query_text AND work_hours>=10");
  $result=mysql_fetch_array($query);
  print "<td>".$result['COUNT(*)']."</td>";

  //now for the average.
  $query=mysql_query("SELECT COUNT(*) FROM evaluations WHERE section_id=$id && work_hours IS NOT NULL") or die(mysql_error());
  $result=mysql_fetch_array($query);
  $num=$result['COUNT(*)'];
  if($num!=0){
    $sum=0;
    $query=mysql_query("SELECT work_hours FROM evaluations WHERE work_hours IS NOT NULL AND section_id=$id");
    while($result=mysql_fetch_assoc($query)){
      $sum+=$result['work_hours'];
    }
    $avg=round($sum/$num,2);
  }
  else{
    $avg=0;
  }
  print "<td>$avg</td>";

  print '
</tr>
</table>
</p>';

}
?>
</div>
<?php
//fetching comment-questions
$q_query=mysql_query("SELECT question_id,question_text FROM comment_questions WHERE is_active=1 ORDER BY ordering");
while($q_row=mysql_fetch_assoc($q_query)){
  $q_id=$q_row['question_id'];
  $question=$q_row['question_text'];
  print "<h2>$question</h2>";
  $c_query=mysql_query("SELECT section_id,submitted_by,comment FROM comments WHERE $id_query_section AND question_id=$q_id");
  while($c_row=mysql_fetch_assoc($c_query)){
    if($c_row['comment']==""){
      continue;
    }
    //NB: using c_row section ID here, not global section id array.
    $s_query=mysql_query("SELECT major_status,class_year FROM evaluations WHERE section_id=".$c_row['section_id']." AND submitted_by=\"".$c_row['submitted_by']."\"") or die(mysql_error());
    $s_row=mysql_fetch_assoc($s_query);
    //now to get the year the section was held
    $y_query=mysql_query("SELECT year,semester FROM sections WHERE section_id=".$c_row['section_id']);
    $y_row=mysql_fetch_assoc($y_query);
    //properly determining fall or spring semester
    switch($y_row['semester']){
    case 2:
      $semester="Fall";
      break;
    case 1:
      $semester="Spring";
      break;
    }
    switch($s_row['major_status']){
    case 1:
      $major_status="non-major";
      break;
    case 2:
      $major_status="potential major";
      break;
    case 3:
      $major_status="major";
      break;
    }
    print "<h3>A";
    if($s_row['class_year']!=0){
      print " ".$s_row['class_year'];
    }
    print" $major_status in $semester ".$y_row['year'].":</h3>\n";
    print "<blockquote>".$c_row['comment']."</blockquote>\n";
  }
}
?>

</div>
<div class="farside"></div>
</body>
</html>
