<?php //die('DO NOT USE THIS ON A LIVE PUBLIC SERVER!');

/**
 * This file is part of the ProBMS package.
 *
 * PHP version 5
 *
 * LICENSE: This is PRIVATE source code developed for ProBMS.com.
 * It is in no way transferable and copy rights belong to Jeff Behnke @ Valid-Webs.com
 *
 * Created 10/22/11, 5:12 PM
 *
 * @category   ProBMS
 * @package
 * @subpackage
 * @author     Jeff Behnke <code@valid-webs.com>
 * @copyright  (c) 2011 Valid-Webs.com
 * @license    Premium
 * @version    0.0.1
 */

// Path to store the files
$files_dir = __DIR__ . '/files/';

// For MarkDown extension should be .md
$ext = '.md';

// Array to hold the file list
$file_list = array();

////////////////////////////////////////////////////////////////////////////////////////
// Everything after this point is for the actual process and forms
///////////////////////////////////////////////////////////////////////////////////////

$is_writeable = is_writable($files_dir);

// Make sure our files directory is writable.
if (!$is_writeable)
{
	echo '<div id="message"><p>ERROR: Your files directory is not writable.<br />Please chmod ' . $files_dir . ' to 777</p></div>';
	exit;
}

// Load the current files into an array
if ($handle = opendir($files_dir))
{
	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != ".." && $file != 'index.html')
		{
			$file_list[] = $file;
		}
	}
	closedir($handle);
}

// Get the files contents if we are loading an existing one
if (isset($_POST['get_file']) && !empty($_POST['file']))
{
	$file_name = $_POST['file'];

	$content = file_get_contents($files_dir . $file_name);

	$file = explode('.', $file_name);

	$file = $file[0];
}
elseif (isset($_POST['submit']) && !empty($_POST['file_name']))
{
	$file_name = trim($_POST['file_name']);

	$file = explode('.', $file_name);
	$file = $file[0];

	$content = trim($_POST['content']);
}
else
{
	$file    = '';
	$content = '';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ReadMe File editor creator</title>
	<link rel="stylesheet" type="text/css" href="bin/images/style.css" />
	<!-- jQuery -->
	<script type="text/javascript" src="src/jquery.min.js"></script>
	<!-- markItUp! -->
	<script type="text/javascript" src="bin/markitup/jquery.markitup.js"></script>
	<!-- markItUp! toolbar settings -->
	<script type="text/javascript" src="bin/markitup/sets/markdown/set.js"></script>
	<!-- markItUp! skin -->
	<link rel="stylesheet" type="text/css" href="bin/markitup/skins/markitup/style.css" />
	<!--  markItUp! toolbar skin -->
	<link rel="stylesheet" type="text/css" href="bin/markitup/sets/markdown/style.css" />
	<style type="text/css">
		#message {
			display:       block;
			position:      relative;
			width:         700px;
			margin:        20px;
			cursor:        pointer;
			font-weight:   bold;
			padding:       10px;
			border:        1px solid #aaa;
			background:    #ffffcc;
			border-radius: 10px;
		}

		#close {
			position:  absolute;
			color:     red;
			bottom:    0;
			right:     20px;
			font-size: 16px;
		}

		#file_list {
			display:       block;
			float:         left;
			width:         200px;
			margin:        50px 0 0 30px;
			padding:       10px;
			background:    #ddd;
			color:         #000;
			border-radius: 10px;
		}

		#file_list p {
			margin: 10px 10px 10px 30px;
		}

		#file_list p.title {
			font-weight:     bold;
			text-decoration: underline;
		}

		#form {
			display: block;
			float:   left;
			width:   800px;
		}

		label {
			font-weight: bold;
		}
	</style>

	<script type="text/javascript">
		$(document).ready(function() {

			// Add markItUp! to your textarea in one line
			$('#content').markItUp(mySettings);

			$('#message').click(function() {
				$('#message').hide('slow');
			});

		});
	</script>
</head>
<body>

<?php
if (isset($_POST['submit']) && !empty($file))
{
	echo '<div id="message">';

	try
	{
		file_put_contents('files/' . $file . $ext, $content);
		echo "<p>Your data was saved to -> $files_dir" . $file . $ext . '</p>';

		unset($content);
		unset($file);
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}

	echo '<p id="close">Close</p>
		</div>';
}
elseif (isset($_POST['submit']) && empty($file))
{
	echo '<div id="message"><p>File Name can not be empty! </p><p id="close">Close</p></div>';
}
?>

<div id="form">
	<form action="" method="post">

		<p>
			<label for="file_name">File Name:</label>
			<input type="text" class="text" id="file_name" name="file_name" value="<?php if (!empty($file))
			{
				echo $file;
			} ?>" />
		</p>

		<div>
			<label for="content">File Content</label>
			<br />
			<textarea id="content" cols="80" rows="20" name="content"><?php if (!empty($content))
			{
				echo $content;
			} ?></textarea>

			<input type="submit" name="submit" id="submit" value="Submit" />
		</div>

	</form>
</div>
<div id="file_list">
	<p class="title">Current Files</p>

	<form action="" method="post">
		<?php

		if (empty($file_list))
		{
			echo '<p>No files</p>';
		}

		foreach ($file_list as $file)
		{
			echo "<input type='radio' name='file' value='$file' /><label for='file'>$file</label><br />";
		} // end foreach
		unset($file);
		?>

		<p><input type="submit" name="get_file" id="get_file" value="Get File" /></p>

	</form>

	<form action="" method="post">
		<p><input type="submit" value="Refresh Page | Reset" /></p>
	</form>
</div>
</body>
</html>