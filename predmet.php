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

	$errorMessages = array();

	$predmetId = null;
	if (isset($_GET["id"])) {
		$predmetId = $_GET["id"];
		setcookie("predmetId", $predmetId, time()+60*60);
	} else {
		$errorMessages[] = "Id predmeta nije određen";
	}

	$predmet = null;
	if (isset($predmetId)) {
		$predmet = $db->getPredmet($predmetId);
	}
	$imePredmeta = isset($predmet) ? $predmet->getIme() : "Predmet";

	// Prikupljanje svih nastavnih aktivnosti.
	$nastave = null;
	if (empty($errorMessages)) {
		$nastave = $db->getNastave($predmetId);
	}

	// Napraviti novi niz tipova nastavnih aktivnosti da im se moze pristupati preko njihovog
	// ID-ja.
	$nastaveById = Utils::array_rekey($nastave, function($k, $v) {
		return $v->getID();
	});

	// Preuzimanje svih nastavnih aktivnosti od postojecih tipova i skladistenje u jedan niz.
	$nastavneAktivnosti = array();
	if (empty($errorMessage)) {
		foreach ($nastave as $nastava) {
			$aktivnosti = $db->getAktivnosti($nastava->getID());
			$nastavneAktivnosti = array_merge($nastavneAktivnosti, $aktivnosti);
		}
	}

	// Sortiranje po rastucem poretku termina pa obrnuti da bude opadajuce.
	usort($nastavneAktivnosti, "Aktivnost::compareByTermin");
	$nastavneAktivnosti = array_reverse($nastavneAktivnosti);

	$filtriraneAktivnosti = $nastavneAktivnosti;
	if (isset($_GET["filterButton"]) && $_GET["filterButton"] == "Primeni filter" && isset($_GET["tipNastave"])) {
		if (isset($_GET["tipNastave"])) {
			$filtriraneAktivnosti = array_filter($nastavneAktivnosti, function($akt) {
				$id = $akt->getNastavaID();
				return $_GET["tipNastave"] == $id || $_GET["tipNastave"] == "sve";
			});
		}
		if (isset($_GET["mindt"]) && !empty($_GET["mindt"])) {
			$filtriraneAktivnosti = array_filter($filtriraneAktivnosti, function($akt) {
				return $akt->getTermin() > $_GET["mindt"];
			});
		}
		if (isset($_GET["maxdt"]) && !empty($_GET["maxdt"])) {
			$filtriraneAktivnosti = array_filter($filtriraneAktivnosti, function($akt) {
				return $akt->getTermin() < $_GET["maxdt"];
			});
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $imePredmeta; ?></title>
	</head>
	<body>
		<!--
			Ukoliko ima gresaka, prikazati samo greske.
			Ukoliko nema gresaka, priakzati trazeni predmet.
		-->
		<?php
			if (!empty($errorMessages)) {
				foreach ($errorMessages as $errorMessage) {
					echo $errorMessage;
				}
			} else {
		?>

		<a href="arhiv.php">Arhiv</a>
		<h2>Nastava</h2>
		<a href="add_nastava.php">Dodaj nastavu</a>
		<!-- Prikaz svih tipova nastavnih aktivnosti -->
		<?php
			foreach ($nastave as $nastava) {
				echo $nastava->getHTML();
			}
		?>

		<h2>Aktivnosti</h2>
		<a href="add_aktivnost.php">Dodaj aktivnost</a>
		<div>
			<h3>Filtriranje</h3>
			<form method="get" action="predmet.php">
				<input type="hidden" name="id" value="<?php echo $predmetId; ?>"/>
				<p>Format datuma mora da bude: YYYY-MM-DD_HH:MM:SS gde je _ razmak.</p>
				<label for="minDT">Od:</label>
				<input type="datetime-local" name="mindt" id="minDT"/><br/>
				<label for="maxDT">Do:</label>
				<input type="datetime-local" name="maxdt" id="maxDT"/><br/>
				<label for="tipNastave">Tip nastavne aktivnosti:</label>
				<select name="tipNastave" id="tipNastave">
					<!--
						Opcija za sve tipove nastavnih aktivnosti.
						Ovo postoji tu samo da bi korisnik mogao da odabere sve nastavne aktivnosti i bez
						brisanja i filtera i time moze da prikaze sve nastavne aktivnosti u datom vremenskom
						intervalu.
					-->
					<?php
						$attr = '';
						if (isset($_GET["tipNastasve"]) && $_GET["tipNastave"] == $id) {
							$attr = ' selected="selected"';
						}
						echo "<option value=\"sve\" $attr>Sve</option>";
					?>

					<!-- Svi ostali tipovi nastavnih aktivnosti -->
					<?php
						foreach ($nastaveById as $id => $nastava) {
							$ime = $nastava->getNaziv();
							$attr = '';
							if (isset($_GET["tipNastave"]) && $_GET["tipNastave"] == $id) {
								$attr = ' selected="selected"';
							}
							echo "<option value=\"$id\" $attr>$ime</option>";
						}
					?>
				</select><br/>
				<!-- Dugmad za primenjivanje ili uklanjanje filtera -->
				<input type="submit" name="filterButton" value="Primeni filter"/>
				<input type="submit" name="filterButton" value="Ukloni filter"/>
			</form>
		</div>
		<table>
		<!-- Prikaz svih isfiltriranih nastavnih aktivnosti -->
		<?php
			foreach ($filtriraneAktivnosti as $aktivnost) {
				$imeNastave = $nastaveById[$aktivnost->getNastavaID()]->getNaziv();
				echo $aktivnost->getHTML($imeNastave);
			}
		?>
		</table>

		<?php
			}
		?>
	</body>
</html>
