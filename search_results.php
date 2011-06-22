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
<title>Scrutiny, search results</title>
<?php
//links to standard, site-wide stylesheets--put in an included file,
//to avoid code duplication by copying.
include("include/stylesheets");
?>
</head>
<body>
<?php
include("include/sql_connect");
//including header
include("include/header");
//including navbar
include("include/navbar");
print"<div class=\"farside\"></div>";
print "<div class=\"contents\">";
//verifying the three search terms are set, and putting them as are set
//in more useful/memorable variables
if($_GET["dept_code"]!="--" && $_GET["dept_code"]!="Dept."){
  $dept_code=$_GET["dept_code"];
 }
if(isset($_GET["class_number"]) && $_GET["class_number"]!="##"
   && $_GET["class_number"]!=""){
  $class_number=$_GET["class_number"];
 }
if(isset($_GET["professor"]) && $_GET["professor"]!="Professor"
   && $_GET["professor"]!=""){
  $prof=$_GET["professor"];
 }
if($_GET["class_name"] && $_GET["class_name"]!="Class name"){
  $class_name=$_GET["class_name"];
 }

/*Setting up a query. A geneneric query beginning is set up (fetching
all appropriate fields, regardless of their inclusion in the search
query) and terms of the WHERE clause are tacked on, depending on whether
the corresponding search term was submitted or no.*/

$query_text="SELECT DISTINCT sections.dept_code,sections.number,sections.name,sections.professor FROM sections,evaluations";

if(isset($dept_code)){
  //as this is the first term, no need for an AND.
  $query_text="$query_text WHERE sections.dept_code='$dept_code'";
}
if(isset($class_number)){
  if(!isset($dept_code)){//if there's not already a term in the WHERE clause
    $query_text="$query_text WHERE sections.number=$class_number";
  }
  else{
    $query_text="$query_text AND sections.number=$class_number";
  }
}
if(isset($prof)){
  if(!(isset($dept_code)||isset($class_number))){
    /*if neither previous search term has been set, 
and thus this is the first term*/
    $query_text="$query_text WHERE sections.professor LIKE \"%$prof%\"";
  }
  else{
    $query_text="$query_text AND sections.professor LIKE \"%$prof%\"";
  }
}

if($class_name){
  if(!($prof||$class_number||$dept_code)){
    $query_text="$query_text WHERE sections.name LIKE \"%$class_name%\"";
  }
  
  else{
    $query_text="$query_text AND sections.name LIKE \"%$class_name%\"";
  }
 }
if($prof||$class_number||$dept_code||$class_name)
  $query_text="$query_text AND evaluations.section_id=sections.section_id ORDER BY sections.dept_code, sections.number, sections.name, sections.professor";
 else
  $query_text="$query_text WHERE evaluations.section_id=sections.section_id ORDER BY sections.dept_code, sections.number, sections.name, sections.professor";
//actual query, using query string just set up

$query=mysql_query("$query_text") or die(mysql_error());
print"<ul>";
while($results=mysql_fetch_assoc($query)){//iterating through distinct results
  //setting a dummy variable to one, to indicate something actually printed.
  //to be used in determining whether to print and error message.
  $some_sections=1;

  //NB:I am here redefining the previous search-term variables.
  //Those values may still be accessed through the $_GET array,
  //should you need them at this point (hopefully not, now the search's done).
  $dept_code=$results["dept_code"];
  $class_number=$results["number"];
  $class_name=$results["name"];
  $prof=$results["professor"];

  //herein I print the years & semesters the classes were taught in the link
  $y_query=mysql_query("SELECT semester, year FROM sections WHERE dept_code='$dept_code' AND number=$class_number AND name=\"$class_name\" AND professor=\"$prof\"") or die(mysql_error());
  $years='(';
  while($year_row=mysql_fetch_assoc($y_query)){
    //assembling a string of semesters and years, formatted to go in search
    //results
    if($years!='('){
      $years.=", ";
    }
    switch($year_row['semester']){
    case 2:
      $semester="Fall";
      break;
    case 1:
      $semester="Spring";
      break;

    }
    $years .= "$semester ".$year_row['year'];
  }
  $years.=')';
  print"<li><p><a href=\"class_ratings.php?dept_code=$dept_code&amp;class_number=$class_number&amp;class_name=".urlencode($class_name)."&amp;prof=".urlencode($prof)."\">$dept_code $class_number $class_name, $prof $years</a></p></li>";
}
print"</ul>";
if(!isset($some_sections)){
  print "Your search returned no evaluations.";
 }
?>
</div>
</body>
</html>
