<?php
	require_once("classes/Constants.php");
	require_once("classes/Predmet.php");
	require_once("classes/Nastava.php");
	require_once("classes/Aktivnost.php");

	class Database {
		private $hashing_salt = "vcyeqbecgwoc7og279e7w9cg8";
		private $conn;

		public function __construct($config_file = "db_config.ini") {
			if ($config = parse_ini_file($config_file)) {
				$host = isset($config["host"]) ? $config["host"] : "";
				$database = isset($config["database"]) ? $config["database"] : "";
				$user = isset($config["user"]) ? $config["user"] : "";
				$password = isset($config["password"]) ? $config["password"] : "";
				try {
					$this->conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
					return true;
				} catch (PDOException $e) {
					echo $e->getMessage();
					$this->conn = null;
				}
			}
			return false;
		}

		public function addPredmet(Predmet $predmet) {
			if (!$this->conn) return false;
			try {
				$sql = "INSERT INTO ".TBL_SUBJECT." (".COL_SUBJECT_USERID.", ".COL_SUBJECT_NAME.", ".COL_SUBJECT_CODE.", ".COL_SUBJECT_DESC.", ".COL_SUBJECT_PROF.", ".COL_SUBJECT_TEACHASSIS.") VALUES (:userId, :name, :code, :desc, :prof, :teachassis);";
				print_r($sql);
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("userId", $predmet->getUserID());
				$stmt->bindValue("name", $predmet->getIme());
				$stmt->bindValue("code", $predmet->getSifra());
				$stmt->bindValue("desc", $predmet->getOpis());
				$stmt->bindValue("prof", $predmet->getProfesor());
				$stmt->bindValue("teachassis", $predmet->getAsistentiString());
				return $stmt->execute();
			} catch (PDOException $e) {
				return false;
			}
		}

		public function getPredmets($userId) {
			$predmets = array();
			if (!$this->conn) return $predmets;
			try {
				$sql = "SELECT * FROM ".TBL_SUBJECT." WHERE ".COL_SUBJECT_USERID." = :userId;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("userId", $userId);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					$predmets[] = new Predmet($row);
				}
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
			finally {
				return $predmets;
			}
		}

		public function getPredmet($id) {
			if (!$this->conn) return null;
			try {
				$sql = "SELECT * FROM ".TBL_SUBJECT." WHERE ".COL_SUBJECT_ID." = :id;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("id", $id);
				$stmt->execute();
				if ($row = $stmt->fetch()) {
					return new Predmet($row);
				}
			} catch (PDOException $e) {
				return null;
			}
		}

		public function addNastava(Nastava $nastava) {
			if (!$this->conn) return false;
			try {
				$sql = "INSERT INTO ".TBL_CLASS." (".COL_CLASS_SUBJECTID.", ".COL_CLASS_NAME.", ".COL_CLASS_TEACHER.", ".COL_CLASS_LINK.") VALUES (:subjectId, :name, :teacher, :link);";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("subjectId", $nastava->getPredmetID());
				$stmt->bindValue("name", $nastava->getNaziv());
				$stmt->bindValue("teacher", $nastava->getPredavac());
				$stmt->bindValue("link", $nastava->getLink());
				if (!$stmt->execute()) {
					return $stmt->errorInfo();
				}
				return $stmt->errorInfo();
			} catch (PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}

		public function getNastave($predmetId) {
			$nastavas = array();
			if (!$this->conn) return $nastavas;
			try {
				$sql = "SELECT * FROM ".TBL_CLASS." WHERE ".COL_CLASS_SUBJECTID." = :predmetId;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("predmetId", $predmetId);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					$nastavas[] = new Nastava($row);
				}
			} catch (PDOException $e) {}
			finally {
				return $nastavas;
			}
		}

		public function getNastava($id) {
			if (!$this->conn) return null;
			try {
				$sql = "SELECT * FROM ".TBL_CLASS." WHERE ".COL_CLASS_ID." = :id;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("id", $id);
				$stmt->execute();
				if ($row = $stmt->fetch()) {
					return new Nastava($row);
				}
			} catch (PDOException $e) {
				return null;
			}
		}

		public function addAktivnost(Aktivnost $aktivnost) {
			if (!$this->conn) return false;
			try {
				$sql = "INSERT INTO ".TBL_ACTIVITY." (".COL_ACTIVITY_CLASSID.", ".COL_ACTIVITY_TIME.", ".COL_ACTIVITY_DURATION.", ".COL_ACTIVITY_MATERIALS.") VALUES (:classId, :time, :duration, :materials);";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("classId", $aktivnost->getNastavaID());
				$stmt->bindValue("time", $aktivnost->getTermin());
				$stmt->bindValue("duration", $aktivnost->getTrajanje());
				$stmt->bindValue("materials", $aktivnost->getMaterijali());
				return $stmt->execute();
			} catch (PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}

		public function getAktivnosti($nastavaId) {
			$aktivnosti = array();
			if (!$this->conn) return $aktivnosti;
			try {
				$sql = "SELECT * FROM ".TBL_ACTIVITY." WHERE ".COL_ACTIVITY_CLASSID." = :nastavaId;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("nastavaId", $nastavaId);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					$aktivnosti[] = new Aktivnost($row);
				}
			} catch (PDOException $e) {}
			finally {
				return $aktivnosti;
			}
		}

		public function getAktivnost($id) {
			if (!$this->conn) return null;
			try {
				$sql = "SELECT * FROM ".TBL_ACTIVITY." WHERE ".COL_ACTIVITY_ID." = :id;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("id", $id);
				$stmt->execute();
				if ($row = $stmt->fetch()) {
					return new Aktivnost($row);
				}
			} catch (PDOException $e) {
				return null;
			}
		}

		public function addUser($data) {
			if (!$this->conn) return false;
			try {
				$hashed_password = crypt($data[COL_USER_PASSWORD], $this->hashing_salt);
				$sql = "INSERT INTO ".TBL_USER." (".COL_USER_USERNAME.", ".COL_USER_PASSWORD.", ".COL_USER_NAME.", ".COL_USER_SURNAME.", ".COL_USER_DOB.", ".COL_USER_INDEX.") VALUES (:username, :password, :name, :surname, :dob, :index);";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("username", $data[COL_USER_USERNAME]);
				$stmt->bindValue("password", $hashed_password);
				$stmt->bindValue("name", $data[COL_USER_NAME]);
				$stmt->bindValue("surname", $data[COL_USER_SURNAME]);
				$stmt->bindValue("dob", $data[COL_USER_DOB]);
				$stmt->bindValue("index", $data[COL_USER_INDEX]);
				return $stmt->execute();
			} catch (PDOException $e) {
				return false;
			}
		}

		public function checkLogin($user, $pass) {
			if (!$this->conn) return null;
			try {
				$hashed_password = crypt($pass, $this->hashing_salt);
				$sql = "SELECT * FROM ".TBL_USER." WHERE ".COL_USER_USERNAME." = :user AND ".COL_USER_PASSWORD." = :pass;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("user", $user, PDO::PARAM_STR);
				$stmt->bindValue("pass", $hashed_password);
				// $stmt->bindValue("pass", $pass, PDO::PARAM_STR);
				$stmt->execute();
				return $stmt->fetch();
			} catch (PDOException $e) {
				return null;
			}
		}

		public function delMaterijalFromTable($id) {
			if (!$this->conn) return false;
			try {
				$sql = "UPDATE ".TBL_ACTIVITY." SET ".COL_ACTIVITY_MATERIALS." = null WHERE ".COL_ACTIVITY_ID." = :id;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("id", $id);
				return $stmt->execute();
			} catch (PDOException $e) {
				return false;
			}
		}

		public function delMaterijalFromStorage($name) {
			unlink("materijali/".$name);
		}

		public function addMaterijalToTable($id, $name) {
			if (!$this->conn) return false;
			try {
				$sql = "UPDATE ".TBL_ACTIVITY." SET ".COL_ACTIVITY_MATERIALS." = :name WHERE ".COL_ACTIVITY_ID." = :id;";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindValue("id", $id);
				$stmt->bindValue("name", $name);
				return $stmt->execute();
			} catch (PDOException $e) {
				return false;
			}
		}
	}
?>
