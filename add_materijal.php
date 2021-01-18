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

	if (isset($_POST["dodajButton"]) && isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
		$id = htmlspecialchars($_POST["id"]);
		$name = htmlspecialchars(basename($_FILES["file"]["name"]));
		$file = $_FILES["file"];

		if (in_array($file["type"], array("application/zip", "application/x-zip-compressed", "multipart/x-zip", "application/x-compressed", "application/octet-stream"))) {
			if ($db->addMaterijalToTable($id, $name)) {
				$tmp_name = $file["tmp_name"];
				if (!move_uploaded_file($tmp_name, "materijali/$name")) {
					$db->delMaterijalFromTable($id);
				}
			}
		}

		header("Location: aktivnost.php?id=$id");
	}
?>
