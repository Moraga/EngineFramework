<?php
/**
 * Sets or gets reports message
 * @param string $message The message
 * @param string $code The code of the message
 * @return string The message
 */
function report($message=null, $code=null) {
	// set
	if ($message) {
		$_SESSION['report'] = array($message, $code);
	}
	// get
	elseif (isset($_SESSION['report'])) {
		$report = $_SESSION['report'];
		
		unset($_SESSION['report']);
		
		return
			'<div class="report report-'.$report[1].'">'.
				'<div>'.$report[0].'</div>'.
			'</div>';
	}
}

?>