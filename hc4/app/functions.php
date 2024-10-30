<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_App_Functions
{
	public static function camelize( $in )
	{
		$return = $in;

		$pos = strpos( $return, '_' );
		while( $pos !== FALSE ){
			$replace = strtoupper( substr($return, $pos + 1, 1) );
			$return = substr_replace( $return, $replace, $pos, 2 );
			$pos = strpos( $return, '_' );
		}

		return $return;
	}

	public static function decamelize( $in )
	{
		$return = $in;

		$return = preg_replace( '/([A-Z])/', '_$1', $return );
		$return = strtolower( $return );

		return $return;
	}

	public static function glueArray( array $array )
	{
		$return = join( '_', $array );
		if( $return ){
			$return = '_' . $return . '_';
		}
		return $return;
	}

	public static function unglueArray( $string )
	{
		$return = array();
		if( is_array($string) ){
			$return = $string;
			return $return;
		}

		$string = trim( $string );
		$string = trim( $string, '_' );
		if( strlen($string) ){
			$return = explode( '_', $string );
		}

		return $return;
	}

	public static function wpGetIdByShortcode( $shortcode )
	{
		global $wpdb;
		$return = array();

		$pages = $wpdb->get_results( 
			"
			SELECT 
				ID 
			FROM $wpdb->posts 
			WHERE 
				( post_type = 'post' OR post_type = 'page' ) 
				AND 
				( post_content LIKE '%[" . $shortcode . "%]%' )
				AND 
				( post_status = 'publish' )
			"
			);

		if( $pages ){
			foreach( $pages as $p ){
				$return[] = $p->ID;
			}
		}

		return $return;
	}

	public static function removeInvisibleCharacters( $str, $url_encoded = TRUE )
	{
		$non_displayables = array();
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}

	public static function buildCsv( $array, $separator = ',' )
	{
		$processed = array();
		reset( $array );
		foreach( $array as $a ){
			if( strpos($a, '"') !== FALSE ){
				$a = str_replace( '"', '""', $a );
			}
			if( strpos($a, $separator) !== FALSE ){
				$a = '"' . $a . '"';
			}
			$processed[] = $a;
			}

		$return = join( $separator, $processed );
		return $return;
	}

	public static function randFromArray( array $array )
	{
		return $array[ array_rand($array) ];
	}

	public static function generateRand( $len = 12, $conf = array() )
	{
		$useLetters = isset($conf['letters']) ? $conf['letters'] : TRUE;
		$useHex = isset($conf['hex']) ? $conf['hex'] : FALSE;
		$useDigits = isset($conf['digits']) ? $conf['digits'] : TRUE;
		$useCaps = isset($conf['caps']) ? $conf['caps'] : FALSE;

		$salt = '';
		if( $useHex ){
			$salt .= 'abcdef';
		}
		if( $useLetters )
			$salt .= 'abcdefghijklmnopqrstuvxyz';
		if( $useDigits ){
			// $salt .= '0123456789';
			$salt .= '123456789';
		}
		if( $useCaps ){
			$salt .= 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
		}

		// srand( (double) microtime() * 1000000 );
		$return = '';
		$i = 1;
		$array = array();
		while ( $i <= $len ){
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$array[] = $tmp;
			$i++;
		}
		shuffle( $array );
		$return = join( '', $array );
		return $return;
	}

	static function removeFromArray( $array, $what, $replaceWith = NULL )
	{
		$return = $array;
		for( $ii = count($return) - 1; $ii >= 0; $ii-- ){
			if( ! array_key_exists($ii, $return) ){
				continue;
			}

			if( $return[$ii] == $what ){
				if( NULL === $replaceWith ){
					array_splice( $return, $ii, 1 );
				}
				else {
					array_splice( $return, $ii, 1, array($replaceWith) );
				}
			}
		}
		return $return;
	}

	public static function listFiles( $dir, $type = 'file', $extension = '' )
	{
		if( ! is_array($dir) )
			$dir = array( $dir );

		$return = array();
		foreach( $dir as $this_dir ){
			if ( file_exists($this_dir) && ($handle = opendir($this_dir)) ){
				while ( false !== ($f = readdir($handle)) ){
					if( substr($f, 0, 1) == '.' )
						continue;

					if( 'file' == $type ){
						if( is_file( $this_dir . '/' . $f ) ){
							if( (! $extension ) || ( substr($f, - strlen($extension)) == $extension ) ){
								$return[] = $f;
							}
						}
					}
					else {
						if( is_dir( $this_dir . '/' . $f ) ){
							if( (! $extension ) || ( substr($f, - strlen($extension)) == $extension ) ){
								$return[] = $f;
							}
						}
					}
				}
				closedir($handle);
			}
		}

		sort( $return );
		return $return;
	}

	public static function makeCombos( $array )
	{
		$return = array();

		while( $thisOnes = array_shift($array) ){
			$thisCombos = array();
			foreach( $thisOnes as $r ){
				if( $return ){
					reset( $return );
					foreach( $return as $combo ){
						$combo[] = $r;
						$thisCombos[] = $combo;
					}
				}
				else {
					$thisCombos[] = array( $r );
				}
			}
			if( $thisCombos ){
				$return = $thisCombos;
			}
		}

		return $return;
	}

	static function addToArrayAfter( $array, $afterKey, $add )
	{
		$index = FALSE;
		if( NULL !== $afterKey ){
			$keys = array_keys( $array );
			$index = array_search( $afterKey, $keys );
		}

		$pos = FALSE === $index ? count($array) : $index + 1;

		$return = array_merge( 
			array_slice( $array, 0, $pos ),
			$add,
			array_slice( $array, $pos )
			);

		return $return;
	}

	public static function addToArrayBefore( $array, $beforeKey, array $add )
	{
		$index = FALSE;
		if( NULL !== $beforeKey ){
			$keys = array_keys( $array );
			$index = array_search( $beforeKey, $keys );
		}

		$pos = FALSE === $index ? 0 : $index;

		$return = array_merge( 
			array_slice( $array, 0, $pos ),
			$add,
			array_slice( $array, $pos )
			);

		return $return;
	}

	public static function getRemoteUrl( $url2get )
	{
		$timeout = 20;
		$old = ini_set('default_socket_timeout', $timeout);

		// ob_start();

		if( intval(get_cfg_var('allow_url_fopen')) && function_exists('file') ){
			$file = file( $url2get );
			$return = implode('', $file);
		}
		elseif(function_exists('curl_init')){
			$curl = curl_init( $url2get );

			curl_setopt($curl, CURLOPT_URL, $url2get);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_HEADER, FALSE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

			$return = curl_exec($curl);

// curl_setopt( $ch, CURLOPT_HEADER, 0 );
// curl_exec( $ch );
			if( curl_error($curl)){
				echo "can't get it: $url2get<br>";
			}
			else {
// echo "curl ok";
// echo "RET = '$return'<br>";
			}
			curl_close($curl);
		}
		else {
			echo "outside connections are not allowed<br>";
		}

		// $return = ob_get_contents();
		// ob_end_clean();

		ini_set( 'default_socket_timeout', $old );
		return $return;
	}

	public static function calcPercent( $value, $total, $digits = 2 )
	{
		$multi = pow(10, $digits);
		$return = floor( $multi * 100 * ($value/$total) ) / $multi;
		return $return;
	}

	public static function httpStatusCode( $code )
	{
		if( $code ){
			$text = '';

			$stati = array(
				200	=> 'OK',
				201	=> 'Created',
				202	=> 'Accepted',
				203	=> 'Non-Authoritative Information',
				204	=> 'No Content',
				205	=> 'Reset Content',
				206	=> 'Partial Content',

				300	=> 'Multiple Choices',
				301	=> 'Moved Permanently',
				302	=> 'Found',
				304	=> 'Not Modified',
				305	=> 'Use Proxy',
				307	=> 'Temporary Redirect',

				400	=> 'Bad Request',
				401	=> 'Unauthorized',
				403	=> 'Forbidden',
				404	=> 'Not Found',
				405	=> 'Method Not Allowed',
				406	=> 'Not Acceptable',
				407	=> 'Proxy Authentication Required',
				408	=> 'Request Timeout',
				409	=> 'Conflict',
				410	=> 'Gone',
				411	=> 'Length Required',
				412	=> 'Precondition Failed',
				413	=> 'Request Entity Too Large',
				414	=> 'Request-URI Too Long',
				415	=> 'Unsupported Media Type',
				416	=> 'Requested Range Not Satisfiable',
				417	=> 'Expectation Failed',
				422	=> 'Unprocessable Entity',

				500	=> 'Internal Server Error',
				501	=> 'Not Implemented',
				502	=> 'Bad Gateway',
				503	=> 'Service Unavailable',
				504	=> 'Gateway Timeout',
				505	=> 'HTTP Version Not Supported'
			);

			if (isset($stati[$code]) AND $text == ''){
				$text = $stati[$code];
			}

			if ($text == ''){
				echo 'No status text available.  Please check your status code number or supply your own message text.';
			}

			$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
			if (substr(php_sapi_name(), 0, 3) == 'cgi'){
				header("Status: {$code} {$text}", TRUE);
			}
			elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
				header($server_protocol." {$code} {$text}", TRUE, $code);
			}
			else {
				header("HTTP/1.1 {$code} {$text}", TRUE, $code);
			}
		}
	}
}