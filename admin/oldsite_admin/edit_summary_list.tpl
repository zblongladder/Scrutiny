<h1 align = "center">Search Results</h1>
<?php if(count($sections) == 0) { ?>
No evaluations have been submitted for any of the specified courses.
<?php } ?>
<ul>
<?php foreach ($sections as $section) { ?>
  <li><a href = "edit_summary.php?section_id=<?php echo $section['section_id']?>" 
         title = "<?php echo make_section_title($section) ?>">
      <?php echo $section['name'] . '.  Professor ' . $section['professor'] . ', ' 
            . ($section['semester'] == 2 ? "Fall " : "Spring ") . $section['year'] ?></a>
      (<?php echo $section['num_evaluations'] ?>)</li>
<?php } ?>
</ul>
