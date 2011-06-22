#!/usr/local/bin/php
<?php
ini_set('include_path', '..' . PATH_SEPARATOR . ini_get('include_path'));
include_once('setup_db.inc.php');
require_once('utils.inc.php');

function extract_time($field) {
    // Extract year/semester data
    if(!preg_match('/(\d{2})(\d{2})([FS])/', $field, $matches)) {
        echo 'Possible error: couldn\'t parse year ' . $field . "<br \>\n";
        return false;
    }

    return $matches;
}

function extract_course($field) {
    if(!preg_match('/([^-]+)-(\d+)[A-Z]?-\w+/', $field, $matches)) {
      echo "Warning: couldn't parse section $field. It will be ignored.";
      return false;
    }
    return $matches;
}

function ensure_section_exists($fields) {
    global $dbh;
    $field0 = extract_time($fields[0]);
    $field1 = extract_course($fields[2]);
    if(!($field0 and $field1)) return false;

    $section_record = array(
        'dept_code' => $field1[1],
        'number' => $field1[2],
        'semester' => ($field0[3] == 'S') ? 1 : 2,
        'year' => '20' . ($field0[3] == 'S' ? $field0[2] : $field0[1]),
        'professor' => str_replace('  ', ' & ', $fields[5]),
        'name' => $fields[4]
    );

    $sections = get_selected_sections(guarded_where_clause($section_record, false));
    if(count($sections) == 0) {    // Section not added yet
        $dbh->autoExecute('sections', $section_record);
        echo "Inserted record $section_id for " . make_section_title($section_record) . "<br />\n";
        return mysql_insert_id($dbh->connection);
    } else if(count($sections) == 1) {
        // Section exists
        return $sections[0]['section_id'];
    } else {
        // Error: these fields should constitute a unique key
        echo "Data matched more than one section!.<br />";
        return false;
    }
}

function extract_username($field) {
    if (preg_match("/(\w+)@(\w+)\.\w+/", $field, $matches)) {
        if ($matches[2] !== "amherst") return false;
        return $matches[1];
    } else {
        trigger_error("email formatted badly: $field");
        return false;
    }
}

function import_courses() {
    global $dbh;
    echo "Importing course data...<br>";
    $lines = fopen($_FILES['courses']['tmp_name'], 'r');
    fgetcsv($lines, 5000); // ignore the first line
    $line_no = 1;
    while(FALSE != ($fields = fgetcsv($lines, 5000))) {
        $line_no++;
        if ($fields[6] == '') continue; // email field may be blank

        $section_id = ensure_section_exists($fields);
        if($section_id === false) continue;
        
        $username = extract_username($fields[6]);
        if ($username === false) continue;

        $student = array(
            'section_id' => $section_id,
            'username' => $username,
        );
        
        $existing_record = $dbh->getAll("SELECT username, section_id FROM enrollment WHERE section_id = $section_id AND username LIKE '$username'");
        if (count($existing_record) != 0) { continue; }
         
        $dbh->autoExecute('enrollment', $student);
    }
    fclose($lines);
    echo "Import complete.";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_FILES['courses']['error'] == UPLOAD_ERR_OK) import_courses();
} else {
?>
<html>
<head>
<title>Scrutiny - Import Course Data</title>
<style>
body {
    background: #eef;
    font-size: 14px;
}
table, tr, td, th {
    border: solid 1px black;
}
</style>
</head>
<body>
<p>Upload the comma-separated-value file of the registrar's course schedule.</p>
<p>It should be in the following format:</p>
<table>
    <tr>
        <th>Term</th>
        <th>[UNUSED]</th>
        <th>Course Name</th>
        <th>[UNUSED]</th>
        <th>Course Title</th>
        <th>Faculty Names</th>
        <th>Student's Email</th>
    </tr>
    <tr>
        <td>1011F</td>
        <td>&nbsp;</td>
        <td>PHYS-17-02</td>
        <td>&nbsp;</td>
        <td>Electromagnetism &amp; Optics</td>
        <td>Arthur Zajonc&nbsp;&nbsp;Paul Bourgeois</td>
        <td>foobar14@amherst.edu</td>
    </tr>
</table>
<ul>
<li>The first line of the CSV file should be the column headings
    &mdash; don't take it out!</li>
<li>1011F means 'Fall Semester of the 2010-2011 Academic Year'</li>
<li>If multiple faculty are teaching, their names should be separated with two
    spaces</li>
</ul>
<form method = "post" enctype = "multipart/form-data">
<input type = "hidden" name = "MAX_FILE_SIZE" value = "4000000" />
<input type = "file" name = "courses"/>
<input type = "submit" value = "Send File" />
</form>
</body>
</html>
<?php } ?>
