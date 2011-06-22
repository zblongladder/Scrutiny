#!/usr/local/bin/php
<?php
include_once('../setup_db.inc.php');
require_once('../utils.inc.php');

if($_SERVER['REQUEST_METHOD'] == 'GET') {
  if(isset($_GET['section_id']) and is_numeric($_GET['section_id'])) { 
    $section_id = $_GET['section_id'];
    $section = get_selected_sections("section_id = $section_id");
    $section = $section[0];
  } else {
    $where_clause = guarded_where_clause($_GET) . ' AND summary = ""';
    $sections = get_evaled_sections($where_clause, '', 'num_evaluations DESC,');
    $include_name = 'edit_summary_list';
  }
} else { 
  $section_id = addslashes($_POST['section_id']);
  $dbh->autoExecute('sections', array(
     'summary' => $_POST['summary'],
     'summarized_by' => get_login()
  ), DB_AUTOQUERY_UPDATE, "section_id = $section_id");
  $include_name = 'edit_summary_do';
}
include('../main.tpl');
?>
