<?php 
class App_Helpers_Formatting {

	public static function sanitize_file_name( $filename ) {
		$filename_raw = $filename;
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));

		$filename = str_replace($special_chars, '', $filename);
		$filename = preg_replace('/[\s-]+/', '-', $filename);
		$filename = trim($filename, '.-_');

		$parts = explode('.', $filename);
		$filename = array_shift($parts);
		$extension = array_pop($parts);

		foreach ( (array) $parts as $part) {
			$filename .= '.' . $part;
		}
		
		$filename .= '.' . $extension;

		return $filename;
	}
}
?>