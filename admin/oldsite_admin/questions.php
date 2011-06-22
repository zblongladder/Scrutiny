#!/usr/local/bin/php
<?php
ini_set('include_path', '..' . PATH_SEPARATOR . ini_get('include_path'));
include_once('setup_db.inc.php');
require_once('utils.inc.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  print_r($_POST);
  foreach($_POST as $name => $value) {
    $parsed = explode('_', $name);
    $action = $parsed[0];
    $table = $parsed[1] . '_questions';
    $id = $parsed[2];
    
    if($action == 'update') {
      $dbh->autoExecute($table, array('question_text' => $value, ), DB_AUTOQUERY_UPDATE, "question_id = $id");
    } else if($action == 'delete') {
      $dbh->autoExecute($table, array('is_active' => '0'), DB_AUTOQUERY_UPDATE, "question_id = $id");
    } else if($action == 'new' and $value != '') {
      $dbh->autoExecute($table, array('question_text' => $value));
    }
  }
}

$rating_questions = get_questions('rating');
$comment_questions = get_questions('comment');
?>
<html>
<head>
<title>Scrutiny! Administration</title>
</head>
<body>
<h1 align = "center">Question administration for Scrutiny</h1>
<form method = "post">
<h3>Rating questions</h3>
These questions let respondents rate the course on a scale of 1-5.  They do not allow open-ended responses.
<table>
<?php foreach ($rating_questions as $question) { ?>
  <tr>
    <td><input type = "text" size = "60"
               name = "update_rating_<?php echo $question['question_id'] ?>" 
               value = "<?php echo $question['question_text'] ?>" /></td>
    <td><input type = "checkbox"
               name = "delete_rating_<?php echo $question['question_id'] ?>" />Delete question</td>
  <tr>
<?php } ?>
</table>
Or add new question: <input type = "text" name = "new_rating_0" size = "60" /><br /> 
<h3>Comment questions</h3>
These questions allow for open-ended responses.  They appear as a text box on the evaluation.
<table>
<?php foreach ($comment_questions as $question) { ?>
  <tr>
    <td><input type = "text" size = "60"
               name = "update_comment_<?php echo $question['question_id'] ?>" 
               value = "<?php echo $question['question_text'] ?>" /></td>
    <td><input type = "checkbox"
               name = "delete_comment_<?php echo $question['question_id'] ?>" />Delete question</td>
  <tr>
<?php } ?>
</table>
Or add new question: <input type = "text" name = "new_comment_0" size = "80"/><br /> 
<input type = "submit" />
</form>
</body>
</html>
