<?php
	require_once("classes/Constants.php");

	class Aktivnost {
		private $id;
		private $nastavaId;
		private $termin;
		private $trajanje;
		private $materijali;

		public function __construct($data) {
			$this->id = $data[COL_ACTIVITY_ID];
			$this->nastavaId = $data[COL_ACTIVITY_CLASSID];
			$this->termin = $data[COL_ACTIVITY_TIME];
			$this->trajanje = $data[COL_ACTIVITY_DURATION];
			$this->materijali = $data[COL_ACTIVITY_MATERIALS];
		}

		public function getID() {
			return $this->id;
		}

		public function getNastavaID() {
			return $this->nastavaId;
		}

		public function getTermin() {
			return $this->termin;
		}

		public function getTrajanje() {
			return $this->trajanje;
		}

		public function getMaterijali() {
			return $this->materijali;
		}

		public function hasMaterijal() {
			return isset($this->materijali);
		}

		public function getHTML($imeNastave) {
			$termin = (DateTime::createFromFormat('Y-m-d H:i:s', $this->termin))->format('d. m. Y. | H:i');

			return  "<tr><td><a href=\"aktivnost.php?id=$this->id\">$termin</a></td><td>Tip:</td><td><b>$imeNastave</b></td></tr><tr><td/><td>Materijali:</td><td>$this->materijali</td></tr><tr><td/><td>Trajanje:</td><td>$this->trajanje min</td></tr>";
		}

		public static function compareByTermin($a, $b) {
			return $a->termin <=> $b->termin;
		}
	}
?>
