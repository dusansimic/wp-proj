<?php
	require_once("classes/Constants.php");

	class Nastava {
		private $id;
		private $predmetId;
		private $naziv;
		private $predavac;
		private $link;

		public function __construct($data) {
			$this->id = $data[COL_CLASS_ID];
			$this->predmetId = $data[COL_CLASS_SUBJECTID];
			$this->naziv = $data[COL_CLASS_NAME];
			$this->predavac = $data[COL_CLASS_TEACHER];
			$this->link = $data[COL_CLASS_LINK];
		}

		public function getID() {
			return $this->id;
		}

		public function getPredmetID() {
			return $this->predmetId;
		}

		public function getNaziv() {
			return $this->naziv;
		}

		public function getPredavac() {
			return $this->predavac;
		}

		public function getLink() {
			return $this->link;
		}

		public function getHTML() {
			return "<div>
			<h3>$this->naziv</h3>
			<table>
				<tr><td>Predavač:</td><td>$this->predavac</td></tr>
				<tr><td>Link:</td><td><a href=\"$this->link\">$this->link</a></td></tr>
			</table>
			</div><hr/>";
		}
	}
?>
