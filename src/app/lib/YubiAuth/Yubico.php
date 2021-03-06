<?php
  /**
   * Class for verifying Yubico One-Time-Passcodes
   *
   * LICENSE:
   *
   * Copyright (c) 2007, 2008  Simon Josefsson.  All rights reserved.
   *
   * Redistribution and use in source and binary forms, with or without
   * modification, are permitted provided that the following conditions
   * are met:
   *
   * o Redistributions of source code must retain the above copyright
   *   notice, this list of conditions and the following disclaimer.
   * o Redistributions in binary form must reproduce the above copyright
   *   notice, this list of conditions and the following disclaimer in the
   *   documentation and/or other materials provided with the distribution.
   * o The names of the authors may not be used to endorse or promote
   *   products derived from this software without specific prior written
   *   permission.
   *
   * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
   * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
   * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
   * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
   * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
   * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
   * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
   * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
   * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
   * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
   * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
   *
   * @category    Auth
   * @package     Auth_Yubico
   * @author      Simon Josefsson <simon@yubico.com>
   * @copyright   2008 Simon Josefsson
   * @license     http://opensource.org/licenses/bsd-license.php New BSD License
   * @version     CVS: $Id: Yubico.php,v 1.7 2007-10-22 12:56:14 jas Exp $
   * @link        http://yubico.com/
   */

require_once 'PEAR.php';

/**
 * Class for verifying Yubico One-Time-Passcodes
 *
 * Simple example:
 * <code>
 * require_once 'Auth/Yubico.php';
 * $yubi = &new Auth_Yubico('42');
 * $auth = $yubi->verify("ccbbddeertkrctjkkcglfndnlihhnvekchkcctif");
 * if (PEAR::isError($auth)) {
 *    print "<p>Authentication failed: " . $auth->getMessage();
 *    print "<p>Debug output from server: " . $yubi->getLastResponse();
 * } else {
 *    print "<p>You are authenticated!";
 * }
 * </code>
 */
class Auth_Yubico
{
	/**#@+
	 * @access private
	 */

	/**
	 * Yubico client ID
	 * @var string
	 */
	var $_id;

	/**
	 * Yubico client key
	 * @var string
	 */
	var $_key;

	/**
	 * Response from server
	 * @var string
	 */
	var $_response;

	/**
	 * Constructor
	 *
	 * Sets up the object
	 * @param    string  The client identity
	 * @param    string  The client MAC key (optional)
	 * @access public
	 */
	function Auth_Yubico($id, $key = '')
	{
		$this->_id =  $id;
		$this->_key = $key;
	}

	/**
	 * Return the last data received from the server, if any.
	 *
	 * @return string		Output from server.
	 * @access public
	 */
	function getLastResponse()
	{
		return $this->_response;
	}

	/* TODO? Add functions to get parsed parts of server response? */

	/**
	 * Verify Yubico OTP
	 *
	 * @param string $token     Yubico OTP
	 * @return mixed            PEAR error on error, true otherwise
	 * @access public
	 */
	function verify($token)
	{
		/* TODO: Generate signature here. */

		/* TODO: Support https. */

		$url = "http://api.yubico.com/wsapi/verify?id=" .
			$this->_id . "&otp=" . $token;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, "PEAR Auth_Yubico");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$this->_response = curl_exec($ch);
		curl_close($ch);

		/* TODO: Verify signature here. */

		if(!preg_match("/status=([a-zA-Z0-9_]+)/", $this->_response, $out)) {
			return PEAR::raiseError('Could not parse response');
		}

		$status = $out[1];

		if ($status != 'OK') {
			return PEAR::raiseError($status);
		}

		return true;
	}
}
?>
