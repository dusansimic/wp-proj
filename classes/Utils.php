<?php
	class Utils {
		public static function array_rekey($array, $function) {
			$new = array();
			foreach ($array as $k => $v) {
				$new[$function($k, $v)] = $v;
			}
			return $new;
		}

		public static function outErr($errors, $err) {
			if (isset($errors[$err])) {
				echo '<div style="color: red;">'.$errors[$err].'</div>';
			}
		}
	}
?>
