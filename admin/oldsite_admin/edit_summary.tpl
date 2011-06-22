<h1>Edit summary for <?php echo make_section_title($section); ?></h1>
<form method = "post">
<input type = "hidden" name = "section_id" value = "<?php echo $section['section_id'] ?>" />
<a href = "/~scrutiny/all_comments.php?section_id=<?php echo $section_id ?>" target = "_NEW">View all comments</a><br />
<textarea name = "summary" rows = "20" cols = "80"><?php echo $section['summary'] ?></textarea><br />
<input type = "submit">
</form>
