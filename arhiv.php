<?php
	require_once("classes/Constants.php");
	require_once("classes/Database.php");
	session_start();
	$db = new Database();

	$main_user = false;
	if (isset($_POST["loginButton"])) {
		$main_user = $db->checkLogin($_POST["username"], $_POST["password"]);
		if (!$main_user) {
			header("Location: login.php?login-fail");
		} else {
			$_SESSION["user"] = $main_user;
			if ($_POST["remember-me"]) {
				setcookie("username", $main_user[COL_USER_USERNAME], time()+60*60*24*365);
			}
			header("Location: arhiv.php");
		}
	}

	$errorMessage = "";

	if (!isset($_SESSION["user"])) {
		header("Location: login.php");
	}

	if (!$main_user) {
		$main_user = $_SESSION["user"];
	}

	$predmeti = $db->getPredmets($main_user[COL_USER_ID]);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Arhiv</title>
	</head>
	<body>
		<h3>Korisnik: <?php echo $main_user[COL_USER_NAME]." ".$main_user[COL_USER_SURNAME]; ?></h3>
		<p><a href="login.php?logout">Logout</a></p>
		<p><a href="add_predmet.php">Dodaj predmet</a></p>
		<?php
			foreach ($predmeti as $predmet) {
				echo $predmet->getHTML();
			}
		?>
	</body>
</html>