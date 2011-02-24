<?php if (!defined('IN_PHPBB')) exit; $this->_tpl_include('overall_header.html'); if ($this->_rootref['S_FORUM_RULES']) {  ?>

			<span class="header"><?php echo ((isset($this->_rootref['L_FORUM_RULES'])) ? $this->_rootref['L_FORUM_RULES'] : ((isset($user->lang['FORUM_RULES'])) ? $user->lang['FORUM_RULES'] : '{ FORUM_RULES }')); ?></span>
			<ul class="pageitem"><li class="textbox">
		<?php if ($this->_rootref['U_FORUM_RULES']) {  ?>

			<a href="<?php echo (isset($this->_rootref['U_FORUM_RULES'])) ? $this->_rootref['U_FORUM_RULES'] : ''; ?>"><b><?php echo ((isset($this->_rootref['L_FORUM_RULES_LINK'])) ? $this->_rootref['L_FORUM_RULES_LINK'] : ((isset($user->lang['FORUM_RULES_LINK'])) ? $user->lang['FORUM_RULES_LINK'] : '{ FORUM_RULES_LINK }')); ?></b></a>
		<?php } else { ?>

			<?php echo (isset($this->_rootref['FORUM_RULES'])) ? $this->_rootref['FORUM_RULES'] : ''; ?>

		<?php } ?>

	</li>
	</ul>
<?php } if ($this->_rootref['S_DISPLAY_ACTIVE']) {  ?>

	<table class="tablebg" width="100%" cellspacing="1">
	<tr>
		<td class="cat" colspan="<?php if ($this->_rootref['S_TOPIC_ICONS']) {  ?>7<?php } else { ?>6<?php } ?>"><span class="nav"><?php echo ((isset($this->_rootref['L_ACTIVE_TOPICS'])) ? $this->_rootref['L_ACTIVE_TOPICS'] : ((isset($user->lang['ACTIVE_TOPICS'])) ? $user->lang['ACTIVE_TOPICS'] : '{ ACTIVE_TOPICS }')); ?></span></td>
	</tr>

	<tr>
		<?php if ($this->_rootref['S_TOPIC_ICONS']) {  ?>

			<th colspan="3">&nbsp;<?php echo ((isset($this->_rootref['L_TOPICS'])) ? $this->_rootref['L_TOPICS'] : ((isset($user->lang['TOPICS'])) ? $user->lang['TOPICS'] : '{ TOPICS }')); ?>&nbsp;</th>
		<?php } else { ?>

			<th colspan="2">&nbsp;<?php echo ((isset($this->_rootref['L_TOPICS'])) ? $this->_rootref['L_TOPICS'] : ((isset($user->lang['TOPICS'])) ? $user->lang['TOPICS'] : '{ TOPICS }')); ?>&nbsp;</th>
		<?php } ?>

		<th>&nbsp;<?php echo ((isset($this->_rootref['L_AUTHOR'])) ? $this->_rootref['L_AUTHOR'] : ((isset($user->lang['AUTHOR'])) ? $user->lang['AUTHOR'] : '{ AUTHOR }')); ?>&nbsp;</th>
		<th>&nbsp;<?php echo ((isset($this->_rootref['L_REPLIES'])) ? $this->_rootref['L_REPLIES'] : ((isset($user->lang['REPLIES'])) ? $user->lang['REPLIES'] : '{ REPLIES }')); ?>&nbsp;</th>
		<th>&nbsp;<?php echo ((isset($this->_rootref['L_VIEWS'])) ? $this->_rootref['L_VIEWS'] : ((isset($user->lang['VIEWS'])) ? $user->lang['VIEWS'] : '{ VIEWS }')); ?>&nbsp;</th>
		<th>&nbsp;<?php echo ((isset($this->_rootref['L_LAST_POST'])) ? $this->_rootref['L_LAST_POST'] : ((isset($user->lang['LAST_POST'])) ? $user->lang['LAST_POST'] : '{ LAST_POST }')); ?>&nbsp;</th>
	</tr>

	<?php $_topicrow_count = (isset($this->_tpldata['topicrow'])) ? sizeof($this->_tpldata['topicrow']) : 0;if ($_topicrow_count) {for ($_topicrow_i = 0; $_topicrow_i < $_topicrow_count; ++$_topicrow_i){$_topicrow_val = &$this->_tpldata['topicrow'][$_topicrow_i]; ?>


		<tr>
			<td class="row1" width="25" align="center"><?php echo $_topicrow_val['TOPIC_FOLDER_IMG']; ?></td>
			<?php if ($this->_rootref['S_TOPIC_ICONS']) {  ?>

				<td class="row1" width="25" align="center"><?php if ($_topicrow_val['TOPIC_ICON_IMG']) {  ?><img src="<?php echo (isset($this->_rootref['T_ICONS_PATH'])) ? $this->_rootref['T_ICONS_PATH'] : ''; echo $_topicrow_val['TOPIC_ICON_IMG']; ?>" width="<?php echo $_topicrow_val['TOPIC_ICON_IMG_WIDTH']; ?>" height="<?php echo $_topicrow_val['TOPIC_ICON_IMG_HEIGHT']; ?>" alt="" title="" /><?php } ?></td>
			<?php } ?>

			<td class="row1">
				<?php if ($_topicrow_val['S_UNREAD_TOPIC']) {  ?><a href="<?php echo $_topicrow_val['U_NEWEST_POST']; ?>"><?php echo (isset($this->_rootref['NEWEST_POST_IMG'])) ? $this->_rootref['NEWEST_POST_IMG'] : ''; ?></a><?php } ?>

				<?php echo $_topicrow_val['ATTACH_ICON_IMG']; ?> <?php if ($_topicrow_val['S_HAS_POLL'] || $_topicrow_val['S_TOPIC_MOVED']) {  ?><b><?php echo $_topicrow_val['TOPIC_TYPE']; ?></b> <?php } ?><a title="<?php echo ((isset($this->_rootref['L_POSTED'])) ? $this->_rootref['L_POSTED'] : ((isset($user->lang['POSTED'])) ? $user->lang['POSTED'] : '{ POSTED }')); ?>: <?php echo $_topicrow_val['FIRST_POST_TIME']; ?>" href="<?php echo $_topicrow_val['U_VIEW_TOPIC']; ?>"class="topictitle"><?php echo $_topicrow_val['TOPIC_TITLE']; ?></a>
				<?php if ($_topicrow_val['S_TOPIC_UNAPPROVED'] || $_topicrow_val['S_POSTS_UNAPPROVED']) {  ?>

					<a href="<?php echo $_topicrow_val['U_MCP_QUEUE']; ?>"><?php echo (isset($this->_rootref['UNAPPROVED_IMG'])) ? $this->_rootref['UNAPPROVED_IMG'] : ''; ?></a>&nbsp;
				<?php } if ($_topicrow_val['S_TOPIC_REPORTED']) {  ?>

					<a href="<?php echo $_topicrow_val['U_MCP_REPORT']; ?>"><?php echo (isset($this->_rootref['REPORTED_IMG'])) ? $this->_rootref['REPORTED_IMG'] : ''; ?></a>&nbsp;
				<?php } if ($_topicrow_val['PAGINATION']) {  ?>

					<p class="gensmall"> [ <?php echo (isset($this->_rootref['GOTO_PAGE_IMG'])) ? $this->_rootref['GOTO_PAGE_IMG'] : ''; echo ((isset($this->_rootref['L_GOTO_PAGE'])) ? $this->_rootref['L_GOTO_PAGE'] : ((isset($user->lang['GOTO_PAGE'])) ? $user->lang['GOTO_PAGE'] : '{ GOTO_PAGE }')); ?>: <?php echo $_topicrow_val['PAGINATION']; ?> ] </p>
				<?php } ?>

			</td>
			<td class="row2" width="130" align="center"><p class="topicauthor"><?php echo $_topicrow_val['TOPIC_AUTHOR_FULL']; ?></p></td>
			<td class="row1" width="50" align="center"><p class="topicdetails"><?php echo $_topicrow_val['REPLIES']; ?></p></td>
			<td class="row2" width="50" align="center"><p class="topicdetails"><?php echo $_topicrow_val['VIEWS']; ?></p></td>
			<td class="row1" width="140" align="center">
				<p class="topicdetails" style="white-space: nowrap;"><?php echo $_topicrow_val['LAST_POST_TIME']; ?></p>
				<p class="topicdetails"><?php echo $_topicrow_val['LAST_POST_AUTHOR_FULL']; ?>

					<?php if (! $this->_rootref['S_IS_BOT']) {  ?><a href="<?php echo $_topicrow_val['U_LAST_POST']; ?>"><?php echo (isset($this->_rootref['LAST_POST_IMG'])) ? $this->_rootref['LAST_POST_IMG'] : ''; ?></a><?php } ?>

				</p>
			</td>
		</tr>

	<?php }} else { ?>


		<tr>
			<?php if ($this->_rootref['S_TOPIC_ICONS']) {  ?>

				<td class="row1" colspan="7" height="30" align="center" valign="middle"><span class="gen"><?php if (! $this->_rootref['S_SORT_DAYS']) {  echo ((isset($this->_rootref['L_NO_TOPICS'])) ? $this->_rootref['L_NO_TOPICS'] : ((isset($user->lang['NO_TOPICS'])) ? $user->lang['NO_TOPICS'] : '{ NO_TOPICS }')); } else { echo ((isset($this->_rootref['L_NO_TOPICS_TIME_FRAME'])) ? $this->_rootref['L_NO_TOPICS_TIME_FRAME'] : ((isset($user->lang['NO_TOPICS_TIME_FRAME'])) ? $user->lang['NO_TOPICS_TIME_FRAME'] : '{ NO_TOPICS_TIME_FRAME }')); } ?></span></td>
			<?php } else { ?>

				<td class="row1" colspan="6" height="30" align="center" valign="middle"><span class="gen"><?php if (! $this->_rootref['S_SORT_DAYS']) {  echo ((isset($this->_rootref['L_NO_TOPICS'])) ? $this->_rootref['L_NO_TOPICS'] : ((isset($user->lang['NO_TOPICS'])) ? $user->lang['NO_TOPICS'] : '{ NO_TOPICS }')); } else { echo ((isset($this->_rootref['L_NO_TOPICS_TIME_FRAME'])) ? $this->_rootref['L_NO_TOPICS_TIME_FRAME'] : ((isset($user->lang['NO_TOPICS_TIME_FRAME'])) ? $user->lang['NO_TOPICS_TIME_FRAME'] : '{ NO_TOPICS_TIME_FRAME }')); } ?></span></td>
			<?php } ?>

		</tr>
	<?php } ?>


	<tr align="center">
		<td class="cat" colspan="<?php if ($this->_rootref['S_TOPIC_ICONS']) {  ?>7<?php } else { ?>6<?php } ?>">&nbsp;</td>
	</tr>
	</table>

	<br clear="all" />
<?php } if ($this->_rootref['S_HAS_SUBFORUM']) {  $this->_tpl_include('forumlist_body.html'); ?>

	<br clear="all" />
<?php } if ($this->_rootref['S_IS_POSTABLE'] || $this->_rootref['S_NO_READ_ACCESS']) {  ?>


		<span class="header"><?php echo (isset($this->_rootref['FORUM_NAME'])) ? $this->_rootref['FORUM_NAME'] : ''; ?></span>

<?php } ?>


