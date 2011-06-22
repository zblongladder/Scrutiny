#!/usr/local/bin/php
List of random evaluations:
<ol>
<?php 
include('../setup_db.inc.php');
$number = $_GET['number'];
$lastRun = isset($_GET['date']) ? $_GET['date']
           : $dbh->getOne('SELECT date_last_run FROM raffle_data');

$names = array_unique($dbh->getCol('SELECT submitted_by FROM evaluations ' .
                                   "WHERE time_submitted > \"$lastRun\" " .
                                   "ORDER BY RAND() LIMIT $number"));
foreach ($names as $name) {
  echo "<li>$name</li>";
} 

$dbh->query('UPDATE raffle_data SET date_last_run = NOW()');
?>
</ol>
Taking evaluations submitted since <?php echo $lastRun; ?>.  
Code, for those who are curious, is:<pre>
<?php 
$text = file_get_contents('random_evaluation.php'); 
$text = preg_replace('/&/', '&amp;', $text);
$text = preg_replace('/</', '&lt;', $text);
$text = preg_replace('/>/', '&gt;', $text);
echo $text;
?>
</pre>

