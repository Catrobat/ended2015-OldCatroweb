<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class CoreMailHandler {
  private $_return;
  private $_reply;
  private $_from;
  private $_subject;
  private $_text;
  private $_to;
  private $_bcc;
 
  public function __construct() {
     
  }

  public function sendUserMail($subject, $text, $userAddress) {
    if(!$subject || !$text || !$userAddress) {
      return false;
    }
    $this->_subject = USER_EMAIL_SUBJECT_PREFIX.' - '.$subject;
    $this->_text = $this->chunk_split_unicode($text);
    $this->_return = "-f".USER_EMAIL_NOREPLY;
    $this->_reply = USER_EMAIL_NOREPLY;
	  $this->_from = USER_EMAIL_NOREPLY;
	  $this->_to = $userAddress;
	  $this->_bcc = '';
	
	  return($this->send());
  }

  public function sendAdministrationMail($subject, $text) {
    if (!(SEND_NOTIFICATION_EMAIL))
        return false;
    if(!$subject || !$text) {
      return false;
    }
    $this->_subject = ADMIN_EMAIL_SUBJECT_PREFIX.' - '.$subject;
    $this->_text = $this->chunk_split_unicode($text);
    $this->_return = "-f".ADMIN_EMAIL_WEBMASTER;
    $this->_reply = ADMIN_EMAIL_NOREPLY;
	  $this->_from = ADMIN_EMAIL_NOREPLY;
	  $this->_to = ADMIN_EMAIL_WEBMASTER;
	  $this->_bcc = '';
	
	  return($this->send());
  }
  
  private function chunk_split_unicode($str, $length = 76, $end = "\r\n") {
    $chuncks = explode('{unwrap}', $str);
    
    /*
    sdafdsafdsafdsafsa
    sadfdsfsadfdsafdsaf{/unwrap}safddsafsadf
    dsfsadfdsafdsaf{/unwrap}safddsaf
    safdsa{/unwrap}
    */
    
    
    $result = '';
    foreach($chuncks as $chunck) {
      $unwrapParts = explode('{/unwrap}', $chunck);
      
    }
    
    
    
    $tmp = array_chunk(preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $length);

    $string = "";
    foreach ($tmp as $line) {
      $string .= implode('', $line) . $end;
    }
    return $string;
  }
  
  private function send() {
    $header = "From: ".$this->_from."\r\n"."Bcc: ".$this->_bcc."\r\n"."Reply-To: ".$this->_reply.";\r\n"."X-Mailer: PHP/".phpversion();
    if(@mail($this->_to, $this->_subject, $this->_text, $header, $this->_return)) {
      return true;
    } else {
      return false;
    }
  }

  public function __destruct() {
  }
}

?>