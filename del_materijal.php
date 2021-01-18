<?php
	require_once("classes/Database.php");
	session_start();
	$db = new Database();

	if (!isset($_SESSION["user"])) {
		header("Location: login.php");
	}

	if (isset($_POST["deleteButton"])) {
		$id = htmlspecialchars($_POST["id"]);
		$aktivnost = $db->getAktivnost($id);
		$ime = $aktivnost->getMaterijali();

		$db->delMaterijalFromTable($id) && $db->delMaterijalFromStorage($ime);

		header("Location: aktivnost.php?id=$id");
	} else {
		echo '<div style="color: red;">Forma nije potpuna!</div>';
	}
?>
