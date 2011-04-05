<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class BadWordsFilter {
	private $insulting_words = array();
	private $unapproved_words = array();
  private $dbconnnection;
	
	public function __construct($db) {
	  $this->dbconnnection = $db;
	}

	public function areThereInsultingWords($text) {
		$words = explode(" ", $this->unleetify($this->cleanSampleText($text)));

		foreach($words as $word) {
			if(!$this->checkWord($word)) {
				switch($this->checkWordOnWordnet($word)) {
					case -1:
						$this->addWord($word, 'true', 'false');
						break;
					case 0:
						$this->addWord($word, 'false', 'true');
						break;
					case 1:
						$this->addWord($word, 'true', 'true');
						break;
					default:
						$this->addWord($word, 'true', 'false');
				}
				if(!$this->checkWord($word))
				return -1;
			}
		}

		if(count($this->insulting_words) > 0) return 1;
		else return 0;
	}

	public function addWord($word, $meaning, $approved) {
		$query = "EXECUTE add_word_to_wordlist('".utf8_decode($word)."', $meaning, $approved);";
		$result = @pg_query($this->dbconnnection, $query);
		if($result) {
			pg_free_result($result);
		}
	}

	public function checkWord($word) {
		$query = "EXECUTE get_word_from_wordlist('".utf8_decode($word)."');";
		$result = @pg_query($this->dbconnnection, $query);
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

	public function getBadWords() {
		return $this->insulting_words;
	}

	public function getUnapprovedWords() {
		return $this->unapproved_words;
	}

	public function mapUnapprovedWordsToProject($project_id) {
		if($this->getUnapprovedWords()) {
			foreach($this->getUnapprovedWords() as $word) {
				$query = "EXECUTE get_word_from_wordlist('".utf8_decode($word)."');";
				$result = @pg_query($this->dbconnnection, $query) or
          $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
          
				$unapproved_word_id = -1;
				if($result) {
					$word_id = pg_fetch_all($result);
					$unapproved_word_id = $word_id[0]['id'];
					pg_free_result($result);
				}

				$query = "EXECUTE add_mapping_to_unapproved_words_in_projects('$project_id', '$unapproved_word_id');";
				$result = @pg_query($this->dbconnnection, $query);
				if($result) {
					pg_free_result($result);
				}
			}
		}
	}

	private function cleanSampleText($sample_text) {
		$result = preg_replace("/[\t\r\n]/", " ", strtolower($sample_text));
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

	private function checkWordOnNoswearing($word)
	{
		$contents = $this->getURLContents("http://www.noswearing.com/search.php?st=$word");

		if(strpos($contents, "<table width='650'><tr><td valign=top></td><td valign=top width='250'>") !== false) {
			return -1;
		}
		return 0;
	}

	public function checkWordOnWordnet($word)
	{
		return -1;
//		$contents = $this->getURLContents("http://wordnetweb.princeton.edu/perl/webwn?s=$word&sub=Search+WordNet");
//
//		if($contents == "") {
//			return -1;
//		} elseif(strpos($contents, "Your search did not return any results.") !== false) {
//			return -1;
//		} elseif(strpos($contents, "insulting terms") !== false) {
//			return 0;
//		} elseif(strpos($contents, "obscene terms") !== false) {
//			return 0;
//		}	elseif(strpos($contents, "sexual intercourse") !== false) {
//			return 0;
//		}
//
//		return 1;
	}

//	private function getURLContents($url)	{
//		$curl_handler = curl_init();
//		curl_setopt($curl_handler, CURLOPT_URL, $url);
//		curl_setopt($curl_handler, CURLOPT_TIMEOUT, 10);
//		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
//		$contents = trim(curl_exec($curl_handler));
//		curl_close($curl_handler);
//
//		return $contents;
//	}

	public function __destruct() {
	}
}

?>
