<?php
	require_once("classes/Constants.php");

	class Predmet {
		private $id;
		private $userId;
		private $ime;
		private $sifra;
		private $opis;
		private $profesor;
		private $asistenti;

		public function __construct($data) {
			$this->id = $data[COL_SUBJECT_ID];
			$this->userId = $data[COL_SUBJECT_USERID];
			$this->ime = $data[COL_SUBJECT_NAME];
			$this->sifra = $data[COL_SUBJECT_CODE];
			$this->opis = $data[COL_SUBJECT_DESC];
			$this->profesor = $data[COL_SUBJECT_PROF];
			$this->asistenti = explode(';', $data[COL_SUBJECT_TEACHASSIS]);
		}

		public function getID() {
			return $this->id;
		}

		public function getUserID() {
			return $this->userId;
		}

		public function getIme() {
			return $this->ime;
		}

		public function getSifra() {
			return $this->sifra;
		}

		public function getOpis() {
			return $this->opis;
		}

		public function getProfesor() {
			return $this->profesor;
		}

		public function getAsistenti() {
			return $this->asistenti;
		}

		public function getAsistentiString() {
			return implode(";", $this->asistenti);
		}

		public function getHTML() {
			$asistenti = implode(", ", $this->asistenti);
			return "<div>
			<h3><a href=\"predmet.php?id=$this->id\">$this->ime</a></h3>
			<table>
				<tr><td>Šifra:</td><td>$this->sifra</td></tr>
				<tr><td>Profesor:</td><td>$this->profesor</td></tr>
				<tr><td>Asistenti:</td><td>$asistenti</td></tr>
				<tr><td>Opis:</td><td>$this->opis</td></tr>
			</table>
			</div><hr/>";
		}
	}
?>
