<?php
	require_once("classes/Constants.php");
	require_once("classes/Database.php");
	session_start();
	
	$db = new Database();
	$errors = array();
	$messages = array();

	if (isset($_GET["logout"])) {
		session_destroy();
	} elseif (isset($_SESSION["user"])) {
		header("Location: arhiv.php");
	}

	if (isset($_GET["login-fail"])) {
		$messages[] = "Pogrešan username ili šifra";
	}

	if (isset($_GET["forget-me"])) {
		setcookie("username", "", time()-1000);
		header("Location: login.php");
	}

	if (isset($_POST["registerButton"])) {
		$data = array(
			COL_USER_USERNAME => false,
			COL_USER_PASSWORD => false,
			COL_USER_NAME => false,
			COL_USER_SURNAME => false,
			COL_USER_DOB => false,
			COL_USER_INDEX => false
		);

		if ($_POST[COL_USER_USERNAME]) {
			$data[COL_USER_USERNAME] = htmlspecialchars($_POST[COL_USER_USERNAME]);
		}
		if ($_POST[COL_USER_PASSWORD]) {
			$data[COL_USER_PASSWORD] = htmlspecialchars($_POST[COL_USER_PASSWORD]);
		}
		if ($_POST[COL_USER_NAME]) {
			$data[COL_USER_NAME] = htmlspecialchars($_POST[COL_USER_NAME]);
		}
		if ($_POST[COL_USER_SURNAME]) {
			$data[COL_USER_SURNAME] = htmlspecialchars($_POST[COL_USER_SURNAME]);
		}
		if ($_POST[COL_USER_DOB]) {
			$data[COL_USER_DOB] = htmlspecialchars($_POST[COL_USER_DOB]);
		}
		if ($_POST[COL_USER_INDEX]) {
			$data[COL_USER_INDEX] = htmlspecialchars($_POST[COL_USER_INDEX]);
		}

		if (!$data[COL_USER_USERNAME]) {
			$errors["username"] = "Unesite korisničko ime!";
		}
		if (!$data[COL_USER_PASSWORD]) {
			$errors["password"] = "Unesite lozinku!";
		}
		if (!$data[COL_USER_NAME]) {
			$errors["name"] = "Unesite ime!";
		}
		if (!$data[COL_USER_SURNAME]) {
			$errors["surname"] = "Unesite prezime!";
		}
		if (!$data[COL_USER_DOB]) {
			$errors["dob"] = "Unesite datum rođenja!";
		}
		if (!$data[COL_USER_INDEX]) {
			$errors["index"] = "Unesite broj indeksa!";
		}

		if (empty($errors)) {
			$success = $db->addUser($data);
			$messages[] = $success ? "Uspešno ste se registrovali!" : "Registracija nije je bila uspešna!";
		}
	}

	function outErr($err) {
		global $errors;
		if (isset($errors[$err])) {
			echo '<div style="color: red;">'.$errors[$err].'</div>';
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Login to Archive</title>
	</head>
	<body>
		<section class="messages-section">
			<?php
				if (!empty($messages)) {
					echo '<div>';
					foreach ($messages as $message) {
						echo "<div>$message</div>";
					}
					echo '</div><br/>';
				}
			?>
		</section>
		<section class="login-section">
			<h3>Login</h3>
			<form method="post" action="arhiv.php">
				<label for="username">Username</label>
				<input type="text" name="username" value="<?php echo isset($_COOKIE["username"]) ? $_COOKIE["username"] : ""; ?>"/><br/>

				<label for="password">Password</label>
				<input type="password" name="password"/><br/>

				<input type="checkbox" name="remember-me" checked/> Zapamti moj username<br/>
				<a href="?forget-me">Forget me</a>

				<input type="submit" name="loginButton" value="Uloguj se"/>
			</form>
		</section>
		<section class="register-section">
			<h3>Register</h3>
			<form action="login.php" method="post">
				<label for="username">Username:</label>
				<?php outErr("username"); ?>
				<input type="text" name="<?php echo COL_USER_USERNAME; ?>" id="username" required/><br/>

				<label for="password">Password:</label>
				<?php outErr("password"); ?>
				<input type="password" name="<?php echo COL_USER_PASSWORD; ?>" id="password" required/><br/>

				<label for="ime">Ime:</label>
				<?php outErr("name"); ?>
				<input type="text" name="<?php echo COL_USER_NAME; ?>" id="ime" required/><br/>

				<label for="prezime">Prezime:</label>
				<?php outErr("surname"); ?>
				<input type="text" name="<?php echo COL_USER_SURNAME; ?>" id="ime" required/><br/>

				<label for="datumrodj">Datum rođenja:</label>
				<?php outErr("dob"); ?>
				<input type="date" name="<?php echo COL_USER_DOB; ?>" id="datumrodj" required/><br/>

				<label for="brIndeksa">Broj indeksa:</label>
				<?php outErr("index"); ?>
				<input type="text" name="<?php echo COL_USER_INDEX; ?>" id="brIndeksa" required/><br/>

				<input type="submit" name="registerButton" value="Registruj se">
			</form>
		</section>
	</body>
</html>