<div id="pagecontent">

<?php if ($this->_rootref['S_NO_READ_ACCESS']) {  ?>

	<table class="tablebg" width="100%" cellspacing="1">
	<tr>
		<td class="row1" height="30" align="center" valign="middle"><span class="gen"><?php echo ((isset($this->_rootref['L_NO_READ_ACCESS'])) ? $this->_rootref['L_NO_READ_ACCESS'] : ((isset($user->lang['NO_READ_ACCESS'])) ? $user->lang['NO_READ_ACCESS'] : '{ NO_READ_ACCESS }')); ?></span></td>
	</tr>
	</table>

	<br clear="all" />
<?php } if ($this->_rootref['S_DISPLAY_POST_INFO'] || $this->_rootref['TOTAL_TOPICS']) {  ?>

		<table width="100%" cellspacing="1">
		<tr>
			<?php if ($this->_rootref['TOTAL_TOPICS']) {  ?>

				<td class="nav" valign="middle" nowrap="nowrap">&nbsp;<?php echo (isset($this->_rootref['PAGE_NUMBER'])) ? $this->_rootref['PAGE_NUMBER'] : ''; ?><br /></td>
				<td class="gensmall" nowrap="nowrap">&nbsp;[ <?php echo (isset($this->_rootref['TOTAL_TOPICS'])) ? $this->_rootref['TOTAL_TOPICS'] : ''; ?> ]&nbsp;</td>
				<td class="gensmall" width="100%" align="<?php echo (isset($this->_rootref['S_CONTENT_FLOW_END'])) ? $this->_rootref['S_CONTENT_FLOW_END'] : ''; ?>" nowrap="nowrap"><?php $this->_tpl_include('pagination.html'); ?></td>
			<?php } ?>

		</tr>
		</table>
	<?php } if (! $this->_rootref['S_DISPLAY_ACTIVE'] && ( $this->_rootref['S_IS_POSTABLE'] || sizeof($this->_tpldata['topicrow']) )) {  $_topicrow_count = (isset($this->_tpldata['topicrow'])) ? sizeof($this->_tpldata['topicrow']) : 0;if ($_topicrow_count) {for ($_topicrow_i = 0; $_topicrow_i < $_topicrow_count; ++$_topicrow_i){$_topicrow_val = &$this->_tpldata['topicrow'][$_topicrow_i]; if ($_topicrow_val['S_TOPIC_TYPE_SWITCH'] == (1)) {  ?>

				<span class="header"><?php echo ((isset($this->_rootref['L_ANNOUNCEMENTS'])) ? $this->_rootref['L_ANNOUNCEMENTS'] : ((isset($user->lang['ANNOUNCEMENTS'])) ? $user->lang['ANNOUNCEMENTS'] : '{ ANNOUNCEMENTS }')); ?></span>
			<?php } else if ($_topicrow_val['S_TOPIC_TYPE_SWITCH'] == 0) {  ?>

			</ul>
				<span class="header"><?php echo ((isset($this->_rootref['L_TOPICS'])) ? $this->_rootref['L_TOPICS'] : ((isset($user->lang['TOPICS'])) ? $user->lang['TOPICS'] : '{ TOPICS }')); ?></span>
				<ul class="pageitem">
			<?php } if ($_topicrow_val['S_FIRST_ROW']) {  ?><ul class="pageitem"><?php } ?>

	<li class="menu">
	<a href="<?php echo $_topicrow_val['U_VIEW_TOPIC']; ?>"><?php echo $_topicrow_val['TOPIC_FOLDER_IMG']; ?> <span class="name"><?php echo $_topicrow_val['TOPIC_TITLE']; ?></span> <span class="comment"><?php echo $_topicrow_val['REPLIES']; ?> <?php echo ((isset($this->_rootref['L_REPLIES'])) ? $this->_rootref['L_REPLIES'] : ((isset($user->lang['REPLIES'])) ? $user->lang['REPLIES'] : '{ REPLIES }')); ?></span> <span class="arrow"></span> </a>
	</li>
<?php if ($_topicrow_val['S_LAST_ROW']) {  ?></ul><?php } }} else { if ($this->_rootref['S_IS_POSTABLE']) {  ?>

			<tr>
				<?php if ($this->_rootref['S_TOPIC_ICONS']) {  ?>

					<td class="row1" colspan="7" height="30" align="center" valign="middle"><span class="gen"><?php if (! $this->_rootref['S_SORT_DAYS']) {  echo ((isset($this->_rootref['L_NO_TOPICS'])) ? $this->_rootref['L_NO_TOPICS'] : ((isset($user->lang['NO_TOPICS'])) ? $user->lang['NO_TOPICS'] : '{ NO_TOPICS }')); } else { echo ((isset($this->_rootref['L_NO_TOPICS_TIME_FRAME'])) ? $this->_rootref['L_NO_TOPICS_TIME_FRAME'] : ((isset($user->lang['NO_TOPICS_TIME_FRAME'])) ? $user->lang['NO_TOPICS_TIME_FRAME'] : '{ NO_TOPICS_TIME_FRAME }')); } ?></span></td>
				<?php } else { ?>

					<td class="row1" colspan="6" height="30" align="center" valign="middle"><span class="gen"><?php if (! $this->_rootref['S_SORT_DAYS']) {  echo ((isset($this->_rootref['L_NO_TOPICS'])) ? $this->_rootref['L_NO_TOPICS'] : ((isset($user->lang['NO_TOPICS'])) ? $user->lang['NO_TOPICS'] : '{ NO_TOPICS }')); } else { echo ((isset($this->_rootref['L_NO_TOPICS_TIME_FRAME'])) ? $this->_rootref['L_NO_TOPICS_TIME_FRAME'] : ((isset($user->lang['NO_TOPICS_TIME_FRAME'])) ? $user->lang['NO_TOPICS_TIME_FRAME'] : '{ NO_TOPICS_TIME_FRAME }')); } ?></span></td>
				<?php } ?>

			</tr>
			<?php } } ?>

		</div>
	<?php } if ($this->_rootref['S_DISPLAY_ONLINE_LIST']) {  ?>

<br />
		<span class="header"><?php echo ((isset($this->_rootref['L_WHO_IS_ONLINE'])) ? $this->_rootref['L_WHO_IS_ONLINE'] : ((isset($user->lang['WHO_IS_ONLINE'])) ? $user->lang['WHO_IS_ONLINE'] : '{ WHO_IS_ONLINE }')); ?></span>

		<ul class="pageitem"><li class="textbox"><?php echo (isset($this->_rootref['LOGGED_IN_USER_LIST'])) ? $this->_rootref['LOGGED_IN_USER_LIST'] : ''; ?></li></ul>
<?php } $this->_tpl_include('overall_footer.html'); ?>