<?php get_header(); ?>
<?php
if(isset($_POST['step'])) {
	if($_POST['step'] == 'first') {
		$where = $_POST['where'];
		$duration = $_POST['duration'];
		$count = $_POST['count'];

		include( locate_template( 'content-questions.php', false, false ) );
	} else if($_POST['step'] == 'second') {
		$where = $_POST['where'];
		$duration = $_POST['duration'];
		$count = $_POST['count'];

		$questioncount = wp_count_posts('questions')->publish;
		$answers = array();
		for($i = 1; $i <= $questioncount; $i++) {
			$answers[$i] = $_POST['question'.$i];
			$asks[$i] = $_POST['ask'.$i];
		}

		include( locate_template( 'content-finalform.php', false, false ) );
	} else if($_POST['step'] == 'third') {
		$where = $_POST['where'];
		$duration = $_POST['duration'];
		$count = $_POST['count'];

		$questioncount = wp_count_posts('questions')->publish;
		$answers = array();
		for($i = 1; $i <= $questioncount; $i++) {
			$answers[$i] = $_POST['question'.$i];
			$asks[$i] = $_POST['ask'.$i];
		}

		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$useremail = $_POST['useremail'];
		$usertelefon = $_POST['usertelefon'];

		include( locate_template( 'content-success.php', false, false ) );
	} else get_template_part( 'content', get_post_format() );
} else get_template_part( 'content', get_post_format() );
?>
<?php get_footer(); ?>