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
    $this->_text = $this->wordwrap($text);
    $this->_return = "-f" . ADMIN_EMAIL_WEBMASTER;
    $this->_reply = ADMIN_EMAIL_NOREPLY;
	  $this->_from = ADMIN_EMAIL_NOREPLY;
	  $this->_to = ADMIN_EMAIL_WEBMASTER;
	  $this->_bcc = '';
	
	  return($this->send());
  }
  public function wordwrap($str, $length = 76, $end = "\r\n") {
    $lastUnwrap = strrpos($str, "{/unwrap}");
    preg_match_all('|(.*?){unwrap}(.*?){/unwrap}|ism', $str, $parts);
  
    $result = '';
    if($lastUnwrap !== false) {
      for($counter = 0, $numberOfUnwraps = count($parts[1]); $counter < $numberOfUnwraps; $counter++) {
        $temp = explode($end, $parts[1][$counter]);
        array_pop($temp);
        foreach($temp as $piece) {
          $result .= wordwrap($piece, $length, $end) . $end;
        }
        $result .= $parts[2][$counter] . $end;
      }
      $str = substr($str, $lastUnwrap + 9);
    }
  
    $temp = explode($end, $str);
    foreach($temp as $piece) {
      $result .= wordwrap($piece, $length, $end) . $end;
    }
    return $result;
  }
    
  public function chunk_split_unicode1($str, $length = 76, $end = "\r\n") {
    $lastUnwrap = strrpos($str, "{/unwrap}");
    preg_match_all('|(.*?){unwrap}(.*?){/unwrap}|ism', $str, $parts);
    
    $result = '';
    if($lastUnwrap !== false) {
      for($counter = 0, $numberOfUnwraps = count($parts[1]); $counter < $numberOfUnwraps; $counter++) {
        $tmp = array_chunk(preg_split("//u", $parts[1][$counter], -1, PREG_SPLIT_NO_EMPTY), $length);
        foreach ($tmp as $line) {
          $result .= implode('', $line) . $end;
        }
        $result .= $parts[2][$counter] . $end;
      }
      $str = substr($str, $lastUnwrap + 9);
    }
    
    $tmp = array_chunk(preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $length);
    foreach ($tmp as $line) {
      $result .= implode('', $line) . $end;
    }
    return $result;
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