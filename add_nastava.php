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
			COL_CLASS_SUBJECTID => false,
			COL_CLASS_NAME => false,
			COL_CLASS_TEACHER => false,
			COL_CLASS_LINK => false,
		);

		foreach (array_keys($data) as $col) {
			if ($form[$col]) {
				$data[$col] = htmlspecialchars($form[$col]);
			}
		}

		if (!$data[COL_CLASS_SUBJECTID]) {
			$errors["subjectId"] = "Id predmeta nije definisan!";
		}
		if (!$data[COL_CLASS_NAME]) {
			$errors["name"] = "Unesite ime nastavne aktivnosti!";
		}
		if (!$data[COL_CLASS_TEACHER]) {
			$errors["teacher"] = "Unesite ime predavača!";
		}
		if (!$data[COL_CLASS_LINK]) {
			$errors["link"] = "Unesite link za nastavnu aktivnost!";
		}

		return $data;
	}

	if (isset($_POST["dodajButton"])) {
		$data = checkForm($_POST);
		$novaNastava = new Nastava($data);
		if ($db->addNastava($novaNastava)) {
			$predmetId = $data[COL_CLASS_SUBJECTID];
			header("Location: predmet.php?id=$predmetId");
		} else {
			$messages[] = "Nastavna aktivnost nije dodata!";
		}
	}

	$predmetId = false;
	if (isset($_COOKIE["predmetId"])) {
		$predmetId = $_COOKIE["predmetId"];
	} else {
		header("Location: arhiv.php");
	}

	$predmet = false;
	if ($predmetId) {
		$predmet = $db->getPredmet($predmetId);
	}

	$predavaci = false;
	if ($predmet) {
		$predavaci = $predmet->getAsistenti();
		array_unshift($predavaci, $predmet->getProfesor());
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
					echo "<div>$message</div>";
				}
			}
		?>

		<h2>Dodavanje nastave</h2>
		<form action="add_nastava.php" method="post">
			<input type="hidden" name="<?php echo COL_CLASS_SUBJECTID; ?>" value="<?php echo $predmetId; ?>"/>

			<label for="ime">Ime:</label>
			<?php Utils::outErr($errors, "name"); ?>
			<input type="text" name="<?php echo COL_CLASS_NAME; ?>" id="ime" required/><br/>

			<label for="predavac">Predavač:</label>
			<?php utils::outerr($errors, "teacher"); ?>
			<select name="<?php echo COL_CLASS_TEACHER; ?>" id="predavac">
				<?php
					foreach ($predavaci as $predavac) {
						echo "<option value=\"$predavac\">$predavac</option>";
					}
				?>
			</select><br/>

			<label for="link">Link:</label>
			<?php utils::outerr($errors, "link"); ?>
			<input type="text" name="<?php echo COL_CLASS_LINK; ?>" id="link" required/><br/>

			<input type="submit" name="dodajButton" value="Dodaj"/>
		</form>
	</body>
</html>

