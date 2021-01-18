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
	$messages = array();

	function checkForm($form) {
		global $errors;
		$data = array(
			COL_ACTIVITY_CLASSID => false,
			COL_ACTIVITY_TIME => false,
			COL_ACTIVITY_DURATION => false,
			COL_ACTIVITY_MATERIALS => null,
		);

		foreach (array_keys($data) as $col) {
			if ($form[$col]) {
				$data[$col] = htmlspecialchars($form[$col]);
			}
		}

		if (!$data[COL_ACTIVITY_CLASSID]) {
			$errors["classId"] = "Id nastave nije određen!";
		}
		if (!$data[COL_ACTIVITY_TIME]) {
			$errors["time"] = "Unesite termin nastavne aktivnosti!";
		}
		if (!$data[COL_ACTIVITY_DURATION]) {
			$errors["duration"] = "Unesite trajanje nastavne aktivnosti";
		}

		return $data;
	}

	if (isset($_POST["dodajButton"])) {
		$_POST[COL_ACTIVITY_TIME] = $_POST["datepart"]." ".$_POST["timepart"];
		$_POST[COL_ACTIVITY_MATERIALS] = basename($_FILES[COL_ACTIVITY_MATERIALS]["name"]);

		$data = checkForm($_POST);

		$novaAktivnost = new Aktivnost($_POST);

		// Dodavanje aktivnosti i dodavanje materijala
		if ($db->addAktivnost($novaAktivnost) && isset($_FILES[COL_ACTIVITY_MATERIALS]) && $_FILES[COL_ACTIVITY_MATERIALS]["error"] == UPLOAD_ERR_OK) {
			// U slucaju da format materijala nije dobar
			if (!in_array($_FILES[COL_ACTIVITY_MATERIALS]["type"], array("application/zip", "application/x-zip-compressed", "multipart/x-zip", "application/x-compressed", "application/octet-stream"))) {
				$messages[] = "Materijal nije odgovarajućeg tipa podatka!";
			} else {
				// Dodavanje materijala
				$tmp_name = $_FILES[COL_ACTIVITY_MATERIALS]["tmp_name"];
				$name = $data[COL_ACTIVITY_MATERIALS];

				if (move_uploaded_file($tmp_name, "materijali/$name")) {
					$predmetId = $_COOKIE["predmetId"];
					header("Location: predmet.php?id=$predmetId");
				}

				$messages[] = "Neuspešno dodavanje materijala!";
				$messages[] = $_FILES[COL_ACTIVITY_MATERIALS]["error"];
			}
		} else {
			$messages[] = "Aktivnost nije dodata!";
		}
	}

	$predmetId = false;
	if (isset($_COOKIE["predmetId"])) {
		$predmetId = $_COOKIE["predmetId"];
	} else {
		$errorMessages[] = "Id predmeta nije defininsan!";
	}

	$predmet = false;
	if ($predmetId) {
		$predmet = $db->getPredmet($predmetId);
	}

	$nastave = array();
	if ($predmetId) {
		$nastave = $db->getNastave($predmetId);
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
			if (!empty($messages)) {
				foreach ($messages as $message) {
					echo "<div style=\"color: red;\">$message</div>";
				}
			}
		?>

		<h2>Dodavanje aktivnosti</h2>
		<form action="add_aktivnost.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="<?php echo COL_ACTIVITY_CLASSID; ?>" value="<?php echo $nastavaId; ?>"/>

			<label for="nastava">Nastava:</label>
			<?php Utils::outErr($errors, "classId"); ?>
			<select name="<?php echo COL_ACTIVITY_CLASSID; ?>" id="nastava">
				<?php
					foreach ($nastave as $nastava) {
						$nastavaId = $nastava->getID();
						$nastavaNaziv = $nastava->getNaziv();
						echo "<option value=\"$nastavaId\">$nastavaNaziv</option>";
					}
				?>
			</select><br/>

			<label for="datepart">Datum održavanja:</label>
			<?php Utils::outErr($errors, "time"); ?>
			<input type="date" name="datepart" id="datepart" required/><br/>

			<label for="timepart">Vreme održavanja:</label>
			<input type="time" name="timepart" id="timepart" required/><br/>

			<label for="trajanje">Trajanje:</label>
			<?php Utils::outErr($errors, "duration"); ?>
			<input type="number" name="<?php echo COL_ACTIVITY_DURATION; ?>" id="trajanje" required/><br/>

			<label for="materijali">Materijali:</label>
			<input type="file" name="<?php echo COL_ACTIVITY_MATERIALS; ?>" id="materijali"/><br/>

			<input type="submit" name="dodajButton" value="Dodaj"/>
		</form>
	</body>
</html>
