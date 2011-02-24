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
?>

<body>
<h2>Administration Tools - List of unapproved Words</h2>
<a id="aAdminToolsBackToCatroidweb"
	href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a>
<br />
<br />
<?php if($this->answer) {
	echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
}?>
<div class="projectList">
<table class="projectTable">
	<tr>
		<th>ID</th>
		<th>Word</th>
		<th>Meaning</th>
		<th>Approve</th>
		<th>Delete</th>
	</tr>
	<?php
	if($this->words) {
		foreach($this->words as $word) {?>
	<tr>
		<td><?php echo $word['id']?></td>
		<td><?php echo $word['word']?></td>
		<td>
		<form class="admin" action="approveWords" method="POST"><select name="meaning">
			<option value="-1">-</option>
			<option value="1">good</option>
			<option value="0">bad</option>
		</select>
		</td>
		<td><input type="hidden" name="wordId"
			value="<?php echo $word['id']?>" /> <input type="submit"
			value="approve" name="approve" id="approve<?php echo $word['id']?>" />
		<!-- chg -->
		</form>
		</td>
		<td>
		<form class="admin" action="approveWords" method="POST"><input
			type="hidden" name="wordId" value="<?php echo $word['id']?>" /> <input
			type="submit" value="delete" name="delete"
			id="delete<?php echo $word['id']?>" /> <!-- chg --></form>
		</td>
	</tr>
	<?php }}?>
</table>
</div>
</body>
