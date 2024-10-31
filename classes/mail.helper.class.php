<?php

if ( $phpmailerversion = bulkmail_option( 'php_mailer' ) ) :

	if ( ! class_exists( 'PHPMailer_bulkmail' ) ) {
		require_once BULKEMAIL_DIR . 'classes/libs/phpmailer/class.phpmailer.php';
	}

	if ( ! class_exists( 'SMTP_bulkmail' ) ) {
		require_once BULKEMAIL_DIR . 'classes/libs/phpmailer/class.smtp.php';
	}

	class _bulkmail_SMTP extends SMTP_bulkmail {};
	class _bulkmail_mail_helper extends PHPMailer_bulkmail {};
	class _bulkmail_phpmailerException extends phpmailerException_bulkmail{};

	// since WordPress 5.5
elseif ( file_exists( ABSPATH . WPINC . '/PHPMailer/PHPMailer.php' ) ) :

	require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
	require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';

	class_alias( PHPMailer\PHPMailer\PHPMailer::class, 'PHPMailer' );
	class_alias( PHPMailer\PHPMailer\Exception::class, 'phpmailerException' );
	class_alias( PHPMailer\PHPMailer\SMTP::class, 'SMTP' );

	class _bulkmail_SMTP extends SMTP {};
	class _bulkmail_mail_helper extends PHPMailer {};
	class _bulkmail_phpmailerException extends phpmailerException {};

else :

	global $phpmailer;
	if ( ! is_object( $phpmailer ) || ! $phpmailer instanceof PHPMailer ) {
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
		$phpmailer = new PHPMailer( true );
	}
	if ( ! class_exists( 'SMTP' ) ) {
		require_once ABSPATH . WPINC . '/class-smtp.php';
	}

	class _bulkmail_SMTP extends SMTP {};
	class _bulkmail_mail_helper extends PHPMailer {};
	class _bulkmail_phpmailerException extends phpmailerException {};

endif;

// this class extends PHPMailer and offers some fixes
class bulkmail_mail_helper extends _bulkmail_mail_helper {

	/**
	 *
	 *
	 * @param unknown $exceptions (optional)
	 */
	public function __construct( $exceptions = false ) {
		$this->Version     = defined( 'self::VERSION' ) ? self::VERSION : $this->Version;
		$this->XMailer     = 'Bulkmail ' . BULKEMAIL_VERSION . ' (' . $this->Version . ')';
		$this->CharSet     = bulkmail_option( 'charset', 'UTF-8' );
		$this->Encoding    = bulkmail_option( 'encoding', '8bit' );
		$this->Ical        = apply_filters( 'mymail_ical', apply_filters( 'bulkmail_ical', '' ) );
		$this->SMTPDebug   = 0; // 0 = off, 1 = commands, 2 = commands and data
		$this->SMTPOptions = apply_filters(
			'mymail_smtp_options',
			apply_filters( 'bulkmail_smtp_options', bulkmail_option( 'allow_self_signed' ) )
			? array(
				'ssl' => array(
					'verify_peer'       => false,
					'verify_peer_name'  => false,
					'allow_self_signed' => true,
				),
			) : array()
		);
		$this->Debugoutput = 'error_log'; // Options: "echo", "html" or "error_log;
		$this->AllowEmpty  = true;
		parent::__construct( $exceptions );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function setAsSMTP() {

		if ( ! is_object( $this->smtp ) ) {
			$this->smtp = new _bulkmail_SMTP();
		}
		return $this->smtp;
	}


	/**
	 *
	 *
	 * @param unknown $address
	 * @param unknown $patternselect (optional)
	 * @return unknown
	 */
	public static function ValidateAddress( $address, $patternselect = 'auto' ) {
		return bulkmail_is_email( $address );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function PreSend() {

		try {
			return parent::PreSend();

		} catch ( _bulkmail_phpmailerException $e ) {
			$this->SetError( $e->getMessage() );
			if ( $this->exceptions ) {
				throw $e;
			}
			return false;
		}

	}


	/**
	 *
	 *
	 * @param unknown $text
	 * @param unknown $breaktype (optional)
	 * @return unknown
	 */
	public static function normalizeBreaks( $text, $breaktype = "\r\n" ) {
		return preg_replace( '/(\r\n|\r|\n)/ms', $breaktype, $text );
	}


	/**
	 *
	 *
	 * @param unknown $html
	 * @param unknown $advanced (optional)
	 * @return unknown
	 */
	public function html2text( $html, $advanced = false ) {

		preg_match( '#<body[^>]*>.*?<\/body>#is', $html, $matches );

		if ( ! empty( $matches ) ) {
			$html = $matches[0];
		}

		$text       = preg_replace( '# +#', ' ', $html );
		$text       = str_replace( array( "\n", "\r", "\t" ), '', $text );
		$piclinks   = '/< *a[^>]*href *= *"([^#][^"]*)"[^>]*> *< *img[^>]*> *< *\/ *a *>/Uis';
		$style      = '#< *style(?:(?!< */ *style *>).)*< */ *style *>#isU';
		$strikeTags = '#< *strike(?:(?!< */ *strike *>).)*< */ *strike *>#iU';
		$headlines  = '#< *(h1|h2)[^>]*>#Ui';
		$stars      = '#< *li[^>]*>#Ui';
		$return1    = '#< */ *(li|td|tr|div|p)[^>]*> *< *(li|td|tr|div|p)[^>]*>#Ui';
		$return2    = '#< */? *(br|p|h1|h2|legend|h3|li|ul|h4|h5|h6|tr|td|div)[^>]*>#Ui';
		$links      = '/< *a[^>]*href *= *"([^#][^"]*)"[^>]*>(.*)< *\/ *a *>/Uis';
		$text       = preg_replace( array( $piclinks, $style, $strikeTags, $headlines, $stars, $return1, $return2, $links ), array( '${1}' . "\n", '', '', "\n\n", "\n&#x25CF; ", "\n", "\n", '${2} ( ${1} )' ), $text );
		$text       = str_replace( '&nbsp;', ' ', strip_tags( $text ) );
		$text       = trim( @html_entity_decode( $text, ENT_QUOTES, $this->CharSet ) );
		$text       = preg_replace( '# +#', ' ', $text );
		$text       = preg_replace( '#\n *\n\s+#', "\n\n", $text );

		return html_entity_decode(
			$text,
			ENT_QUOTES,
			$this->CharSet
		);

	}


}


class mailerException extends Exception {}
