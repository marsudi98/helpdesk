<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.4.3                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2019 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

class JAK_rewrite {

	private $url_seg;
	private $data = array();
	
	// This constructor can be used for all classes:
	
	public function __construct($url) {
		$this->url = $url;
	}
	
	function jakGetseg($var) {

		if (isset($var)) {
	
			if (JAK_USE_APACHE) {
			
				$url = str_replace(_APP_MAIN_DIR, '', $this->url);
				$_tmp = explode('?', $url);
				$url = $_tmp[0];
				
				if ($url = explode('/', $url)) {
				    foreach ($url as $d) {
				        if ($d) {
				            $data[] = $d;
				        }
				    }
				}
				
				if (!empty($data[$var])) $url_seg = $data[$var];
			
			} else {
		
				// get the url and parse it
				$parseurl = parse_url($this->url);
				
				if (!empty($parseurl["query"])) {
					// get only the query
					$parameters = $parseurl["query"];
					parse_str($parameters, $data);
					
					// Now we have to set the array to basic keys
					if (!empty($data)) foreach($data as $d) {
						$data[] = $d;
					}
				
					if (!empty($data[$var])) $url_seg = $data[$var];

				}
			}

		
			if (!empty($url_seg)) return $url_seg;

		}
	}

	public static function jakCleanurl($str, $options = array()) {
			
		$defaults = array(
			'delimiter' => '-',
			'limit' => null,
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => true,
		);
			
		// Merge options
		$options = array_merge($defaults, $options);
			
		$char_map = array(
				// Latin
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
				'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
				'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
				'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
				'ß' => 'ss', 
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
				'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
				'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
				'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
				'ÿ' => 'y',
		
				// Latin symbols
				'©' => '(c)',
		
				// Greek
				'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
				'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
				'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
				'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
				'Ϋ' => 'Y',
				'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
				'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
				'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
				'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
				'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
		
				// Turkish
				'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
				'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
		
				// Russian
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
				'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
				'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
				'Я' => 'Ya',
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
				'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
				'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
				'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
				'я' => 'ya',
		
				// Ukrainian
				'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
				'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
		
				// Czech
				'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
				'Ž' => 'Z', 
				'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
				'ž' => 'z', 
		
				// Polish
				'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
				'Ż' => 'Z', 
				'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
				'ż' => 'z',
		
				// Latvian
				'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
				'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
				'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
				'š' => 's', 'ū' => 'u', 'ž' => 'z'
			);
			
			// Make custom replacements
			if (!empty($options['replacements'])) {
				$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
			}
			
			// Transliterate characters to ASCII
			if ($options['transliterate']) {
				$str = str_replace(array_keys($char_map), $char_map, $str);
			}
			
			// Replace non-alphanumeric characters with our delimiter
			$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
			
			// Remove duplicate delimiters
			$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
			
			// Truncate slug to max. characters
			$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
			
			// Remove delimiter from ends
			$str = trim($str, $options['delimiter']);
			
		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	
	}
	
	public static function jakParseurl($var, $var1= '', $var2 = '', $var3 = '', $var4 = '', $var5 = '', $var6 = '', $var7 = '')
	{
	
		// Set v to zero
		$v = $v1 = $v2 = $v3 = $v4 = $v5 = $v6 = $v7 = $varname = '';
		
		// Check if is/not apache and create url
		if (!JAK_USE_APACHE) {
				
			if (!empty($var1)) {
				$v = '&amp;sp='.htmlspecialchars($var1);
			}
			if (!empty($var2)) {
				$v1 = '&amp;ssp='.htmlspecialchars($var2);
			}
			if (!empty($var3)) {
				$v2 = '&amp;sssp='.htmlspecialchars($var3);
			}
			
			if (!empty($var4)) {
				$v3 = '&amp;ssssp='.htmlspecialchars($var4);
			}
			
			if (!empty($var5)) {
				$v4 = '&amp;sssssp='.htmlspecialchars($var5);
			}

			if (!empty($var6)) {
				$v5 = '&amp;sssssp='.htmlspecialchars($var6);
			}

			if (!empty($var7)) {
				$v6 = '&amp;sssssp='.htmlspecialchars($var7);
			}
			
			// if not apache add some stuff to the url
			if ($var) {
				if ($var == JAK_OPERATOR_LOC) {
					$var = JAK_OPERATOR_LOC.'/index.php?p='.htmlspecialchars($var1);
					$varname = BASE_URL.html_entity_decode($var.$v1.$v2.$v3.$v4.$v5.$v6);
				} else {
					$var = 'index.php?p='.htmlspecialchars($var);
					$varname = BASE_URL.html_entity_decode($var.$v.$v1.$v2.$v3.$v4.$v5.$v6);
				}
			} else {
				$var = '/';
				$varname = BASE_URL.html_entity_decode($var.$v.$v1.$v2.$v3.$v4.$v5.$v6);
			}
		
		} else {
					
			if (!empty($var1)) {
				$v = '/'.htmlspecialchars($var1);
			}
			if (!empty($var2)) {
				$v1 = '/'.htmlspecialchars($var2);
			}
			if (!empty($var3)) {
				$v2 = '/'.htmlspecialchars($var3);
			}
			if (!empty($var4)) {
				$v3 = '/'.htmlspecialchars($var4);
			}
			if (!empty($var5)) {
				$v4 = '/'.htmlspecialchars($var5);
			}
			if (!empty($var6)) {
				$v5 = '/'.htmlspecialchars($var6);
			}
			if (!empty($var7)) {
				$v6 = '/'.htmlspecialchars($var7);
			}
			
			// page is always the same
			$var = htmlspecialchars($var);

			// Now se the var for apache
			$varname = BASE_URL.$var.$v.$v1.$v2.$v3.$v4.$v5.$v6;
				
		}
		
		if (!empty($varname)) return $varname;
		
	}
	
	public static function jakParseurlpaginate($var) {
	
		$varname = '';
		
		if ($var != 1) {
			// Check if is/not apache and create url
			if (!JAK_USE_APACHE && $var) {
				// Now se the var for none apache
				$varname = '&amp;page='.$var;
			} else {
				// Now se the var for seo apache
				$varname = '/'.$var;
			}
		}
		
		return $varname;
	
	}
	
	public function jakRealrequest()
	{
		return str_replace(_APP_MAIN_DIR, '', $this->url);
	}
}
?>