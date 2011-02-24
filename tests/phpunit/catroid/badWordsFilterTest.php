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

require_once('testsBootstrap.php');

class badWordsFilterTest extends PHPUnit_Framework_TestCase
{
	protected $badWordsFilter;

	protected function setUp() {
		require_once CORE_BASE_PATH.'modules/catroid/BadWordsFilter.php';
		$this->badWordsFilter = new BadWordsFilter();
	}

	public function testAddingWordsToWordlist() {
		$word = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";

		$this->badWordsFilter->addWord($word, 'true', 'true');
		$this->assertTrue($this->badWordsFilter->checkWord($word));
		$this->deleteWord($word);

		$this->badWordsFilter->addWord($word, 'false', 'false');
		$this->badWordsFilter->checkWord($word);
		$this->assertTrue((count($this->badWordsFilter->getUnapprovedWords()) > 0));
		$this->deleteWord($word);

		$this->badWordsFilter->addWord($word, 'false', 'true');
		$this->badWordsFilter->checkWord($word);
		$this->assertTrue((count($this->badWordsFilter->getBadWords()) > 0));
		$this->deleteWord($word);

		$this->assertFalse($this->badWordsFilter->checkWord($word));
	}

/*	public function testOnlineLookup() {
		$this->assertTrue($this->badWordsFilter->checkWordOnWordnet('hello') == 1);
		$this->assertTrue($this->badWordsFilter->checkWordOnWordnet('fuck') == 0);
		$this->assertTrue($this->badWordsFilter->checkWordOnWordnet('akslj') == -1);
	}
*/
	public function testScratchDescriptions() {
		$text = "Guys, the \"bui\" thing is a joke. I know spanish for bye is adios. Shaddup!";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

    $text = "This is a real guide of what Scratchers hate the most, including myself. Enjoy this video. Use arrows Here's my website: http://mario555.weebly.com/index.html 2nd top viewed 7th top loved";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));
		
		$text = "♥ Remember to Press Love it! ♥ Please, please, please. If i get 200+ by the end of this week I'll add a new area. :DDD *Download and view in presentation mode for Zero lag. *A big thanks to Blade_Edge for making the very cool boss sprite. He's currently taking sprite requests! -=Download to Play=- Click to download: (link to project) ! -=Controls=- WASD/Arrow Keys: Move E: Talk to People/Use the Maps Q: Attack W/Up Arrow: Enter Mart Hold R: Run -=Story=- Along time ago, a warrior stood up to the challenge of defeating a dragon that was plauging the west of the FableQuest world. It has been five decades since then, and the World is in trouble again. This time, in the north, a dark king has taken over. He has strict rules and very high taxes. If anyone is reported to have disagreed with him, they will be hanged. CB hears this, as he is traveling across the land, and decides to rise up to the challenge of defeating him. But first, he must become a much more skilled warrior... -=Auto-Save=- The game has an auto save feature that allows you continue your game even after pressing the stop button. Also, save the project when closing so you can return to your progress next time you play. -=Dying=- When you die, press space. You will be sent back to n00b path, with no gold and 1 HP. Talk to Prize to be healed. -=Side Quests=- These are unlocked after competing the quest Max Dawn sets for you. There are 3, which involve simple tasks and can be done at any time. These will help you avoid grinding :)";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));
    
		$text = "Click the channels and watch the short films! Don't forget to check out my other projects!";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text = "Stop the hackers! A fake kaj account is an automatic ban so don't even try. To see some pictures of the hackers projects/accounts go to this: (link to project) Press love it! If you have any more suggestions or questions, post a comment. ADD ME FOR UPDATES. Credit to swifty2 for music.";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

    $text = "Adam and Jamie try to get a nuclear bomb to test their myth";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));
    
    $text = "Tag Anyone?-Get it? It means 2 things... Mi Prainz Hurtiez Welcome to Notebook Hero! This was made in like 2 hours and not that like 95% effort -------------------------------------------------- If i get 10 love its i will make a second with WAY better effort and levels HOLY FUTON! 1 HOUR AND ALLREADY 11 LOVEITS! WOW IM BECOMING FAMOUS! :) and as i said Notebook Hero 2 is coming out now! :) Woah, Works better online XD Credit to SM 2nd top viewed YESSSSSSSSSSSSS!!!!!!! :) thanks guys! 1329 views! :) 1st TOP VIEWED! YESH!!!!!!!!!!!!!!!!!!!!!!!!!! 3rd TOP LOVED YEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA!!!!!!!!!!!!!!!!! OMG 2nd TOP LOVED YESSSSSSSSSSSSSSSSSSSSSSSSSS TANK YOU SO MUCH! YESSSS WOOOOHOOOOO 1st top loved!";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));
    
    $text = "IM GOING TO DLETE THIS! I dont think kaj is real, and all you people seem to not get the idea. Its not for signtures, its for becoming a meeber. Im gonna go back to games,Click the prev and next buttons. Stop the hackers!!!!!!!! By remixing you are officiallly a member of the anti-hackers league ps: dont remix this one, go into the remixes and remix the one with the most signitures. Front paged again!!!!!! yay 1st one this year.";
    $this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text = "Yay! Featured 2/9/11 -------------------- An experiment! The lists play with the music! Timing is WAY off online. PLEASE DOWNLOAD!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Click green flag twice! Music- jovian sky from Scratch resources I hope you like it! ";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text ="Yes!!! I've reached my goal to upload 20 episodes in 1 month!!! 2-7-11 FRONT PAGE!!! YES!!! FEATURED!!!! I don't even know what that means and I'm happy! YES! Over 1000 veiws!!!!";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text = "This is my attempt at a virtual guitar. Drag the black dots to place the \"Fingers\" on different frets. While dragging, press X to mute the string, and O to leave the string unmuted, but open. Press space to strum. All notes were recorded on my guitar. One note at a time **NEW** Now, you can select any major OR minor chord, and it will set it for you. Then, flip through variations of the chord with the LEFT and RIGHT ARROWS NOTE: All the chord positions came right out of my head, so if I forgot something, tell me about it. Please give feedback! If you find a glitch, or have a suggestion, please tell me about it. ";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text = "Injoy a AVANCED 3D Optic lab simulaters! ONE OF SCRATCH'S BEST! (or that i know of :) ) Instuctions are in the word bar found in the top right corner! 4 moduals added! INJOY ON OF THE BEST OF D.O.G.'s! NO PHOTO SHOP SKETCH UP OR ANYTHING BUT SCRATCH!! ";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text = "*Love-its greatly appreciated* ________________________________________ -:ABOUT:- I started work on this 2 or 3 months on it but gave up shortly after. In late December I did more on it, yet still gave up. Recently, however, I worked on it more, added much, until it came to this, while still puny, release. :P You may be wondering what the heck this is. Well, it's a fake operating system (like Macintosh, Windows, Linux, etc (but there not fake, obviously :P ) ). It has few options (only 5) but more will be added in future versions. :) The app-like things are as follows: About I-OS, Preferences, Random word/number generator, City Creator (based on my previous project, but cut down a LOT), and finally but completely not least, Ice Paint Gold. I'll go over the apps now. The first, about, is pretty self-explanetory, so I'll not go into that. But the second, preferences, has two options: theme changing (changes the theme colour (Yes, the british spelling rules. :P), and brightness), and background changing (sadly, only has 5 BGs). The third option is city creator. A simple game where you place landscapes down to make your own town! (please check out my proper version found on my stuff. smile Thanks! ) The fourth is a random word/number generator, which, quite simply generates a random word/number! tongue The final option, in my opinion the best is, Ice paint gold or IPG. It is a simple paint program! ;D Oh, and BTW, you may be wondering why there's a 'skip' button for logging on. Well, I know a lot of people get bored typing in there username and password (not scratch one) so it's just to make life that little bit easier. (but I still wanted a login system thing) ________________________________________ -:Programs, inspiration, and stuffs:- Well, the biggest inspiration had to be 08Jackt with his epic OSs! (link to project) :D Thanks, Jack! Another inspirational OS is Wolfie1996's Firedance. (link to project) I used Gimp, and SPE for the graphics. And thanks to everyone who supported this or will ever support this! (Please report any bugs/glitches you find, as I expect there are many) ";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$text = "53: [ws] prepare for uploading to scratch website: turbo mode is not compulsory anymore; add text \"Initializing ...\" when resetting the board; some small refactorings to make questions more understandable. 52: [ws + auditorium]: move \"compute all possibilities at index\" to board. 51: [ws] unit tests with turbo mode: 23 seconds; without turbo mode: 550 seconds... 50: [ws] Pictures of letters instead of typed text in costumes to avoid the dissappearing text bug ((link to forums) 49: [ws] Variant that instead of immediately restarting \"compute all possibilities\" each time a new unique value is found, goes through the whole board and then only restarts. Speed difference for functional tests: old version 48: 310 seconds; new version 49: 211 seconds => speedup of almost 30%! So we keep it ;-) A few other new remarks and a more stable way to time the solver. 48: [ws] Added comments for further ideas. 47: [ws] Find more than one solution implemented and tested. 46: [ws] Small refactorings of unit tests. 45: [ws] If the user at the beginning does not know how to use the system, a press on the number or cursor keys tells that input is expected via mouse. 44: [ws] New way to check for conflicting values during input, which allows quicker reaction time for user. 43: [ws] Interactive tests added. Some refactorings. User cannot enter trivially conflicting numbers (same number in rows, columns, or quadrants) anymore. Additional unit test for new functionality and behaviour of solver: compute possibilities at index. 42: [ws] Functional test: Vorderman's 200: super-difficult \"less than 60 minutes = maximum points\" type (working). 41: [ws] Functional tests: Vorderman's 161 (working). 40: [ws] Functional tests: Vorderman's 160. Tests fine. Many refactorings. 39: [ws] Many refactorings. 38: [ws] perform_recursive_backtracking is unit-tested and works; other small refactorings. Buggy: difficult sudokus can be solved, but super-difficult sudokus stall the solver (sudokus from Carol Vorderman's How to do sudoku book). 37: [ws] find_a_solution; unit test for deterministic version of find_a_solution. 36: [ws] make_a_probabilistic_choice is unit-tested and works. 35: [ws] find_index_with_fewest_possibilities is unit-tested and works. Also a few refactorings at other places. 34: [ws] solve_sequence records deterministic choices; this is a preparation for a later nondeterministic search in order to be able to backtrack to the last probabilistic choice where we need to be able to undo all deterministic values found after the probabilistic choice that did not lead to a solution. 33: [ws] handle case when no value is possible for a field. 32: [ws] same as 31 but with solution found for a very simple sudoku. 31: [ws] solver: compute_all_possibilities: comprehensive unit tests for compute_all_possibilities. Then code, and everything works! 30: [ws] deleted all conflict counters etc. Refactored to manual set and reset, without automatic conflict resolution as a preparation for new solver approach. Hill-climbing / TABU search approach does not appear promising after tests with the old code as converging to solutions is *much* too slow (takes forever even for simple problems). Throw away 60% of code and unit tests ;-) 29: [ws] solver first probabilistically chooses quadrant and cell in quadrant, in both cases the probability being based on number of conflicts. Then it searches for the best partner in the quadrant and switches the two. 28: [ws] solver probabilistically identifies conflict areas and tries to switch positions in a quadrant, but so far only with places that do have a conflict themselves and that are not manually set (=fixed). 27: [ws] added unit_test for checking the correctness of the calculation of the number_of_conflicts_in_quadrants + the code to compute the latter. 26: [ws] solver template code. 25: [ws] Refactorings. 24: [ws] total_number_of conflicts: unit test, then implementation; perfect. 23: [ws] Refactorings. 22: [ws] \"c\" shows sum of number of row and column conflicts for each cell. 21: [ws] Added list for number of all conflicts (row, column) for each cell, then unit test to check correct updating of values, then code to compute the values. Everything works as expected. 20: [ws] clean up of the code and unit tests. 19: [ws] All TODOs resolved. Unit test: set value quadrant check works. Many refactorings for readability. New helping codefor debugging: pressing \"d\" deletes all intermediate board values that were not set by the user. 18: [ws] unit tests refactored for readability. 0 problems and 6 (!) todos remain. Documented work still to do in comments marked with TODO. Special case when field is edited by user but receives same value as before: how to handle updates of conflicts… 17: [ws] unit tests refactored for readability. 2 problems and 2 todos remain. 16: [ws] updating conflicts numbers in rows and columns of exchange partner in same quadrant implemented. Unit tests not satisfied by new code, so there must still be problems … 15: [ws] Refactorings, new problems. 14: [ws] New bugs, problematic pieces flagged for later investigations. 13: [ws] A few bugs resolved. 12: [ws] Refactoring of unit tests. Many bugs: identified and marked several places that could be connected to bugs in conflict updates. 11: [ws] Much bigger unit tests; added screen shots and explanations in costumes to unit tests. 10: [ws] clear_and_exchange_same_value_cell_in_quadrant implemented. factored unit tests slightly. Bug with incorrect propagation of valuers during entry by user. Corrected. 9: [ws] writer: show intermediate solution when \"s\" is pressed. 8: [ws] small refactorings. 7: [ws] unit test for set_value elaborated. Board message/methods: clear_same_value_cell_in_row, column, quadrant. clear_all, write_all_values. Small refactorings. Approach is to keep unique numbers on all fields, keep quadrants without conflicts, and counts conflicts in rows and columns all already *during* user input. Idea for later: implement a local search hillclimbing algorithm, maybe a TABU search type algorithm. 6: [ws] Small refactorings for clarity. 5: [ws] check for turbo speed mode; unit test for set_value. 4: [ws] solver object - no functionality yet. Initialization of board such that initial board is a correct filled Sudoku instance without conflicts and all fields filled. 3: [ws] Chooser, writer; unit test for clear_all. 2: [ws] Cursor follows mouse but stays in fields. Numbers entered via keyboard. 1: [ws] Picture of board. ";
		$this->assertTrue(($this->badWordsFilter->areThereInsultingWords($text) == 0));

		$filename = CORE_BASE_PATH."include/swear_words.txt";
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		$numberOfInsultingWords = 1;
		$words = explode("\n", $contents);
		foreach($words as $word) {
			if($word != "") {
				$this->deleteWord($word);
				$this->badWordsFilter->addWord($word, 'false', 'true');
				$this->badWordsFilter->areThereInsultingWords($word);
				$this->assertTrue((count($this->badWordsFilter->getBadWords()) == $numberOfInsultingWords++));
			}
		}
	}

	private function deleteWord($word) {
		$query = "DELETE FROM wordlist WHERE word='$word'";
		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
		pg_free_result($result);
	}
}
?>
