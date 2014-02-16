<?php
/**
  * Catroid: An on-device visual programming system for Android devices
  * Copyright (C) 2010-2014 The Catrobat Team
  * (<http://developer.catrobat.org/credits>)
  * 
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU Affero General Public License as
  * published by the Free Software Foundation, either version 3 of the
  * License, or (at your option) any later version.
  * 
  * An additional term exception under section 7 of the GNU Affero
  * General Public License, version 3, is available at
  * http://developer.catrobat.org/license_additional_term
  * 
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU Affero General Public License for more details.
  * 
  * You should have received a copy of the GNU Affero General Public License
  * along with this program. If not, see <http://www.gnu.org/licenses/>.
  */

class CoreBadwordsFilter {
  private $insulting_words = array();
  private $unapproved_words = array();
  private $dbConnection;

  public function __construct($dbConnection) {
    $this->dbConnection = $dbConnection;
  }

  public function areThereInsultingWords($text) {
    $words = explode(" ", $this->unleetify($this->cleanSampleText($text)));

    foreach($words as $word) {
      if(!$this->checkWord($word)) {
        $this->addWord($word, 'true', 'false');
        if(!$this->checkWord($word)) {
          return -1;
        }
      }
    }

    if(count($this->insulting_words) > 0) return 1;
    else return 0;
  }

  public function addWord($word, $meaning, $approved) {
    $result = pg_execute($this->dbConnection, "add_word_to_wordlist", array(pg_escape_string($word), $meaning, $approved));
    if($result) {
      pg_free_result($result);
    }
  }

  public function checkWord($word) {
    $result = pg_execute($this->dbConnection, "get_word_from_wordlist", array(pg_escape_string($word)));
    if($result) {
      $word_standing = pg_fetch_all($result);
      pg_free_result($result);

      if($word_standing) {
        if($word_standing[0]['approved'] == 't') {
          if($word_standing[0]['good_meaning'] == 'f') {
            array_push($this->insulting_words, $word);
          }
        }
        else {
          array_push($this->unapproved_words, $word);
        }
        return true;
      }
    }
    return false;
  }

  public function mapUnapprovedWordsToProject($project_id) {
    if($this->getUnapprovedWords()) {
      foreach($this->getUnapprovedWords() as $word) {
        $result = pg_execute($this->dbConnection, "get_word_from_wordlist", array(pg_escape_string($word)));

        $unapproved_word_id = -1;
        if($result) {
          $word_id = pg_fetch_all($result);
          $unapproved_word_id = $word_id[0]['id'];
          pg_free_result($result);
        }
        $result = pg_execute($this->dbConnection, "add_mapping_to_unapproved_words_in_projects", array($project_id, $unapproved_word_id));
        if($result) {
          pg_free_result($result);
        }
      }
    }
  }

  public function getBadWords() {
    return $this->insulting_words;
  }

  public function getUnapprovedWords() {
    return $this->unapproved_words;
  }

  private function cleanSampleText($sample_text) {
    $result = preg_replace("/[\t\r\n]/", " ", mb_strtolower($sample_text, "UTF-8"));
    $result = preg_replace("/[ ]+/", " ", $result);
    $result = preg_replace("/[!,\.;:\?\"\'<>\*_\-]/", "", $result);
    $result = preg_replace("/[\(\)]/", "", $result);
    $result = preg_replace("/[\/]/", " ", $result);
    return $result;
  }

  private function unleetify($text)
  {
    $english = array("a", "e", "A", "o", "O", "t", "l");
    $leet = array("4", "3", "4", "0", "0", "+", "1");

    $result = "";
    for ($i = 0; $i < strlen($text); $i++)
    {
      $char = $text[$i];
      if(false !== ($pos = array_search($char, $leet))) {
        $char = $english[$pos];
      }

      $result .= $char;
    }
    return $result;
  }

  public function __destruct() {
  }
}

?>