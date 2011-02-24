<?php if (!defined('IN_PHPBB')) exit; $_forumrow_count = (isset($this->_tpldata['forumrow'])) ? sizeof($this->_tpldata['forumrow']) : 0;if ($_forumrow_count) {for ($_forumrow_i = 0; $_forumrow_i < $_forumrow_count; ++$_forumrow_i){$_forumrow_val = &$this->_tpldata['forumrow'][$_forumrow_i]; if (( $_forumrow_val['S_IS_CAT'] && ! $_forumrow_val['S_FIRST_ROW'] ) || $_forumrow_val['S_NO_CAT']) {  ?>

			</ul>

	<?php } if ($_forumrow_val['S_IS_CAT'] || $_forumrow_val['S_FIRST_ROW'] || $_forumrow_val['S_NO_CAT']) {  ?>

			<span class="header"><?php if ($_forumrow_val['S_IS_CAT']) {  echo $_forumrow_val['FORUM_NAME']; } else { echo ((isset($this->_rootref['L_FORUM'])) ? $this->_rootref['L_FORUM'] : ((isset($user->lang['FORUM'])) ? $user->lang['FORUM'] : '{ FORUM }')); } ?></span>
			<ul class="pageitem">
	<?php } if (! $_forumrow_val['S_IS_CAT']) {  ?>

	
	<li class="menu">
<a href="<?php echo $_forumrow_val['U_VIEWFORUM']; ?>"> <img alt="Description" src="<?php echo $_forumrow_val['FORUM_FOLDER_IMG_SRC']; ?>" /> <span class="name"><?php echo $_forumrow_val['FORUM_NAME']; ?></span> <span class="comment"><?php echo $_forumrow_val['TOPICS']; ?> <?php echo ((isset($this->_rootref['L_TOPICS'])) ? $this->_rootref['L_TOPICS'] : ((isset($user->lang['TOPICS'])) ? $user->lang['TOPICS'] : '{ TOPICS }')); ?></span> <span class="arrow"></span> </a>
</li>
	<?php } if ($_forumrow_val['S_LAST_ROW']) {  ?>

</ul>
	<?php } }} else { ?>	
	<ul class="pageitem">
<li class="textbox"> <span class="header"><?php echo ((isset($this->_rootref['L_NO_FORUMS'])) ? $this->_rootref['L_NO_FORUMS'] : ((isset($user->lang['NO_FORUMS'])) ? $user->lang['NO_FORUMS'] : '{ NO_FORUMS }')); ?></span>
</li>
</ul>
<?php } ?>