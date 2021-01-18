<?php
	require_once("classes/Constants.php");
	require_once("classes/Database.php");
	require_once("classes/Aktivnost.php");
	session_start();
	$db = new Database();

	if (!isset($_SESSION["user"])) {
		header("Location: login.php");
	}

	$main_user = false;
	if (!$main_user) {
		$main_user = $_SESSION["user"];
	}

	$errorMessages = array();

	$aktivnost = null;
	if (isset($_GET["id"])) {
		$aktivnost = $db->getAktivnost($_GET["id"]);
	} else {
		$errorMessages[] = "Id nije postavljen!";
	}

	$tipNastave = null;
	if (isset($aktivnost)) {
		$tipNastave = $db->getNastava($aktivnost->getNastavaID());
	} else {
		$errorMessages[] = "Nije pronadjena aktivnost!";
	}

	$predmet = null;
	if (isset($tipNastave)) {
		$predmet = $db->getPredmet($tipNastave->getPredmetID());
	} else {
		$errorMessages[] = "Nije pronadjen tip nastave!";
	}

	if (!isset($predmet)) {
		$errorMessages[] = "Nije pronadjen predmet!";
	}

	$naslov = $tipNastave->getNaziv()." - ".$predmet->getIme();
	$termin = (DateTime::createFromFormat('Y-m-d H:i:s', $aktivnost->getTermin()))->format('d. m. Y. | H:i');
	$materijali = $aktivnost->getMaterijali();
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $naslov; ?></title>
	</head>
	<body>
		<?php
			if (!empty($errorMessages)) {
		?>

		<h2>Greška</h2>
		<?php
			foreach ($errorMessages as $errorMessage) {
				echo "<p>$errorMessage</p>";
			}
		?>

		<?php
			} else {
		?>

		<?php
			function printMaterijal() {
				global $materijali;
		?>
			<p> Materijali: <a href="materijali/<?php echo $materijali; ?>"><?php echo $materijali; ?></a></p>
		<?php
			}

			function printDodajMaterijal() {
				global $aktivnost;
				$aktivnostId = $aktivnost->getID();
		?>
			<form action="add_materijal.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="id" value="<?php echo $aktivnostId; ?>"/>
				<label for="file">Materjiali:</label>
				<input type="file" name="file" id="file"/>
				<input type="submit" name="dodajButton" value="Dodaj"/>
			</form>
		<?php
			}
		?>

		<h2><?php echo $naslov; ?></h2>
		<h3><?php echo $termin; ?></h3>
		<p>Trajanje: <?php echo $aktivnost->getTrajanje()." min"; ?></p>
		<?php $aktivnost->hasMaterijal() ? printMaterijal() : printDodajMaterijal(); ?>

		<?php if ($aktivnost->hasMaterijal()) { ?>
		<div>
			<form action="del_materijal.php" method="post">
				<input type="hidden" name="id" value="<?php echo $aktivnost->getID(); ?>"/>
				<input type="submit" name="deleteButton" value="Ukloni materijal"/>
			</form>
		</div>
		<?php } ?>

		<?php
			}
		?>
	</body>
</html>
