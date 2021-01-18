<?php
	define("TBL_USER", "korisnik");
	define("COL_USER_ID", "id");
	define("COL_USER_USERNAME", "username");
	define("COL_USER_PASSWORD", "password");
	define("COL_USER_NAME", "ime");
	define("COL_USER_SURNAME", "prezime");
	define("COL_USER_DOB", "datrodj");
	define("COL_USER_INDEX", "brIndeksa");

	define("TBL_SUBJECT", "predmet");
	define("COL_SUBJECT_ID", "id");
	define("COL_SUBJECT_USERID", "korisnikId");
	define("COL_SUBJECT_NAME", "ime");
	define("COL_SUBJECT_CODE", "sifra");
	define("COL_SUBJECT_DESC", "opis");
	define("COL_SUBJECT_PROF", "profesor");
	define("COL_SUBJECT_TEACHASSIS", "asistenti");

	define("TBL_CLASS", "nastava");
	define("COL_CLASS_ID", "id");
	define("COL_CLASS_SUBJECTID", "predmetId");
	define("COL_CLASS_NAME", "naziv");
	define("COL_CLASS_TEACHER", "predavac");
	define("COL_CLASS_LINK", "link");

	define("TBL_ACTIVITY", "aktivnost");
	define("COL_ACTIVITY_ID", "id");
	define("COL_ACTIVITY_CLASSID", "nastavaId");
	define("COL_ACTIVITY_TIME", "termin");
	define("COL_ACTIVITY_DURATION", "trajanje");
	define("COL_ACTIVITY_MATERIALS", "materijali");
?>
