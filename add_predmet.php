<?php
	require_once("classes/Constants.php");
	require_once("classes/Database.php");
	require_once("classes/Utils.php");
	session_start();
	$db = new Database();

	if (!isset($_SESSION["user"])) {
		header("Location: login.php");
	}

	$main_user = false;
	if (!$main_user) {
		$main_user = $_SESSION["user"];
	}

	$errors = array();

	function checkForm($form) {
		global $errors;
		$data = array(
			COL_SUBJECT_USERID => false,
			COL_SUBJECT_NAME => false,
			COL_SUBJECT_CODE => false,
			COL_SUBJECT_DESC => null,
			COL_SUBJECT_PROF => false,
			COL_SUBJECT_TEACHASSIS => false,
		);

		foreach (array_keys($data) as $col) {
			if ($form[$col]) {
				$data[$col] = htmlspecialchars($form[$col]);
			}
		}

		if (!$data[COL_SUBJECT_USERID]) {
			$errors["userId"] = "Id korisnika nije definisan!";
		}
		if (!$data[COL_SUBJECT_NAME]) {
			$errors["name"] = "Unesite ime predmeta!";
		}
		if (!$data[COL_SUBJECT_CODE]) {
			$errors["code"] = "Unesite šifru predmeta!";
		}
		if (!$data[COL_SUBJECT_PROF]) {
			$errors["prof"] = "Unesite ime profesora";
		}

		return $data;
	}

	if (isset($_POST["dodajButton"])) {
		$data = checkForm($_POST);
		$noviPredmet = new Predmet($data);
		if ($db->addPredmet($noviPredmet)) {
			header("Location: arhiv.php");
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
	</head>
	<body>
		<?php
			if (!empty($errors)) {
				foreach ($errors as $error) {
					echo "<div style=\"color: red;\">$error</div>";
				}
			}
		?>

		<h2>Dodavanje predmeta</h2>
		<form action="add_predmet.php" method="post">
			<input type="hidden" name="<?php echo COL_SUBJECT_USERID; ?>" value="<?php echo $main_user[COL_USER_ID]; ?>"/>

			<label for="ime">Ime:</label>
			<?php Utils::outErr($errors, "name"); ?>
			<input type="text" name="<?php echo COL_SUBJECT_NAME; ?>" id="ime" required/><br/>

			<label for="sifra">Šifra:</label>
			<?php Utils::outErr($errors, "code"); ?>
			<input type="text" name="<?php echo COL_SUBJECT_CODE; ?>" id="sifra" required/><br/>

			<label for="opis">Opis:</label>
			<textarea name="<?php echo COL_SUBJECT_DESC; ?>" id="opis" cols="50" rows="4"></textarea><br/>

			<label for="profesor">Profesor:</label>
			<?php Utils::outErr($errors, "prof"); ?>
			<input type="text" name="<?php echo COL_SUBJECT_PROF; ?>" id="profesor" required/><br/>

			<p>Asistente odvajati sa znakom <b>;<b></p>
			<label for="asistenti">Asistenti:</label>
			<input type="text" name="<?php echo COL_SUBJECT_TEACHASSIS; ?>" id="asistenti" required/><br/>

			<input type="submit" name="dodajButton" value="Dodaj"/>
		</form>
	</body>
</html>
