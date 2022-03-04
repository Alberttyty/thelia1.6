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
require_once __DIR__ . "/../fonctions/autoload.php";

class Smtp
{
    public $server;
    public $port=25;
    public $from;
    public $rcpt;
    public $subject;
    public $texte;

    function __construct()
    {}

    function ligne($fp, $msg, $vide=0)
    {
        fputs($fp, "$msg");
        if ($vide) fgets($fp, 1024);
    }

    function envoyer()
    {
        $fp = fsockopen($this->server, $this->port);

        $this->ligne($fp, "helo server\r\n");
        $this->ligne($fp, "mail from: " . $this->from . "\r\n");
        $this->ligne($fp, "rcpt to: " . $this->rcpt . "\r\n");
        $this->ligne($fp, "data\r\n");
        $this->ligne($fp, "From: " . $this->from . "\r\n");
        $this->ligne($fp, "To: " . $this->rcpt ."\r\n");
        $this->ligne($fp, "Subject: " . $this->subject . "\r\n");
        $this->ligne($fp, "\r\n");
        $this->ligne($fp, $this->texte . "\r\n");
        $this->ligne($fp, ".\r\n", 1);
   }

}
?>
