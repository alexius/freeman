<?php
/**
 * Класс для отправки писем из системы
 * реализующий возможность конвертации писем в различные
 * кодировки а также составление писем из шаблонов в базе данных.
 * Класс не является универсальным так как пока не позволяет переопределить отправителя и т.д.
 *
 */
class Core_Mailer extends Zend_Mail {
	/**
	 * Кодировка отправляемых писем
	 *
	 * @var string
	 */
	protected $_charset = 'windows-1251';

	/**
	 * Хранилище переменных для замены
	 *
	 * @var array
	 */
	private $_variables = array ();

	/**
	 * Констурктор. Принимает в качестве параметров
	 * имя письма в базе данных а также масив переменных для замены.
	 *
	 * @param string $mail_name
	 * @param array $vars
	 */
	public function __construct($mail_name = null, $vars = null, $type = 0, $delivery = 0) {
		if (! empty ( $mail_name ) && ! empty ( $vars )) {
			$this->_variables = $vars;

			$letter = Core_Model_DbTable_Mail::getMail ( $mail_name );

			if (empty ( $letter )) {
				throw new Exception ( Core_Model_Errors::getError ( 94 ) );
				return;
			}

			if (! $type)
				$this->setBodyText ( $this->_replace ( $letter ['mail_body'] ) );
			else if ($type)
				$this->setBodyHtml ( $this->_replace ( $letter ['mail_body'] ) );
			$this->setSubject ( $this->_replace ( $letter ['mail_subject'] ) );
			$this->setFrom ( 'info@s2b.com.ua', 'Тендер S2B - Сервисное сообщение' );
		}
	}

	/**
	 * Конвертирует строку в кодировку windows-1251
	 * так как не все почтовые клиенты понимают utf-8
	 *
	 * @param string $txt
	 * @return string
	 */
	private function _convert($txt) {
		//return $txt;
		return iconv ( 'utf-8', $this->_charset, $txt );
	}

	/**
	 * Заменяет все переменные найденные в тексте
	 * $replacment на их значения
	 *
	 * @param string $replacment
	 * @return string
	 */
	private function _replace($text) {
		foreach ( $this->_variables as $var => $replacment ) {
			$text = preg_replace ( '/{{' . $var . '}}/', $replacment, $text );
		}
		return $text;
	}

	/**
	 * Текст письма. Создано для возможности отправки писем без базы данных.
	 *
	 * @param string
	 */
	public function setBodyText($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		$txt = $this->_convert ( $txt );

		parent::setBodyText ( $txt, $charset, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE );
	}

	public function setBodyHtml($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		$txt = $this->_convert ( $txt );

		parent::setBodyHtml ( $txt, $charset, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE );
	}
	/**
	 * Определяет отправителя
	 *
	 * @param string $email
	 * @param string $name
	 */
	public function setFrom($email, $name = null) {
		if (! empty ( $name )) {
			$name = $this->_convert ( $name );
		}
		parent::setFrom ( $email, $name );
	}

	/**
	 * Определяет получателя
	 *
	 * @param string $email
	 * @param string $name
	 */
	public function addTo($email, $name = '') {
		if (! empty ( $name )) {
			$name = $this->_convert ( $name );
		}

		parent::addTo ( $email, $name );
	}

	/**
	 * Определяет тему письма. Создано для возможности отправки писем без базы данных.
	 *
	 * @param string $subject
	 */
	public function setSubject($subject) {
		parent::setSubject ( $this->_convert ( $subject ) );
	}

	protected function encodeQuotedPrintable($str, $lineLength = Zend_Mime::LINELENGTH) {
		$out = '';
		$str = str_replace ( '=', '=3D', $str );
		$str = str_replace ( Zend_Mime::$qpKeys, Zend_Mime::$qpReplaceValues, $str );
		$str = rtrim ( $str );
		// Split encoded text into separate lines
		while ( $str ) {
			$ptr = strlen ( $str );
			if ($ptr > $lineLength) {
				$ptr = $lineLength;
			}
			// Ensure we are not splitting across an encoded character
			$pos = strrpos ( substr ( $str, 0, $ptr ), '=' );
			if ($pos !== false && $pos >= $ptr - 2) {
				$ptr = $pos;
			}
			// Check if there is a space at the end of the line and rewind
			if ($ptr > 0 && $str [$ptr - 1] == ' ') {
				-- $ptr;
			}
			// Add string and continue
			$out .= substr ( $str, 0, $ptr );
			$str = substr ( $str, $ptr );
		}
		return $out;
	}

	/**
	 * Encode header fields
	 *
	 * Encodes header content according to RFC1522 if it contains non-printable
	 * characters.
	 *
	 * @param  string $value
	 * @return string
	 */
	/*  protected function _encodeHeader($value)
     {
         if (Zend_Mime::isPrintable($value)) {
             return $value;
         } else {

             $quotedValue = '';
             $count = 1;
             for ($i=0; strlen($value)>$i;$i++) {
                 if ($value[$i] == '?' or $value[$i] == '_' or $value[$i] == ' ') {
                     $quotedValue .= str_replace(array('?', ' ', '_'), array('=3F', '=20', '=5F'), $value[$i]);
                 } else {
                     $quotedValue .= $this->encodeQuotedPrintable($value[$i]);
                 }
                 if (strlen($quotedValue)>$count*Zend_Mime::LINELENGTH) {
                     $count++;
                     $quotedValue .= "?=\n =?". $this->_charset . '?Q?';
                 }
             }
             return '=?' . $this->_charset . '?Q?' . $quotedValue . '?=';
         }
     }*/

	// Будем использовать 64енкод что бы не бились строки !!!
	protected function _encodeHeader($value) {
		if (Zend_Mime::isPrintable ( $value )) {
			return $value;
		} else {
			$quotedValue = '';
			$count = 1;
			$str = base64_encode ( $value );
			for($i = 0; strlen ( $str ) > $i; $i ++) {
				$quotedValue .= $str [$i];

				if (strlen ( $quotedValue ) > $count * (Zend_Mime::LINELENGTH)) {
					$count ++;
					$quotedValue .= "=?=\n =?" . $this->_charset . '?B?';
				}
			}

			return '=?' . $this->_charset . '?B?' . $quotedValue . '?=';
		}
	}

}
?>