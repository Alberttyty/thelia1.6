<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
require_once(__DIR__ . "/../fonctions/autoload.php");
require_once(__DIR__ . "/../lib/phpMailer/class.phpmailer.php");
require_once(__DIR__ . "/../lib/phpMailer/class.smtp.php");


class Mail extends PHPMailer
{
    function __construct()
    {
    		$this->LE = "\n";
        $this->IsHTML(true);
    		$this->CharSet = "UTF-8";
    		$this->SetLanguage("en", realpath(dirname(__FILE__)."/../lib/phpMailer/language/"));
  		  if (file_exists(SITE_DIR."template/images/mail_top.gif")) $this->AddEmbeddedImage(SITE_DIR."template/images/mail_top.gif", 'mail_top', 'mail_top.gif');
  	    if (file_exists(SITE_DIR."template/images/mail_bottom.gif")) $this->AddEmbeddedImage(SITE_DIR."template/images/mail_bottom.gif", 'mail_bottom', 'mail_bottom.gif');
    }

  	public function AddrFormat($addr)
    {
    		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $formatted = $this->SecureHeader($addr[0]);
    		else {
        		if (empty($addr[1])) $formatted = $this->SecureHeader($addr[0]);
         		else $formatted = $this->EncodeHeader($this->SecureHeader($addr[1]), 'phrase') . " <" . $this->SecureHeader($addr[0]) . ">";
    		}

       	return $formatted;
    }

    /*
     * Un helper pour creer un mailer simplement
     */
    public static function creermailer()
    {
      	$mailclient = new Mail();
    		$smtp = new Smtpconfig(1);

    		if ($smtp->active) {
      			$mailclient->IsSMTP();
      			$mailclient->Host = $smtp->serveur;
      			$mailclient->Port = ($smtp->port!="")?$smtp->port:25;

      			if ($smtp->username != "") {
        				$mailclient->SMTPAuth = true;
        				$mailclient->Username = $smtp->username;
        				$mailclient->Password = $smtp->password;

        				$mailclient->SMTPSecure = ($smtp->secure != "")?$smtp->secure:"";
      			}
    		}
    		else $mailclient->IsMail();

    		return $mailclient;
    }

    /*
     * Un helper pour envoyer un mail simple simplement
     */
    public static function envoyer(
					$to_name, $to_address,
					$from_name, $from_address,
					$subject,
					$msg_html, $msg_text,
          $pj = ''
    )
    {
     		$mailclient = self::creermailer();

    		$mailclient->FromName = /*utf8_decode(*/$from_name/*)*/;
    		$mailclient->From = $from_address;
        /*Ajout*/$mailclient->Sender = $from_address;
        /*Ajout*/$mailclient->Hostname = substr(strrchr($from_address,'@'),1);

    		$mailclient->AddAddress($to_address, /*utf8_decode(*/$to_name/*)*/);

    		$mailclient->Subject = /*utf8_decode(*/$subject/*)*/;

    		if (empty($msg_html)) $mailclient->Body = /*utf8_decode(*/$msg_text/*)*/;
    		else {
      			$mailclient->MsgHTML(/*utf8_decode(*/$msg_html/*)*/);
      			$mailclient->AltBody = /*utf8_decode(*/$msg_text/*)*/;
    		}

        if (!empty($pj)) {
            $mailclient->addAttachment(
                $pj['tmp_name'],
                $pj['name']
            );
        }

    		return $mailclient->send();
  	}
}

?>
