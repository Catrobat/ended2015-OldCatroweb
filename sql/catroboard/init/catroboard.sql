/*

 $Id$

*/

BEGIN;

/*
	Domain definition
*/
CREATE DOMAIN varchar_ci AS varchar(255) NOT NULL DEFAULT ''::character varying;

/*
	Operation Functions
*/
CREATE FUNCTION _varchar_ci_equal(varchar_ci, varchar_ci) RETURNS boolean AS 'SELECT LOWER($1) = LOWER($2)' LANGUAGE SQL STRICT;
CREATE FUNCTION _varchar_ci_not_equal(varchar_ci, varchar_ci) RETURNS boolean AS 'SELECT LOWER($1) != LOWER($2)' LANGUAGE SQL STRICT;
CREATE FUNCTION _varchar_ci_less_than(varchar_ci, varchar_ci) RETURNS boolean AS 'SELECT LOWER($1) < LOWER($2)' LANGUAGE SQL STRICT;
CREATE FUNCTION _varchar_ci_less_equal(varchar_ci, varchar_ci) RETURNS boolean AS 'SELECT LOWER($1) <= LOWER($2)' LANGUAGE SQL STRICT;
CREATE FUNCTION _varchar_ci_greater_than(varchar_ci, varchar_ci) RETURNS boolean AS 'SELECT LOWER($1) > LOWER($2)' LANGUAGE SQL STRICT;
CREATE FUNCTION _varchar_ci_greater_equals(varchar_ci, varchar_ci) RETURNS boolean AS 'SELECT LOWER($1) >= LOWER($2)' LANGUAGE SQL STRICT;

/*
	Operators
*/
CREATE OPERATOR <(
  PROCEDURE = _varchar_ci_less_than,
  LEFTARG = varchar_ci,
  RIGHTARG = varchar_ci,
  COMMUTATOR = >,
  NEGATOR = >=,
  RESTRICT = scalarltsel,
  JOIN = scalarltjoinsel);

CREATE OPERATOR <=(
  PROCEDURE = _varchar_ci_less_equal,
  LEFTARG = varchar_ci,
  RIGHTARG = varchar_ci,
  COMMUTATOR = >=,
  NEGATOR = >,
  RESTRICT = scalarltsel,
  JOIN = scalarltjoinsel);

CREATE OPERATOR >(
  PROCEDURE = _varchar_ci_greater_than,
  LEFTARG = varchar_ci,
  RIGHTARG = varchar_ci,
  COMMUTATOR = <,
  NEGATOR = <=,
  RESTRICT = scalargtsel,
  JOIN = scalargtjoinsel);

CREATE OPERATOR >=(
  PROCEDURE = _varchar_ci_greater_equals,
  LEFTARG = varchar_ci,
  RIGHTARG = varchar_ci,
  COMMUTATOR = <=,
  NEGATOR = <,
  RESTRICT = scalargtsel,
  JOIN = scalargtjoinsel);

CREATE OPERATOR <>(
  PROCEDURE = _varchar_ci_not_equal,
  LEFTARG = varchar_ci,
  RIGHTARG = varchar_ci,
  COMMUTATOR = <>,
  NEGATOR = =,
  RESTRICT = neqsel,
  JOIN = neqjoinsel);

CREATE OPERATOR =(
  PROCEDURE = _varchar_ci_equal,
  LEFTARG = varchar_ci,
  RIGHTARG = varchar_ci,
  COMMUTATOR = =,
  NEGATOR = <>,
  RESTRICT = eqsel,
  JOIN = eqjoinsel,
  HASHES,
  MERGES,
  SORT1= <);

/*
	Table: 'phpbb_attachments'
*/
CREATE SEQUENCE phpbb_attachments_seq;

CREATE TABLE phpbb_attachments (
	attach_id INT4 DEFAULT nextval('phpbb_attachments_seq'),
	post_msg_id INT4 DEFAULT '0' NOT NULL CHECK (post_msg_id >= 0),
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	in_message INT2 DEFAULT '0' NOT NULL CHECK (in_message >= 0),
	poster_id INT4 DEFAULT '0' NOT NULL CHECK (poster_id >= 0),
	is_orphan INT2 DEFAULT '1' NOT NULL CHECK (is_orphan >= 0),
	physical_filename varchar(255) DEFAULT '' NOT NULL,
	real_filename varchar(255) DEFAULT '' NOT NULL,
	download_count INT4 DEFAULT '0' NOT NULL CHECK (download_count >= 0),
	attach_comment varchar(4000) DEFAULT '' NOT NULL,
	extension varchar(100) DEFAULT '' NOT NULL,
	mimetype varchar(100) DEFAULT '' NOT NULL,
	filesize INT4 DEFAULT '0' NOT NULL CHECK (filesize >= 0),
	filetime INT4 DEFAULT '0' NOT NULL CHECK (filetime >= 0),
	thumbnail INT2 DEFAULT '0' NOT NULL CHECK (thumbnail >= 0),
	PRIMARY KEY (attach_id)
);

CREATE INDEX phpbb_attachments_filetime ON phpbb_attachments (filetime);
CREATE INDEX phpbb_attachments_post_msg_id ON phpbb_attachments (post_msg_id);
CREATE INDEX phpbb_attachments_topic_id ON phpbb_attachments (topic_id);
CREATE INDEX phpbb_attachments_poster_id ON phpbb_attachments (poster_id);
CREATE INDEX phpbb_attachments_is_orphan ON phpbb_attachments (is_orphan);

/*
	Table: 'phpbb_acl_groups'
*/
CREATE TABLE phpbb_acl_groups (
	group_id INT4 DEFAULT '0' NOT NULL CHECK (group_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	auth_option_id INT4 DEFAULT '0' NOT NULL CHECK (auth_option_id >= 0),
	auth_role_id INT4 DEFAULT '0' NOT NULL CHECK (auth_role_id >= 0),
	auth_setting INT2 DEFAULT '0' NOT NULL
);

CREATE INDEX phpbb_acl_groups_group_id ON phpbb_acl_groups (group_id);
CREATE INDEX phpbb_acl_groups_auth_opt_id ON phpbb_acl_groups (auth_option_id);
CREATE INDEX phpbb_acl_groups_auth_role_id ON phpbb_acl_groups (auth_role_id);

/*
	Table: 'phpbb_acl_options'
*/
CREATE SEQUENCE phpbb_acl_options_seq;

CREATE TABLE phpbb_acl_options (
	auth_option_id INT4 DEFAULT nextval('phpbb_acl_options_seq'),
	auth_option varchar(50) DEFAULT '' NOT NULL,
	is_global INT2 DEFAULT '0' NOT NULL CHECK (is_global >= 0),
	is_local INT2 DEFAULT '0' NOT NULL CHECK (is_local >= 0),
	founder_only INT2 DEFAULT '0' NOT NULL CHECK (founder_only >= 0),
	PRIMARY KEY (auth_option_id)
);

CREATE UNIQUE INDEX phpbb_acl_options_auth_option ON phpbb_acl_options (auth_option);

/*
	Table: 'phpbb_acl_roles'
*/
CREATE SEQUENCE phpbb_acl_roles_seq;

CREATE TABLE phpbb_acl_roles (
	role_id INT4 DEFAULT nextval('phpbb_acl_roles_seq'),
	role_name varchar(255) DEFAULT '' NOT NULL,
	role_description varchar(4000) DEFAULT '' NOT NULL,
	role_type varchar(10) DEFAULT '' NOT NULL,
	role_order INT2 DEFAULT '0' NOT NULL CHECK (role_order >= 0),
	PRIMARY KEY (role_id)
);

CREATE INDEX phpbb_acl_roles_role_type ON phpbb_acl_roles (role_type);
CREATE INDEX phpbb_acl_roles_role_order ON phpbb_acl_roles (role_order);

/*
	Table: 'phpbb_acl_roles_data'
*/
CREATE TABLE phpbb_acl_roles_data (
	role_id INT4 DEFAULT '0' NOT NULL CHECK (role_id >= 0),
	auth_option_id INT4 DEFAULT '0' NOT NULL CHECK (auth_option_id >= 0),
	auth_setting INT2 DEFAULT '0' NOT NULL,
	PRIMARY KEY (role_id, auth_option_id)
);

CREATE INDEX phpbb_acl_roles_data_ath_op_id ON phpbb_acl_roles_data (auth_option_id);

/*
	Table: 'phpbb_acl_users'
*/
CREATE TABLE phpbb_acl_users (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	auth_option_id INT4 DEFAULT '0' NOT NULL CHECK (auth_option_id >= 0),
	auth_role_id INT4 DEFAULT '0' NOT NULL CHECK (auth_role_id >= 0),
	auth_setting INT2 DEFAULT '0' NOT NULL
);

CREATE INDEX phpbb_acl_users_user_id ON phpbb_acl_users (user_id);
CREATE INDEX phpbb_acl_users_auth_option_id ON phpbb_acl_users (auth_option_id);
CREATE INDEX phpbb_acl_users_auth_role_id ON phpbb_acl_users (auth_role_id);

/*
	Table: 'phpbb_banlist'
*/
CREATE SEQUENCE phpbb_banlist_seq;

CREATE TABLE phpbb_banlist (
	ban_id INT4 DEFAULT nextval('phpbb_banlist_seq'),
	ban_userid INT4 DEFAULT '0' NOT NULL CHECK (ban_userid >= 0),
	ban_ip varchar(40) DEFAULT '' NOT NULL,
	ban_email varchar(100) DEFAULT '' NOT NULL,
	ban_start INT4 DEFAULT '0' NOT NULL CHECK (ban_start >= 0),
	ban_end INT4 DEFAULT '0' NOT NULL CHECK (ban_end >= 0),
	ban_exclude INT2 DEFAULT '0' NOT NULL CHECK (ban_exclude >= 0),
	ban_reason varchar(255) DEFAULT '' NOT NULL,
	ban_give_reason varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (ban_id)
);

CREATE INDEX phpbb_banlist_ban_end ON phpbb_banlist (ban_end);
CREATE INDEX phpbb_banlist_ban_user ON phpbb_banlist (ban_userid, ban_exclude);
CREATE INDEX phpbb_banlist_ban_email ON phpbb_banlist (ban_email, ban_exclude);
CREATE INDEX phpbb_banlist_ban_ip ON phpbb_banlist (ban_ip, ban_exclude);

/*
	Table: 'phpbb_bbcodes'
*/
CREATE TABLE phpbb_bbcodes (
	bbcode_id INT2 DEFAULT '0' NOT NULL,
	bbcode_tag varchar(16) DEFAULT '' NOT NULL,
	bbcode_helpline varchar(255) DEFAULT '' NOT NULL,
	display_on_posting INT2 DEFAULT '0' NOT NULL CHECK (display_on_posting >= 0),
	bbcode_match varchar(4000) DEFAULT '' NOT NULL,
	bbcode_tpl TEXT DEFAULT '' NOT NULL,
	first_pass_match TEXT DEFAULT '' NOT NULL,
	first_pass_replace TEXT DEFAULT '' NOT NULL,
	second_pass_match TEXT DEFAULT '' NOT NULL,
	second_pass_replace TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (bbcode_id)
);

CREATE INDEX phpbb_bbcodes_display_on_post ON phpbb_bbcodes (display_on_posting);

/*
	Table: 'phpbb_bookmarks'
*/
CREATE TABLE phpbb_bookmarks (
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	PRIMARY KEY (topic_id, user_id)
);


/*
	Table: 'phpbb_bots'
*/
CREATE SEQUENCE phpbb_bots_seq;

CREATE TABLE phpbb_bots (
	bot_id INT4 DEFAULT nextval('phpbb_bots_seq'),
	bot_active INT2 DEFAULT '1' NOT NULL CHECK (bot_active >= 0),
	bot_name varchar(255) DEFAULT '' NOT NULL,
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	bot_agent varchar(255) DEFAULT '' NOT NULL,
	bot_ip varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (bot_id)
);

CREATE INDEX phpbb_bots_bot_active ON phpbb_bots (bot_active);

/*
	Table: 'phpbb_config'
*/
CREATE TABLE phpbb_config (
	config_name varchar(255) DEFAULT '' NOT NULL,
	config_value varchar(255) DEFAULT '' NOT NULL,
	is_dynamic INT2 DEFAULT '0' NOT NULL CHECK (is_dynamic >= 0),
	PRIMARY KEY (config_name)
);

CREATE INDEX phpbb_config_is_dynamic ON phpbb_config (is_dynamic);

/*
	Table: 'phpbb_confirm'
*/
CREATE TABLE phpbb_confirm (
	confirm_id char(32) DEFAULT '' NOT NULL,
	session_id char(32) DEFAULT '' NOT NULL,
	confirm_type INT2 DEFAULT '0' NOT NULL,
	code varchar(8) DEFAULT '' NOT NULL,
	seed INT4 DEFAULT '0' NOT NULL CHECK (seed >= 0),
	attempts INT4 DEFAULT '0' NOT NULL CHECK (attempts >= 0),
	PRIMARY KEY (session_id, confirm_id)
);

CREATE INDEX phpbb_confirm_confirm_type ON phpbb_confirm (confirm_type);

/*
	Table: 'phpbb_disallow'
*/
CREATE SEQUENCE phpbb_disallow_seq;

CREATE TABLE phpbb_disallow (
	disallow_id INT4 DEFAULT nextval('phpbb_disallow_seq'),
	disallow_username varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (disallow_id)
);


/*
	Table: 'phpbb_drafts'
*/
CREATE SEQUENCE phpbb_drafts_seq;

CREATE TABLE phpbb_drafts (
	draft_id INT4 DEFAULT nextval('phpbb_drafts_seq'),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	save_time INT4 DEFAULT '0' NOT NULL CHECK (save_time >= 0),
	draft_subject varchar(255) DEFAULT '' NOT NULL,
	draft_message TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (draft_id)
);

CREATE INDEX phpbb_drafts_save_time ON phpbb_drafts (save_time);

/*
	Table: 'phpbb_extensions'
*/
CREATE SEQUENCE phpbb_extensions_seq;

CREATE TABLE phpbb_extensions (
	extension_id INT4 DEFAULT nextval('phpbb_extensions_seq'),
	group_id INT4 DEFAULT '0' NOT NULL CHECK (group_id >= 0),
	extension varchar(100) DEFAULT '' NOT NULL,
	PRIMARY KEY (extension_id)
);


/*
	Table: 'phpbb_extension_groups'
*/
CREATE SEQUENCE phpbb_extension_groups_seq;

CREATE TABLE phpbb_extension_groups (
	group_id INT4 DEFAULT nextval('phpbb_extension_groups_seq'),
	group_name varchar(255) DEFAULT '' NOT NULL,
	cat_id INT2 DEFAULT '0' NOT NULL,
	allow_group INT2 DEFAULT '0' NOT NULL CHECK (allow_group >= 0),
	download_mode INT2 DEFAULT '1' NOT NULL CHECK (download_mode >= 0),
	upload_icon varchar(255) DEFAULT '' NOT NULL,
	max_filesize INT4 DEFAULT '0' NOT NULL CHECK (max_filesize >= 0),
	allowed_forums varchar(8000) DEFAULT '' NOT NULL,
	allow_in_pm INT2 DEFAULT '0' NOT NULL CHECK (allow_in_pm >= 0),
	PRIMARY KEY (group_id)
);


/*
	Table: 'phpbb_forums'
*/
CREATE SEQUENCE phpbb_forums_seq;

CREATE TABLE phpbb_forums (
	forum_id INT4 DEFAULT nextval('phpbb_forums_seq'),
	parent_id INT4 DEFAULT '0' NOT NULL CHECK (parent_id >= 0),
	left_id INT4 DEFAULT '0' NOT NULL CHECK (left_id >= 0),
	right_id INT4 DEFAULT '0' NOT NULL CHECK (right_id >= 0),
	forum_parents TEXT DEFAULT '' NOT NULL,
	forum_name varchar(255) DEFAULT '' NOT NULL,
	forum_desc varchar(4000) DEFAULT '' NOT NULL,
	forum_desc_bitfield varchar(255) DEFAULT '' NOT NULL,
	forum_desc_options INT4 DEFAULT '7' NOT NULL CHECK (forum_desc_options >= 0),
	forum_desc_uid varchar(8) DEFAULT '' NOT NULL,
	forum_link varchar(255) DEFAULT '' NOT NULL,
	forum_password varchar(40) DEFAULT '' NOT NULL,
	forum_style INT4 DEFAULT '0' NOT NULL CHECK (forum_style >= 0),
	forum_image varchar(255) DEFAULT '' NOT NULL,
	forum_rules varchar(4000) DEFAULT '' NOT NULL,
	forum_rules_link varchar(255) DEFAULT '' NOT NULL,
	forum_rules_bitfield varchar(255) DEFAULT '' NOT NULL,
	forum_rules_options INT4 DEFAULT '7' NOT NULL CHECK (forum_rules_options >= 0),
	forum_rules_uid varchar(8) DEFAULT '' NOT NULL,
	forum_topics_per_page INT2 DEFAULT '0' NOT NULL,
	forum_type INT2 DEFAULT '0' NOT NULL,
	forum_status INT2 DEFAULT '0' NOT NULL,
	forum_posts INT4 DEFAULT '0' NOT NULL CHECK (forum_posts >= 0),
	forum_topics INT4 DEFAULT '0' NOT NULL CHECK (forum_topics >= 0),
	forum_topics_real INT4 DEFAULT '0' NOT NULL CHECK (forum_topics_real >= 0),
	forum_last_post_id INT4 DEFAULT '0' NOT NULL CHECK (forum_last_post_id >= 0),
	forum_last_poster_id INT4 DEFAULT '0' NOT NULL CHECK (forum_last_poster_id >= 0),
	forum_last_post_subject varchar(255) DEFAULT '' NOT NULL,
	forum_last_post_time INT4 DEFAULT '0' NOT NULL CHECK (forum_last_post_time >= 0),
	forum_last_poster_name varchar(255) DEFAULT '' NOT NULL,
	forum_last_poster_colour varchar(6) DEFAULT '' NOT NULL,
	forum_flags INT2 DEFAULT '32' NOT NULL,
	forum_options INT4 DEFAULT '0' NOT NULL CHECK (forum_options >= 0),
	display_subforum_list INT2 DEFAULT '1' NOT NULL CHECK (display_subforum_list >= 0),
	display_on_index INT2 DEFAULT '1' NOT NULL CHECK (display_on_index >= 0),
	enable_indexing INT2 DEFAULT '1' NOT NULL CHECK (enable_indexing >= 0),
	enable_icons INT2 DEFAULT '1' NOT NULL CHECK (enable_icons >= 0),
	enable_prune INT2 DEFAULT '0' NOT NULL CHECK (enable_prune >= 0),
	prune_next INT4 DEFAULT '0' NOT NULL CHECK (prune_next >= 0),
	prune_days INT4 DEFAULT '0' NOT NULL CHECK (prune_days >= 0),
	prune_viewed INT4 DEFAULT '0' NOT NULL CHECK (prune_viewed >= 0),
	prune_freq INT4 DEFAULT '0' NOT NULL CHECK (prune_freq >= 0),
	PRIMARY KEY (forum_id)
);

CREATE INDEX phpbb_forums_left_right_id ON phpbb_forums (left_id, right_id);
CREATE INDEX phpbb_forums_forum_lastpost_id ON phpbb_forums (forum_last_post_id);

/*
	Table: 'phpbb_forums_access'
*/
CREATE TABLE phpbb_forums_access (
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	session_id char(32) DEFAULT '' NOT NULL,
	PRIMARY KEY (forum_id, user_id, session_id)
);


/*
	Table: 'phpbb_forums_track'
*/
CREATE TABLE phpbb_forums_track (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	mark_time INT4 DEFAULT '0' NOT NULL CHECK (mark_time >= 0),
	PRIMARY KEY (user_id, forum_id)
);


/*
	Table: 'phpbb_forums_watch'
*/
CREATE TABLE phpbb_forums_watch (
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	notify_status INT2 DEFAULT '0' NOT NULL CHECK (notify_status >= 0)
);

CREATE INDEX phpbb_forums_watch_forum_id ON phpbb_forums_watch (forum_id);
CREATE INDEX phpbb_forums_watch_user_id ON phpbb_forums_watch (user_id);
CREATE INDEX phpbb_forums_watch_notify_stat ON phpbb_forums_watch (notify_status);

/*
	Table: 'phpbb_groups'
*/
CREATE SEQUENCE phpbb_groups_seq;

CREATE TABLE phpbb_groups (
	group_id INT4 DEFAULT nextval('phpbb_groups_seq'),
	group_type INT2 DEFAULT '1' NOT NULL,
	group_founder_manage INT2 DEFAULT '0' NOT NULL CHECK (group_founder_manage >= 0),
	group_skip_auth INT2 DEFAULT '0' NOT NULL CHECK (group_skip_auth >= 0),
	group_name varchar_ci DEFAULT '' NOT NULL,
	group_desc varchar(4000) DEFAULT '' NOT NULL,
	group_desc_bitfield varchar(255) DEFAULT '' NOT NULL,
	group_desc_options INT4 DEFAULT '7' NOT NULL CHECK (group_desc_options >= 0),
	group_desc_uid varchar(8) DEFAULT '' NOT NULL,
	group_display INT2 DEFAULT '0' NOT NULL CHECK (group_display >= 0),
	group_avatar varchar(255) DEFAULT '' NOT NULL,
	group_avatar_type INT2 DEFAULT '0' NOT NULL,
	group_avatar_width INT2 DEFAULT '0' NOT NULL CHECK (group_avatar_width >= 0),
	group_avatar_height INT2 DEFAULT '0' NOT NULL CHECK (group_avatar_height >= 0),
	group_rank INT4 DEFAULT '0' NOT NULL CHECK (group_rank >= 0),
	group_colour varchar(6) DEFAULT '' NOT NULL,
	group_sig_chars INT4 DEFAULT '0' NOT NULL CHECK (group_sig_chars >= 0),
	group_receive_pm INT2 DEFAULT '0' NOT NULL CHECK (group_receive_pm >= 0),
	group_message_limit INT4 DEFAULT '0' NOT NULL CHECK (group_message_limit >= 0),
	group_max_recipients INT4 DEFAULT '0' NOT NULL CHECK (group_max_recipients >= 0),
	group_legend INT2 DEFAULT '1' NOT NULL CHECK (group_legend >= 0),
	PRIMARY KEY (group_id)
);

CREATE INDEX phpbb_groups_group_legend_name ON phpbb_groups (group_legend, group_name);

/*
	Table: 'phpbb_icons'
*/
CREATE SEQUENCE phpbb_icons_seq;

CREATE TABLE phpbb_icons (
	icons_id INT4 DEFAULT nextval('phpbb_icons_seq'),
	icons_url varchar(255) DEFAULT '' NOT NULL,
	icons_width INT2 DEFAULT '0' NOT NULL,
	icons_height INT2 DEFAULT '0' NOT NULL,
	icons_order INT4 DEFAULT '0' NOT NULL CHECK (icons_order >= 0),
	display_on_posting INT2 DEFAULT '1' NOT NULL CHECK (display_on_posting >= 0),
	PRIMARY KEY (icons_id)
);

CREATE INDEX phpbb_icons_display_on_posting ON phpbb_icons (display_on_posting);

/*
	Table: 'phpbb_lang'
*/
CREATE SEQUENCE phpbb_lang_seq;

CREATE TABLE phpbb_lang (
	lang_id INT2 DEFAULT nextval('phpbb_lang_seq'),
	lang_iso varchar(30) DEFAULT '' NOT NULL,
	lang_dir varchar(30) DEFAULT '' NOT NULL,
	lang_english_name varchar(100) DEFAULT '' NOT NULL,
	lang_local_name varchar(255) DEFAULT '' NOT NULL,
	lang_author varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (lang_id)
);

CREATE INDEX phpbb_lang_lang_iso ON phpbb_lang (lang_iso);

/*
	Table: 'phpbb_log'
*/
CREATE SEQUENCE phpbb_log_seq;

CREATE TABLE phpbb_log (
	log_id INT4 DEFAULT nextval('phpbb_log_seq'),
	log_type INT2 DEFAULT '0' NOT NULL,
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	reportee_id INT4 DEFAULT '0' NOT NULL CHECK (reportee_id >= 0),
	log_ip varchar(40) DEFAULT '' NOT NULL,
	log_time INT4 DEFAULT '0' NOT NULL CHECK (log_time >= 0),
	log_operation varchar(4000) DEFAULT '' NOT NULL,
	log_data TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (log_id)
);

CREATE INDEX phpbb_log_log_type ON phpbb_log (log_type);
CREATE INDEX phpbb_log_forum_id ON phpbb_log (forum_id);
CREATE INDEX phpbb_log_topic_id ON phpbb_log (topic_id);
CREATE INDEX phpbb_log_reportee_id ON phpbb_log (reportee_id);
CREATE INDEX phpbb_log_user_id ON phpbb_log (user_id);

/*
	Table: 'phpbb_moderator_cache'
*/
CREATE TABLE phpbb_moderator_cache (
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	username varchar(255) DEFAULT '' NOT NULL,
	group_id INT4 DEFAULT '0' NOT NULL CHECK (group_id >= 0),
	group_name varchar(255) DEFAULT '' NOT NULL,
	display_on_index INT2 DEFAULT '1' NOT NULL CHECK (display_on_index >= 0)
);

CREATE INDEX phpbb_moderator_cache_disp_idx ON phpbb_moderator_cache (display_on_index);
CREATE INDEX phpbb_moderator_cache_forum_id ON phpbb_moderator_cache (forum_id);

/*
	Table: 'phpbb_modules'
*/
CREATE SEQUENCE phpbb_modules_seq;

CREATE TABLE phpbb_modules (
	module_id INT4 DEFAULT nextval('phpbb_modules_seq'),
	module_enabled INT2 DEFAULT '1' NOT NULL CHECK (module_enabled >= 0),
	module_display INT2 DEFAULT '1' NOT NULL CHECK (module_display >= 0),
	module_basename varchar(255) DEFAULT '' NOT NULL,
	module_class varchar(10) DEFAULT '' NOT NULL,
	parent_id INT4 DEFAULT '0' NOT NULL CHECK (parent_id >= 0),
	left_id INT4 DEFAULT '0' NOT NULL CHECK (left_id >= 0),
	right_id INT4 DEFAULT '0' NOT NULL CHECK (right_id >= 0),
	module_langname varchar(255) DEFAULT '' NOT NULL,
	module_mode varchar(255) DEFAULT '' NOT NULL,
	module_auth varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (module_id)
);

CREATE INDEX phpbb_modules_left_right_id ON phpbb_modules (left_id, right_id);
CREATE INDEX phpbb_modules_module_enabled ON phpbb_modules (module_enabled);
CREATE INDEX phpbb_modules_class_left_id ON phpbb_modules (module_class, left_id);

/*
	Table: 'phpbb_poll_options'
*/
CREATE TABLE phpbb_poll_options (
	poll_option_id INT2 DEFAULT '0' NOT NULL,
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	poll_option_text varchar(4000) DEFAULT '' NOT NULL,
	poll_option_total INT4 DEFAULT '0' NOT NULL CHECK (poll_option_total >= 0)
);

CREATE INDEX phpbb_poll_options_poll_opt_id ON phpbb_poll_options (poll_option_id);
CREATE INDEX phpbb_poll_options_topic_id ON phpbb_poll_options (topic_id);

/*
	Table: 'phpbb_poll_votes'
*/
CREATE TABLE phpbb_poll_votes (
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	poll_option_id INT2 DEFAULT '0' NOT NULL,
	vote_user_id INT4 DEFAULT '0' NOT NULL CHECK (vote_user_id >= 0),
	vote_user_ip varchar(40) DEFAULT '' NOT NULL
);

CREATE INDEX phpbb_poll_votes_topic_id ON phpbb_poll_votes (topic_id);
CREATE INDEX phpbb_poll_votes_vote_user_id ON phpbb_poll_votes (vote_user_id);
CREATE INDEX phpbb_poll_votes_vote_user_ip ON phpbb_poll_votes (vote_user_ip);

/*
	Table: 'phpbb_posts'
*/
CREATE SEQUENCE phpbb_posts_seq;

CREATE TABLE phpbb_posts (
	post_id INT4 DEFAULT nextval('phpbb_posts_seq'),
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	poster_id INT4 DEFAULT '0' NOT NULL CHECK (poster_id >= 0),
	icon_id INT4 DEFAULT '0' NOT NULL CHECK (icon_id >= 0),
	poster_ip varchar(40) DEFAULT '' NOT NULL,
	post_time INT4 DEFAULT '0' NOT NULL CHECK (post_time >= 0),
	post_approved INT2 DEFAULT '1' NOT NULL CHECK (post_approved >= 0),
	post_reported INT2 DEFAULT '0' NOT NULL CHECK (post_reported >= 0),
	enable_bbcode INT2 DEFAULT '1' NOT NULL CHECK (enable_bbcode >= 0),
	enable_smilies INT2 DEFAULT '1' NOT NULL CHECK (enable_smilies >= 0),
	enable_magic_url INT2 DEFAULT '1' NOT NULL CHECK (enable_magic_url >= 0),
	enable_sig INT2 DEFAULT '1' NOT NULL CHECK (enable_sig >= 0),
	post_username varchar(255) DEFAULT '' NOT NULL,
	post_subject varchar(255) DEFAULT '' NOT NULL,
	post_text TEXT DEFAULT '' NOT NULL,
	post_checksum varchar(32) DEFAULT '' NOT NULL,
	post_attachment INT2 DEFAULT '0' NOT NULL CHECK (post_attachment >= 0),
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	post_postcount INT2 DEFAULT '1' NOT NULL CHECK (post_postcount >= 0),
	post_edit_time INT4 DEFAULT '0' NOT NULL CHECK (post_edit_time >= 0),
	post_edit_reason varchar(255) DEFAULT '' NOT NULL,
	post_edit_user INT4 DEFAULT '0' NOT NULL CHECK (post_edit_user >= 0),
	post_edit_count INT2 DEFAULT '0' NOT NULL CHECK (post_edit_count >= 0),
	post_edit_locked INT2 DEFAULT '0' NOT NULL CHECK (post_edit_locked >= 0),
	PRIMARY KEY (post_id)
);

CREATE INDEX phpbb_posts_forum_id ON phpbb_posts (forum_id);
CREATE INDEX phpbb_posts_topic_id ON phpbb_posts (topic_id);
CREATE INDEX phpbb_posts_poster_ip ON phpbb_posts (poster_ip);
CREATE INDEX phpbb_posts_poster_id ON phpbb_posts (poster_id);
CREATE INDEX phpbb_posts_post_approved ON phpbb_posts (post_approved);
CREATE INDEX phpbb_posts_post_username ON phpbb_posts (post_username);
CREATE INDEX phpbb_posts_tid_post_time ON phpbb_posts (topic_id, post_time);

/*
	Table: 'phpbb_privmsgs'
*/
CREATE SEQUENCE phpbb_privmsgs_seq;

CREATE TABLE phpbb_privmsgs (
	msg_id INT4 DEFAULT nextval('phpbb_privmsgs_seq'),
	root_level INT4 DEFAULT '0' NOT NULL CHECK (root_level >= 0),
	author_id INT4 DEFAULT '0' NOT NULL CHECK (author_id >= 0),
	icon_id INT4 DEFAULT '0' NOT NULL CHECK (icon_id >= 0),
	author_ip varchar(40) DEFAULT '' NOT NULL,
	message_time INT4 DEFAULT '0' NOT NULL CHECK (message_time >= 0),
	enable_bbcode INT2 DEFAULT '1' NOT NULL CHECK (enable_bbcode >= 0),
	enable_smilies INT2 DEFAULT '1' NOT NULL CHECK (enable_smilies >= 0),
	enable_magic_url INT2 DEFAULT '1' NOT NULL CHECK (enable_magic_url >= 0),
	enable_sig INT2 DEFAULT '1' NOT NULL CHECK (enable_sig >= 0),
	message_subject varchar(255) DEFAULT '' NOT NULL,
	message_text TEXT DEFAULT '' NOT NULL,
	message_edit_reason varchar(255) DEFAULT '' NOT NULL,
	message_edit_user INT4 DEFAULT '0' NOT NULL CHECK (message_edit_user >= 0),
	message_attachment INT2 DEFAULT '0' NOT NULL CHECK (message_attachment >= 0),
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	message_edit_time INT4 DEFAULT '0' NOT NULL CHECK (message_edit_time >= 0),
	message_edit_count INT2 DEFAULT '0' NOT NULL CHECK (message_edit_count >= 0),
	to_address varchar(4000) DEFAULT '' NOT NULL,
	bcc_address varchar(4000) DEFAULT '' NOT NULL,
	message_reported INT2 DEFAULT '0' NOT NULL CHECK (message_reported >= 0),
	PRIMARY KEY (msg_id)
);

CREATE INDEX phpbb_privmsgs_author_ip ON phpbb_privmsgs (author_ip);
CREATE INDEX phpbb_privmsgs_message_time ON phpbb_privmsgs (message_time);
CREATE INDEX phpbb_privmsgs_author_id ON phpbb_privmsgs (author_id);
CREATE INDEX phpbb_privmsgs_root_level ON phpbb_privmsgs (root_level);

/*
	Table: 'phpbb_privmsgs_folder'
*/
CREATE SEQUENCE phpbb_privmsgs_folder_seq;

CREATE TABLE phpbb_privmsgs_folder (
	folder_id INT4 DEFAULT nextval('phpbb_privmsgs_folder_seq'),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	folder_name varchar(255) DEFAULT '' NOT NULL,
	pm_count INT4 DEFAULT '0' NOT NULL CHECK (pm_count >= 0),
	PRIMARY KEY (folder_id)
);

CREATE INDEX phpbb_privmsgs_folder_user_id ON phpbb_privmsgs_folder (user_id);

/*
	Table: 'phpbb_privmsgs_rules'
*/
CREATE SEQUENCE phpbb_privmsgs_rules_seq;

CREATE TABLE phpbb_privmsgs_rules (
	rule_id INT4 DEFAULT nextval('phpbb_privmsgs_rules_seq'),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	rule_check INT4 DEFAULT '0' NOT NULL CHECK (rule_check >= 0),
	rule_connection INT4 DEFAULT '0' NOT NULL CHECK (rule_connection >= 0),
	rule_string varchar(255) DEFAULT '' NOT NULL,
	rule_user_id INT4 DEFAULT '0' NOT NULL CHECK (rule_user_id >= 0),
	rule_group_id INT4 DEFAULT '0' NOT NULL CHECK (rule_group_id >= 0),
	rule_action INT4 DEFAULT '0' NOT NULL CHECK (rule_action >= 0),
	rule_folder_id INT4 DEFAULT '0' NOT NULL,
	PRIMARY KEY (rule_id)
);

CREATE INDEX phpbb_privmsgs_rules_user_id ON phpbb_privmsgs_rules (user_id);

/*
	Table: 'phpbb_privmsgs_to'
*/
CREATE TABLE phpbb_privmsgs_to (
	msg_id INT4 DEFAULT '0' NOT NULL CHECK (msg_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	author_id INT4 DEFAULT '0' NOT NULL CHECK (author_id >= 0),
	pm_deleted INT2 DEFAULT '0' NOT NULL CHECK (pm_deleted >= 0),
	pm_new INT2 DEFAULT '1' NOT NULL CHECK (pm_new >= 0),
	pm_unread INT2 DEFAULT '1' NOT NULL CHECK (pm_unread >= 0),
	pm_replied INT2 DEFAULT '0' NOT NULL CHECK (pm_replied >= 0),
	pm_marked INT2 DEFAULT '0' NOT NULL CHECK (pm_marked >= 0),
	pm_forwarded INT2 DEFAULT '0' NOT NULL CHECK (pm_forwarded >= 0),
	folder_id INT4 DEFAULT '0' NOT NULL
);

CREATE INDEX phpbb_privmsgs_to_msg_id ON phpbb_privmsgs_to (msg_id);
CREATE INDEX phpbb_privmsgs_to_author_id ON phpbb_privmsgs_to (author_id);
CREATE INDEX phpbb_privmsgs_to_usr_flder_id ON phpbb_privmsgs_to (user_id, folder_id);

/*
	Table: 'phpbb_profile_fields'
*/
CREATE SEQUENCE phpbb_profile_fields_seq;

CREATE TABLE phpbb_profile_fields (
	field_id INT4 DEFAULT nextval('phpbb_profile_fields_seq'),
	field_name varchar(255) DEFAULT '' NOT NULL,
	field_type INT2 DEFAULT '0' NOT NULL,
	field_ident varchar(20) DEFAULT '' NOT NULL,
	field_length varchar(20) DEFAULT '' NOT NULL,
	field_minlen varchar(255) DEFAULT '' NOT NULL,
	field_maxlen varchar(255) DEFAULT '' NOT NULL,
	field_novalue varchar(255) DEFAULT '' NOT NULL,
	field_default_value varchar(255) DEFAULT '' NOT NULL,
	field_validation varchar(20) DEFAULT '' NOT NULL,
	field_required INT2 DEFAULT '0' NOT NULL CHECK (field_required >= 0),
	field_show_on_reg INT2 DEFAULT '0' NOT NULL CHECK (field_show_on_reg >= 0),
	field_show_on_vt INT2 DEFAULT '0' NOT NULL CHECK (field_show_on_vt >= 0),
	field_show_profile INT2 DEFAULT '0' NOT NULL CHECK (field_show_profile >= 0),
	field_hide INT2 DEFAULT '0' NOT NULL CHECK (field_hide >= 0),
	field_no_view INT2 DEFAULT '0' NOT NULL CHECK (field_no_view >= 0),
	field_active INT2 DEFAULT '0' NOT NULL CHECK (field_active >= 0),
	field_order INT4 DEFAULT '0' NOT NULL CHECK (field_order >= 0),
	PRIMARY KEY (field_id)
);

CREATE INDEX phpbb_profile_fields_fld_type ON phpbb_profile_fields (field_type);
CREATE INDEX phpbb_profile_fields_fld_ordr ON phpbb_profile_fields (field_order);

/*
	Table: 'phpbb_profile_fields_data'
*/
CREATE TABLE phpbb_profile_fields_data (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	PRIMARY KEY (user_id)
);


/*
	Table: 'phpbb_profile_fields_lang'
*/
CREATE TABLE phpbb_profile_fields_lang (
	field_id INT4 DEFAULT '0' NOT NULL CHECK (field_id >= 0),
	lang_id INT4 DEFAULT '0' NOT NULL CHECK (lang_id >= 0),
	option_id INT4 DEFAULT '0' NOT NULL CHECK (option_id >= 0),
	field_type INT2 DEFAULT '0' NOT NULL,
	lang_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (field_id, lang_id, option_id)
);


/*
	Table: 'phpbb_profile_lang'
*/
CREATE TABLE phpbb_profile_lang (
	field_id INT4 DEFAULT '0' NOT NULL CHECK (field_id >= 0),
	lang_id INT4 DEFAULT '0' NOT NULL CHECK (lang_id >= 0),
	lang_name varchar(255) DEFAULT '' NOT NULL,
	lang_explain varchar(4000) DEFAULT '' NOT NULL,
	lang_default_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (field_id, lang_id)
);


/*
	Table: 'phpbb_ranks'
*/
CREATE SEQUENCE phpbb_ranks_seq;

CREATE TABLE phpbb_ranks (
	rank_id INT4 DEFAULT nextval('phpbb_ranks_seq'),
	rank_title varchar(255) DEFAULT '' NOT NULL,
	rank_min INT4 DEFAULT '0' NOT NULL CHECK (rank_min >= 0),
	rank_special INT2 DEFAULT '0' NOT NULL CHECK (rank_special >= 0),
	rank_image varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (rank_id)
);


/*
	Table: 'phpbb_reports'
*/
CREATE SEQUENCE phpbb_reports_seq;

CREATE TABLE phpbb_reports (
	report_id INT4 DEFAULT nextval('phpbb_reports_seq'),
	reason_id INT2 DEFAULT '0' NOT NULL CHECK (reason_id >= 0),
	post_id INT4 DEFAULT '0' NOT NULL CHECK (post_id >= 0),
	pm_id INT4 DEFAULT '0' NOT NULL CHECK (pm_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	user_notify INT2 DEFAULT '0' NOT NULL CHECK (user_notify >= 0),
	report_closed INT2 DEFAULT '0' NOT NULL CHECK (report_closed >= 0),
	report_time INT4 DEFAULT '0' NOT NULL CHECK (report_time >= 0),
	report_text TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (report_id)
);

CREATE INDEX phpbb_reports_post_id ON phpbb_reports (post_id);
CREATE INDEX phpbb_reports_pm_id ON phpbb_reports (pm_id);

/*
	Table: 'phpbb_reports_reasons'
*/
CREATE SEQUENCE phpbb_reports_reasons_seq;

CREATE TABLE phpbb_reports_reasons (
	reason_id INT2 DEFAULT nextval('phpbb_reports_reasons_seq'),
	reason_title varchar(255) DEFAULT '' NOT NULL,
	reason_description TEXT DEFAULT '' NOT NULL,
	reason_order INT2 DEFAULT '0' NOT NULL CHECK (reason_order >= 0),
	PRIMARY KEY (reason_id)
);


/*
	Table: 'phpbb_search_results'
*/
CREATE TABLE phpbb_search_results (
	search_key varchar(32) DEFAULT '' NOT NULL,
	search_time INT4 DEFAULT '0' NOT NULL CHECK (search_time >= 0),
	search_keywords TEXT DEFAULT '' NOT NULL,
	search_authors TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (search_key)
);


/*
	Table: 'phpbb_search_wordlist'
*/
CREATE SEQUENCE phpbb_search_wordlist_seq;

CREATE TABLE phpbb_search_wordlist (
	word_id INT4 DEFAULT nextval('phpbb_search_wordlist_seq'),
	word_text varchar(255) DEFAULT '' NOT NULL,
	word_common INT2 DEFAULT '0' NOT NULL CHECK (word_common >= 0),
	word_count INT4 DEFAULT '0' NOT NULL CHECK (word_count >= 0),
	PRIMARY KEY (word_id)
);

CREATE UNIQUE INDEX phpbb_search_wordlist_wrd_txt ON phpbb_search_wordlist (word_text);
CREATE INDEX phpbb_search_wordlist_wrd_cnt ON phpbb_search_wordlist (word_count);

/*
	Table: 'phpbb_search_wordmatch'
*/
CREATE TABLE phpbb_search_wordmatch (
	post_id INT4 DEFAULT '0' NOT NULL CHECK (post_id >= 0),
	word_id INT4 DEFAULT '0' NOT NULL CHECK (word_id >= 0),
	title_match INT2 DEFAULT '0' NOT NULL CHECK (title_match >= 0)
);

CREATE UNIQUE INDEX phpbb_search_wordmatch_unq_mtch ON phpbb_search_wordmatch (word_id, post_id, title_match);
CREATE INDEX phpbb_search_wordmatch_word_id ON phpbb_search_wordmatch (word_id);
CREATE INDEX phpbb_search_wordmatch_post_id ON phpbb_search_wordmatch (post_id);

/*
	Table: 'phpbb_sessions'
*/
CREATE TABLE phpbb_sessions (
	session_id char(32) DEFAULT '' NOT NULL,
	session_user_id INT4 DEFAULT '0' NOT NULL CHECK (session_user_id >= 0),
	session_forum_id INT4 DEFAULT '0' NOT NULL CHECK (session_forum_id >= 0),
	session_last_visit INT4 DEFAULT '0' NOT NULL CHECK (session_last_visit >= 0),
	session_start INT4 DEFAULT '0' NOT NULL CHECK (session_start >= 0),
	session_time INT4 DEFAULT '0' NOT NULL CHECK (session_time >= 0),
	session_ip varchar(40) DEFAULT '' NOT NULL,
	session_browser varchar(150) DEFAULT '' NOT NULL,
	session_forwarded_for varchar(255) DEFAULT '' NOT NULL,
	session_page varchar(255) DEFAULT '' NOT NULL,
	session_viewonline INT2 DEFAULT '1' NOT NULL CHECK (session_viewonline >= 0),
	session_autologin INT2 DEFAULT '0' NOT NULL CHECK (session_autologin >= 0),
	session_admin INT2 DEFAULT '0' NOT NULL CHECK (session_admin >= 0),
	PRIMARY KEY (session_id)
);

CREATE INDEX phpbb_sessions_session_time ON phpbb_sessions (session_time);
CREATE INDEX phpbb_sessions_session_user_id ON phpbb_sessions (session_user_id);
CREATE INDEX phpbb_sessions_session_fid ON phpbb_sessions (session_forum_id);

/*
	Table: 'phpbb_sessions_keys'
*/
CREATE TABLE phpbb_sessions_keys (
	key_id char(32) DEFAULT '' NOT NULL,
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	last_ip varchar(40) DEFAULT '' NOT NULL,
	last_login INT4 DEFAULT '0' NOT NULL CHECK (last_login >= 0),
	PRIMARY KEY (key_id, user_id)
);

CREATE INDEX phpbb_sessions_keys_last_login ON phpbb_sessions_keys (last_login);

/*
	Table: 'phpbb_sitelist'
*/
CREATE SEQUENCE phpbb_sitelist_seq;

CREATE TABLE phpbb_sitelist (
	site_id INT4 DEFAULT nextval('phpbb_sitelist_seq'),
	site_ip varchar(40) DEFAULT '' NOT NULL,
	site_hostname varchar(255) DEFAULT '' NOT NULL,
	ip_exclude INT2 DEFAULT '0' NOT NULL CHECK (ip_exclude >= 0),
	PRIMARY KEY (site_id)
);


/*
	Table: 'phpbb_smilies'
*/
CREATE SEQUENCE phpbb_smilies_seq;

CREATE TABLE phpbb_smilies (
	smiley_id INT4 DEFAULT nextval('phpbb_smilies_seq'),
	code varchar(50) DEFAULT '' NOT NULL,
	emotion varchar(50) DEFAULT '' NOT NULL,
	smiley_url varchar(50) DEFAULT '' NOT NULL,
	smiley_width INT2 DEFAULT '0' NOT NULL CHECK (smiley_width >= 0),
	smiley_height INT2 DEFAULT '0' NOT NULL CHECK (smiley_height >= 0),
	smiley_order INT4 DEFAULT '0' NOT NULL CHECK (smiley_order >= 0),
	display_on_posting INT2 DEFAULT '1' NOT NULL CHECK (display_on_posting >= 0),
	PRIMARY KEY (smiley_id)
);

CREATE INDEX phpbb_smilies_display_on_post ON phpbb_smilies (display_on_posting);

/*
	Table: 'phpbb_styles'
*/
CREATE SEQUENCE phpbb_styles_seq;

CREATE TABLE phpbb_styles (
	style_id INT4 DEFAULT nextval('phpbb_styles_seq'),
	style_name varchar(255) DEFAULT '' NOT NULL,
	style_copyright varchar(255) DEFAULT '' NOT NULL,
	style_active INT2 DEFAULT '1' NOT NULL CHECK (style_active >= 0),
	template_id INT4 DEFAULT '0' NOT NULL CHECK (template_id >= 0),
	theme_id INT4 DEFAULT '0' NOT NULL CHECK (theme_id >= 0),
	imageset_id INT4 DEFAULT '0' NOT NULL CHECK (imageset_id >= 0),
	PRIMARY KEY (style_id)
);

CREATE UNIQUE INDEX phpbb_styles_style_name ON phpbb_styles (style_name);
CREATE INDEX phpbb_styles_template_id ON phpbb_styles (template_id);
CREATE INDEX phpbb_styles_theme_id ON phpbb_styles (theme_id);
CREATE INDEX phpbb_styles_imageset_id ON phpbb_styles (imageset_id);

/*
	Table: 'phpbb_styles_template'
*/
CREATE SEQUENCE phpbb_styles_template_seq;

CREATE TABLE phpbb_styles_template (
	template_id INT4 DEFAULT nextval('phpbb_styles_template_seq'),
	template_name varchar(255) DEFAULT '' NOT NULL,
	template_copyright varchar(255) DEFAULT '' NOT NULL,
	template_path varchar(100) DEFAULT '' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT 'kNg=' NOT NULL,
	template_storedb INT2 DEFAULT '0' NOT NULL CHECK (template_storedb >= 0),
	template_inherits_id INT4 DEFAULT '0' NOT NULL CHECK (template_inherits_id >= 0),
	template_inherit_path varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (template_id)
);

CREATE UNIQUE INDEX phpbb_styles_template_tmplte_nm ON phpbb_styles_template (template_name);

/*
	Table: 'phpbb_styles_template_data'
*/
CREATE TABLE phpbb_styles_template_data (
	template_id INT4 DEFAULT '0' NOT NULL CHECK (template_id >= 0),
	template_filename varchar(100) DEFAULT '' NOT NULL,
	template_included varchar(8000) DEFAULT '' NOT NULL,
	template_mtime INT4 DEFAULT '0' NOT NULL CHECK (template_mtime >= 0),
	template_data TEXT DEFAULT '' NOT NULL
);

CREATE INDEX phpbb_styles_template_data_tid ON phpbb_styles_template_data (template_id);
CREATE INDEX phpbb_styles_template_data_tfn ON phpbb_styles_template_data (template_filename);

/*
	Table: 'phpbb_styles_theme'
*/
CREATE SEQUENCE phpbb_styles_theme_seq;

CREATE TABLE phpbb_styles_theme (
	theme_id INT4 DEFAULT nextval('phpbb_styles_theme_seq'),
	theme_name varchar(255) DEFAULT '' NOT NULL,
	theme_copyright varchar(255) DEFAULT '' NOT NULL,
	theme_path varchar(100) DEFAULT '' NOT NULL,
	theme_storedb INT2 DEFAULT '0' NOT NULL CHECK (theme_storedb >= 0),
	theme_mtime INT4 DEFAULT '0' NOT NULL CHECK (theme_mtime >= 0),
	theme_data TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (theme_id)
);

CREATE UNIQUE INDEX phpbb_styles_theme_theme_name ON phpbb_styles_theme (theme_name);

/*
	Table: 'phpbb_styles_imageset'
*/
CREATE SEQUENCE phpbb_styles_imageset_seq;

CREATE TABLE phpbb_styles_imageset (
	imageset_id INT4 DEFAULT nextval('phpbb_styles_imageset_seq'),
	imageset_name varchar(255) DEFAULT '' NOT NULL,
	imageset_copyright varchar(255) DEFAULT '' NOT NULL,
	imageset_path varchar(100) DEFAULT '' NOT NULL,
	PRIMARY KEY (imageset_id)
);

CREATE UNIQUE INDEX phpbb_styles_imageset_imgset_nm ON phpbb_styles_imageset (imageset_name);

/*
	Table: 'phpbb_styles_imageset_data'
*/
CREATE SEQUENCE phpbb_styles_imageset_data_seq;

CREATE TABLE phpbb_styles_imageset_data (
	image_id INT4 DEFAULT nextval('phpbb_styles_imageset_data_seq'),
	image_name varchar(200) DEFAULT '' NOT NULL,
	image_filename varchar(200) DEFAULT '' NOT NULL,
	image_lang varchar(30) DEFAULT '' NOT NULL,
	image_height INT2 DEFAULT '0' NOT NULL CHECK (image_height >= 0),
	image_width INT2 DEFAULT '0' NOT NULL CHECK (image_width >= 0),
	imageset_id INT4 DEFAULT '0' NOT NULL CHECK (imageset_id >= 0),
	PRIMARY KEY (image_id)
);

CREATE INDEX phpbb_styles_imageset_data_i_d ON phpbb_styles_imageset_data (imageset_id);

/*
	Table: 'phpbb_topics'
*/
CREATE SEQUENCE phpbb_topics_seq;

CREATE TABLE phpbb_topics (
	topic_id INT4 DEFAULT nextval('phpbb_topics_seq'),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	icon_id INT4 DEFAULT '0' NOT NULL CHECK (icon_id >= 0),
	topic_attachment INT2 DEFAULT '0' NOT NULL CHECK (topic_attachment >= 0),
	topic_approved INT2 DEFAULT '1' NOT NULL CHECK (topic_approved >= 0),
	topic_reported INT2 DEFAULT '0' NOT NULL CHECK (topic_reported >= 0),
	topic_title varchar(255) DEFAULT '' NOT NULL,
	topic_poster INT4 DEFAULT '0' NOT NULL CHECK (topic_poster >= 0),
	topic_time INT4 DEFAULT '0' NOT NULL CHECK (topic_time >= 0),
	topic_time_limit INT4 DEFAULT '0' NOT NULL CHECK (topic_time_limit >= 0),
	topic_views INT4 DEFAULT '0' NOT NULL CHECK (topic_views >= 0),
	topic_replies INT4 DEFAULT '0' NOT NULL CHECK (topic_replies >= 0),
	topic_replies_real INT4 DEFAULT '0' NOT NULL CHECK (topic_replies_real >= 0),
	topic_status INT2 DEFAULT '0' NOT NULL,
	topic_type INT2 DEFAULT '0' NOT NULL,
	topic_first_post_id INT4 DEFAULT '0' NOT NULL CHECK (topic_first_post_id >= 0),
	topic_first_poster_name varchar(255) DEFAULT '' NOT NULL,
	topic_first_poster_colour varchar(6) DEFAULT '' NOT NULL,
	topic_last_post_id INT4 DEFAULT '0' NOT NULL CHECK (topic_last_post_id >= 0),
	topic_last_poster_id INT4 DEFAULT '0' NOT NULL CHECK (topic_last_poster_id >= 0),
	topic_last_poster_name varchar(255) DEFAULT '' NOT NULL,
	topic_last_poster_colour varchar(6) DEFAULT '' NOT NULL,
	topic_last_post_subject varchar(255) DEFAULT '' NOT NULL,
	topic_last_post_time INT4 DEFAULT '0' NOT NULL CHECK (topic_last_post_time >= 0),
	topic_last_view_time INT4 DEFAULT '0' NOT NULL CHECK (topic_last_view_time >= 0),
	topic_moved_id INT4 DEFAULT '0' NOT NULL CHECK (topic_moved_id >= 0),
	topic_bumped INT2 DEFAULT '0' NOT NULL CHECK (topic_bumped >= 0),
	topic_bumper INT4 DEFAULT '0' NOT NULL CHECK (topic_bumper >= 0),
	poll_title varchar(255) DEFAULT '' NOT NULL,
	poll_start INT4 DEFAULT '0' NOT NULL CHECK (poll_start >= 0),
	poll_length INT4 DEFAULT '0' NOT NULL CHECK (poll_length >= 0),
	poll_max_options INT2 DEFAULT '1' NOT NULL,
	poll_last_vote INT4 DEFAULT '0' NOT NULL CHECK (poll_last_vote >= 0),
	poll_vote_change INT2 DEFAULT '0' NOT NULL CHECK (poll_vote_change >= 0),
	PRIMARY KEY (topic_id)
);

CREATE INDEX phpbb_topics_forum_id ON phpbb_topics (forum_id);
CREATE INDEX phpbb_topics_forum_id_type ON phpbb_topics (forum_id, topic_type);
CREATE INDEX phpbb_topics_last_post_time ON phpbb_topics (topic_last_post_time);
CREATE INDEX phpbb_topics_topic_approved ON phpbb_topics (topic_approved);
CREATE INDEX phpbb_topics_forum_appr_last ON phpbb_topics (forum_id, topic_approved, topic_last_post_id);
CREATE INDEX phpbb_topics_fid_time_moved ON phpbb_topics (forum_id, topic_last_post_time, topic_moved_id);

/*
	Table: 'phpbb_topics_track'
*/
CREATE TABLE phpbb_topics_track (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	forum_id INT4 DEFAULT '0' NOT NULL CHECK (forum_id >= 0),
	mark_time INT4 DEFAULT '0' NOT NULL CHECK (mark_time >= 0),
	PRIMARY KEY (user_id, topic_id)
);

CREATE INDEX phpbb_topics_track_topic_id ON phpbb_topics_track (topic_id);
CREATE INDEX phpbb_topics_track_forum_id ON phpbb_topics_track (forum_id);

/*
	Table: 'phpbb_topics_posted'
*/
CREATE TABLE phpbb_topics_posted (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	topic_posted INT2 DEFAULT '0' NOT NULL CHECK (topic_posted >= 0),
	PRIMARY KEY (user_id, topic_id)
);


/*
	Table: 'phpbb_topics_watch'
*/
CREATE TABLE phpbb_topics_watch (
	topic_id INT4 DEFAULT '0' NOT NULL CHECK (topic_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	notify_status INT2 DEFAULT '0' NOT NULL CHECK (notify_status >= 0)
);

CREATE INDEX phpbb_topics_watch_topic_id ON phpbb_topics_watch (topic_id);
CREATE INDEX phpbb_topics_watch_user_id ON phpbb_topics_watch (user_id);
CREATE INDEX phpbb_topics_watch_notify_stat ON phpbb_topics_watch (notify_status);

/*
	Table: 'phpbb_user_group'
*/
CREATE TABLE phpbb_user_group (
	group_id INT4 DEFAULT '0' NOT NULL CHECK (group_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	group_leader INT2 DEFAULT '0' NOT NULL CHECK (group_leader >= 0),
	user_pending INT2 DEFAULT '1' NOT NULL CHECK (user_pending >= 0)
);

CREATE INDEX phpbb_user_group_group_id ON phpbb_user_group (group_id);
CREATE INDEX phpbb_user_group_user_id ON phpbb_user_group (user_id);
CREATE INDEX phpbb_user_group_group_leader ON phpbb_user_group (group_leader);

/*
	Table: 'phpbb_users'
*/
CREATE SEQUENCE phpbb_users_seq;

CREATE TABLE phpbb_users (
	user_id INT4 DEFAULT nextval('phpbb_users_seq'),
	user_type INT2 DEFAULT '0' NOT NULL,
	group_id INT4 DEFAULT '3' NOT NULL CHECK (group_id >= 0),
	user_permissions TEXT DEFAULT '' NOT NULL,
	user_perm_from INT4 DEFAULT '0' NOT NULL CHECK (user_perm_from >= 0),
	user_ip varchar(40) DEFAULT '' NOT NULL,
	user_regdate INT4 DEFAULT '0' NOT NULL CHECK (user_regdate >= 0),
	username varchar_ci DEFAULT '' NOT NULL,
	username_clean varchar_ci DEFAULT '' NOT NULL,
	user_password varchar(40) DEFAULT '' NOT NULL,
	user_passchg INT4 DEFAULT '0' NOT NULL CHECK (user_passchg >= 0),
	user_pass_convert INT2 DEFAULT '0' NOT NULL CHECK (user_pass_convert >= 0),
	user_email varchar(100) DEFAULT '' NOT NULL,
	user_email_hash INT8 DEFAULT '0' NOT NULL,
	user_birthday varchar(10) DEFAULT '' NOT NULL,
	user_lastvisit INT4 DEFAULT '0' NOT NULL CHECK (user_lastvisit >= 0),
	user_lastmark INT4 DEFAULT '0' NOT NULL CHECK (user_lastmark >= 0),
	user_lastpost_time INT4 DEFAULT '0' NOT NULL CHECK (user_lastpost_time >= 0),
	user_lastpage varchar(200) DEFAULT '' NOT NULL,
	user_last_confirm_key varchar(10) DEFAULT '' NOT NULL,
	user_last_search INT4 DEFAULT '0' NOT NULL CHECK (user_last_search >= 0),
	user_warnings INT2 DEFAULT '0' NOT NULL,
	user_last_warning INT4 DEFAULT '0' NOT NULL CHECK (user_last_warning >= 0),
	user_login_attempts INT2 DEFAULT '0' NOT NULL,
	user_inactive_reason INT2 DEFAULT '0' NOT NULL,
	user_inactive_time INT4 DEFAULT '0' NOT NULL CHECK (user_inactive_time >= 0),
	user_posts INT4 DEFAULT '0' NOT NULL CHECK (user_posts >= 0),
	user_lang varchar(30) DEFAULT '' NOT NULL,
	user_timezone decimal(5,2) DEFAULT '0' NOT NULL,
	user_dst INT2 DEFAULT '0' NOT NULL CHECK (user_dst >= 0),
	user_dateformat varchar(30) DEFAULT 'd M Y H:i' NOT NULL,
	user_style INT4 DEFAULT '0' NOT NULL CHECK (user_style >= 0),
	user_rank INT4 DEFAULT '0' NOT NULL CHECK (user_rank >= 0),
	user_colour varchar(6) DEFAULT '' NOT NULL,
	user_new_privmsg INT4 DEFAULT '0' NOT NULL,
	user_unread_privmsg INT4 DEFAULT '0' NOT NULL,
	user_last_privmsg INT4 DEFAULT '0' NOT NULL CHECK (user_last_privmsg >= 0),
	user_message_rules INT2 DEFAULT '0' NOT NULL CHECK (user_message_rules >= 0),
	user_full_folder INT4 DEFAULT '-3' NOT NULL,
	user_emailtime INT4 DEFAULT '0' NOT NULL CHECK (user_emailtime >= 0),
	user_topic_show_days INT2 DEFAULT '0' NOT NULL CHECK (user_topic_show_days >= 0),
	user_topic_sortby_type varchar(1) DEFAULT 't' NOT NULL,
	user_topic_sortby_dir varchar(1) DEFAULT 'd' NOT NULL,
	user_post_show_days INT2 DEFAULT '0' NOT NULL CHECK (user_post_show_days >= 0),
	user_post_sortby_type varchar(1) DEFAULT 't' NOT NULL,
	user_post_sortby_dir varchar(1) DEFAULT 'a' NOT NULL,
	user_notify INT2 DEFAULT '0' NOT NULL CHECK (user_notify >= 0),
	user_notify_pm INT2 DEFAULT '1' NOT NULL CHECK (user_notify_pm >= 0),
	user_notify_type INT2 DEFAULT '0' NOT NULL,
	user_allow_pm INT2 DEFAULT '1' NOT NULL CHECK (user_allow_pm >= 0),
	user_allow_viewonline INT2 DEFAULT '1' NOT NULL CHECK (user_allow_viewonline >= 0),
	user_allow_viewemail INT2 DEFAULT '1' NOT NULL CHECK (user_allow_viewemail >= 0),
	user_allow_massemail INT2 DEFAULT '1' NOT NULL CHECK (user_allow_massemail >= 0),
	user_options INT4 DEFAULT '230271' NOT NULL CHECK (user_options >= 0),
	user_avatar varchar(255) DEFAULT '' NOT NULL,
	user_avatar_type INT2 DEFAULT '0' NOT NULL,
	user_avatar_width INT2 DEFAULT '0' NOT NULL CHECK (user_avatar_width >= 0),
	user_avatar_height INT2 DEFAULT '0' NOT NULL CHECK (user_avatar_height >= 0),
	user_sig TEXT DEFAULT '' NOT NULL,
	user_sig_bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	user_sig_bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	user_from varchar(100) DEFAULT '' NOT NULL,
	user_icq varchar(15) DEFAULT '' NOT NULL,
	user_aim varchar(255) DEFAULT '' NOT NULL,
	user_yim varchar(255) DEFAULT '' NOT NULL,
	user_msnm varchar(255) DEFAULT '' NOT NULL,
	user_jabber varchar(255) DEFAULT '' NOT NULL,
	user_website varchar(200) DEFAULT '' NOT NULL,
	user_occ varchar(4000) DEFAULT '' NOT NULL,
	user_interests varchar(4000) DEFAULT '' NOT NULL,
	user_actkey varchar(32) DEFAULT '' NOT NULL,
	user_newpasswd varchar(40) DEFAULT '' NOT NULL,
	user_form_salt varchar(32) DEFAULT '' NOT NULL,
	user_new INT2 DEFAULT '1' NOT NULL CHECK (user_new >= 0),
	user_reminded INT2 DEFAULT '0' NOT NULL,
	user_reminded_time INT4 DEFAULT '0' NOT NULL CHECK (user_reminded_time >= 0),
	PRIMARY KEY (user_id)
);

CREATE INDEX phpbb_users_user_birthday ON phpbb_users (user_birthday);
CREATE INDEX phpbb_users_user_email_hash ON phpbb_users (user_email_hash);
CREATE INDEX phpbb_users_user_type ON phpbb_users (user_type);
CREATE UNIQUE INDEX phpbb_users_username_clean ON phpbb_users (username_clean);

/*
	Table: 'phpbb_warnings'
*/
CREATE SEQUENCE phpbb_warnings_seq;

CREATE TABLE phpbb_warnings (
	warning_id INT4 DEFAULT nextval('phpbb_warnings_seq'),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	post_id INT4 DEFAULT '0' NOT NULL CHECK (post_id >= 0),
	log_id INT4 DEFAULT '0' NOT NULL CHECK (log_id >= 0),
	warning_time INT4 DEFAULT '0' NOT NULL CHECK (warning_time >= 0),
	PRIMARY KEY (warning_id)
);


/*
	Table: 'phpbb_words'
*/
CREATE SEQUENCE phpbb_words_seq;

CREATE TABLE phpbb_words (
	word_id INT4 DEFAULT nextval('phpbb_words_seq'),
	word varchar(255) DEFAULT '' NOT NULL,
	replacement varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (word_id)
);


/*
	Table: 'phpbb_zebra'
*/
CREATE TABLE phpbb_zebra (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	zebra_id INT4 DEFAULT '0' NOT NULL CHECK (zebra_id >= 0),
	friend INT2 DEFAULT '0' NOT NULL CHECK (friend >= 0),
	foe INT2 DEFAULT '0' NOT NULL CHECK (foe >= 0),
	PRIMARY KEY (user_id, zebra_id)
);



COMMIT;

-- ----------------------------
-- Records of phpbb_acl_groups
-- ----------------------------
INSERT INTO "phpbb_acl_groups" VALUES ('1', '0', '85', '0', '1');
INSERT INTO "phpbb_acl_groups" VALUES ('1', '0', '93', '0', '1');
INSERT INTO "phpbb_acl_groups" VALUES ('1', '0', '111', '0', '1');
INSERT INTO "phpbb_acl_groups" VALUES ('1', '0', '119', '0', '1');
INSERT INTO "phpbb_acl_groups" VALUES ('1', '1', '0', '17', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('1', '2', '0', '17', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('2', '0', '0', '6', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('2', '1', '0', '17', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('2', '2', '0', '15', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('3', '0', '0', '6', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('3', '1', '0', '17', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('3', '2', '0', '15', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('4', '0', '0', '5', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('4', '0', '0', '10', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('4', '2', '0', '21', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('5', '0', '0', '1', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('5', '0', '0', '5', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('5', '2', '0', '10', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('5', '2', '0', '14', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('6', '1', '0', '17', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('6', '2', '0', '19', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('7', '0', '0', '23', '0');
INSERT INTO "phpbb_acl_groups" VALUES ('7', '2', '0', '24', '0');

-- ----------------------------
-- Records of phpbb_acl_options
-- ----------------------------
INSERT INTO "phpbb_acl_options" VALUES ('1', 'f_', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('2', 'f_announce', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('3', 'f_attach', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('4', 'f_bbcode', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('5', 'f_bump', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('6', 'f_delete', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('7', 'f_download', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('8', 'f_edit', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('9', 'f_email', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('10', 'f_flash', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('11', 'f_icons', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('12', 'f_ignoreflood', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('13', 'f_img', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('14', 'f_list', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('15', 'f_noapprove', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('16', 'f_poll', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('17', 'f_post', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('18', 'f_postcount', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('19', 'f_print', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('20', 'f_read', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('21', 'f_reply', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('22', 'f_report', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('23', 'f_search', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('24', 'f_sigs', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('25', 'f_smilies', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('26', 'f_sticky', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('27', 'f_subscribe', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('28', 'f_user_lock', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('29', 'f_vote', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('30', 'f_votechg', '0', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('31', 'm_', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('32', 'm_approve', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('33', 'm_chgposter', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('34', 'm_delete', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('35', 'm_edit', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('36', 'm_info', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('37', 'm_lock', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('38', 'm_merge', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('39', 'm_move', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('40', 'm_report', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('41', 'm_split', '1', '1', '0');
INSERT INTO "phpbb_acl_options" VALUES ('42', 'm_ban', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('43', 'm_warn', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('44', 'a_', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('45', 'a_aauth', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('46', 'a_attach', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('47', 'a_authgroups', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('48', 'a_authusers', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('49', 'a_backup', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('50', 'a_ban', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('51', 'a_bbcode', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('52', 'a_board', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('53', 'a_bots', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('54', 'a_clearlogs', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('55', 'a_email', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('56', 'a_fauth', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('57', 'a_forum', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('58', 'a_forumadd', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('59', 'a_forumdel', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('60', 'a_group', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('61', 'a_groupadd', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('62', 'a_groupdel', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('63', 'a_icons', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('64', 'a_jabber', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('65', 'a_language', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('66', 'a_mauth', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('67', 'a_modules', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('68', 'a_names', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('69', 'a_phpinfo', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('70', 'a_profile', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('71', 'a_prune', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('72', 'a_ranks', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('73', 'a_reasons', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('74', 'a_roles', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('75', 'a_search', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('76', 'a_server', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('77', 'a_styles', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('78', 'a_switchperm', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('79', 'a_uauth', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('80', 'a_user', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('81', 'a_userdel', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('82', 'a_viewauth', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('83', 'a_viewlogs', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('84', 'a_words', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('85', 'u_', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('86', 'u_attach', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('87', 'u_chgavatar', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('88', 'u_chgcensors', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('89', 'u_chgemail', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('90', 'u_chggrp', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('91', 'u_chgname', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('92', 'u_chgpasswd', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('93', 'u_download', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('94', 'u_hideonline', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('95', 'u_ignoreflood', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('96', 'u_masspm', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('97', 'u_masspm_group', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('98', 'u_pm_attach', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('99', 'u_pm_bbcode', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('100', 'u_pm_delete', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('101', 'u_pm_download', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('102', 'u_pm_edit', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('103', 'u_pm_emailpm', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('104', 'u_pm_flash', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('105', 'u_pm_forward', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('106', 'u_pm_img', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('107', 'u_pm_printpm', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('108', 'u_pm_smilies', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('109', 'u_readpm', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('110', 'u_savedrafts', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('111', 'u_search', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('112', 'u_sendemail', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('113', 'u_sendim', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('114', 'u_sendpm', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('115', 'u_sig', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('116', 'u_viewonline', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('117', 'u_viewprofile', '1', '0', '0');
INSERT INTO "phpbb_acl_options" VALUES ('119', 'u_mobile', '1', '0', '0');

-- ----------------------------
-- Records of phpbb_acl_roles
-- ----------------------------
INSERT INTO "phpbb_acl_roles" VALUES ('1', 'ROLE_ADMIN_STANDARD', 'ROLE_DESCRIPTION_ADMIN_STANDARD', 'a_', '1');
INSERT INTO "phpbb_acl_roles" VALUES ('2', 'ROLE_ADMIN_FORUM', 'ROLE_DESCRIPTION_ADMIN_FORUM', 'a_', '3');
INSERT INTO "phpbb_acl_roles" VALUES ('3', 'ROLE_ADMIN_USERGROUP', 'ROLE_DESCRIPTION_ADMIN_USERGROUP', 'a_', '4');
INSERT INTO "phpbb_acl_roles" VALUES ('4', 'ROLE_ADMIN_FULL', 'ROLE_DESCRIPTION_ADMIN_FULL', 'a_', '2');
INSERT INTO "phpbb_acl_roles" VALUES ('5', 'ROLE_USER_FULL', 'ROLE_DESCRIPTION_USER_FULL', 'u_', '3');
INSERT INTO "phpbb_acl_roles" VALUES ('6', 'ROLE_USER_STANDARD', 'ROLE_DESCRIPTION_USER_STANDARD', 'u_', '1');
INSERT INTO "phpbb_acl_roles" VALUES ('7', 'ROLE_USER_LIMITED', 'ROLE_DESCRIPTION_USER_LIMITED', 'u_', '2');
INSERT INTO "phpbb_acl_roles" VALUES ('8', 'ROLE_USER_NOPM', 'ROLE_DESCRIPTION_USER_NOPM', 'u_', '4');
INSERT INTO "phpbb_acl_roles" VALUES ('9', 'ROLE_USER_NOAVATAR', 'ROLE_DESCRIPTION_USER_NOAVATAR', 'u_', '5');
INSERT INTO "phpbb_acl_roles" VALUES ('10', 'ROLE_MOD_FULL', 'ROLE_DESCRIPTION_MOD_FULL', 'm_', '3');
INSERT INTO "phpbb_acl_roles" VALUES ('11', 'ROLE_MOD_STANDARD', 'ROLE_DESCRIPTION_MOD_STANDARD', 'm_', '1');
INSERT INTO "phpbb_acl_roles" VALUES ('12', 'ROLE_MOD_SIMPLE', 'ROLE_DESCRIPTION_MOD_SIMPLE', 'm_', '2');
INSERT INTO "phpbb_acl_roles" VALUES ('13', 'ROLE_MOD_QUEUE', 'ROLE_DESCRIPTION_MOD_QUEUE', 'm_', '4');
INSERT INTO "phpbb_acl_roles" VALUES ('14', 'ROLE_FORUM_FULL', 'ROLE_DESCRIPTION_FORUM_FULL', 'f_', '7');
INSERT INTO "phpbb_acl_roles" VALUES ('15', 'ROLE_FORUM_STANDARD', 'ROLE_DESCRIPTION_FORUM_STANDARD', 'f_', '5');
INSERT INTO "phpbb_acl_roles" VALUES ('16', 'ROLE_FORUM_NOACCESS', 'ROLE_DESCRIPTION_FORUM_NOACCESS', 'f_', '1');
INSERT INTO "phpbb_acl_roles" VALUES ('17', 'ROLE_FORUM_READONLY', 'ROLE_DESCRIPTION_FORUM_READONLY', 'f_', '2');
INSERT INTO "phpbb_acl_roles" VALUES ('18', 'ROLE_FORUM_LIMITED', 'ROLE_DESCRIPTION_FORUM_LIMITED', 'f_', '3');
INSERT INTO "phpbb_acl_roles" VALUES ('19', 'ROLE_FORUM_BOT', 'ROLE_DESCRIPTION_FORUM_BOT', 'f_', '9');
INSERT INTO "phpbb_acl_roles" VALUES ('20', 'ROLE_FORUM_ONQUEUE', 'ROLE_DESCRIPTION_FORUM_ONQUEUE', 'f_', '8');
INSERT INTO "phpbb_acl_roles" VALUES ('21', 'ROLE_FORUM_POLLS', 'ROLE_DESCRIPTION_FORUM_POLLS', 'f_', '6');
INSERT INTO "phpbb_acl_roles" VALUES ('22', 'ROLE_FORUM_LIMITED_POLLS', 'ROLE_DESCRIPTION_FORUM_LIMITED_POLLS', 'f_', '4');
INSERT INTO "phpbb_acl_roles" VALUES ('23', 'ROLE_USER_NEW_MEMBER', 'ROLE_DESCRIPTION_USER_NEW_MEMBER', 'u_', '6');
INSERT INTO "phpbb_acl_roles" VALUES ('24', 'ROLE_FORUM_NEW_MEMBER', 'ROLE_DESCRIPTION_FORUM_NEW_MEMBER', 'f_', '10');

-- ----------------------------
-- Records of phpbb_acl_roles_data
-- ----------------------------
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '44', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '46', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '47', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '48', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '50', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '51', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '52', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '56', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '57', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '58', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '59', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '60', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '61', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '62', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '63', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '66', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '68', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '70', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '71', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '72', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '73', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '79', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '80', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '81', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '82', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '83', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('1', '84', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '44', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '47', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '48', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '56', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '57', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '58', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '59', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '66', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '71', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '79', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '82', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('2', '83', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '44', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '47', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '48', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '50', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '60', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '61', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '62', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '72', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '79', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '80', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '82', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('3', '83', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '44', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '45', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '46', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '47', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '48', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '49', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '50', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '51', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '52', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '53', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '54', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '55', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '56', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '57', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '58', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '59', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '60', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '61', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '62', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '63', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '64', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '65', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '66', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '67', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '68', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '69', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '70', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '71', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '72', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '73', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '74', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '75', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '76', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '77', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '78', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '79', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '80', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '81', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '82', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '83', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('4', '84', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '85', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '86', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '87', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '88', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '89', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '90', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '91', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '92', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '93', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '94', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '95', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '96', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '97', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '98', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '99', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '100', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '101', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '102', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '103', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '104', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '105', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '106', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '107', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '108', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '109', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '110', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '111', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '112', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '113', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '114', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '115', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '116', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '117', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('5', '119', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '85', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '86', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '87', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '88', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '89', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '92', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '93', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '94', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '96', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '97', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '98', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '99', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '100', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '101', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '102', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '103', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '106', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '107', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '108', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '109', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '110', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '111', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '112', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '113', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '114', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '115', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '117', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('6', '119', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '85', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '87', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '88', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '89', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '92', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '93', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '94', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '99', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '100', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '101', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '102', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '105', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '106', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '107', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '108', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '109', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '114', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '115', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '117', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('7', '119', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '85', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '87', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '88', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '89', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '92', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '93', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '94', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '96', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '97', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '109', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '114', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '115', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '117', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('8', '119', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '85', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '87', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '88', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '89', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '92', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '93', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '94', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '96', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '97', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '99', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '100', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '101', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '102', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '105', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '106', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '107', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '108', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '109', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '114', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '115', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '117', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('9', '119', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '31', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '32', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '33', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '34', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '35', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '36', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '37', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '38', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '39', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '40', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '41', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '42', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('10', '43', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '31', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '32', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '34', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '35', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '36', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '37', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '38', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '39', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '40', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '41', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('11', '43', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('12', '31', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('12', '34', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('12', '35', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('12', '36', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('12', '40', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('13', '31', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('13', '32', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('13', '35', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '2', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '3', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '4', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '5', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '6', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '8', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '9', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '10', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '11', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '12', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '13', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '15', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '16', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '17', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '18', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '21', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '22', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '24', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '25', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '26', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '28', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '29', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('14', '30', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '3', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '4', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '5', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '6', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '8', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '9', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '11', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '13', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '15', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '17', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '18', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '21', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '22', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '24', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '25', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '29', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('15', '30', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('16', '1', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('17', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '4', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '8', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '9', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '13', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '15', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '17', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '18', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '21', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '22', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '24', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '25', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('18', '29', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('19', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('19', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('19', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('19', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('19', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '3', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '4', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '8', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '9', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '13', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '15', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '17', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '18', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '21', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '22', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '24', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '25', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('20', '29', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '3', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '4', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '5', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '6', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '8', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '9', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '11', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '13', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '15', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '16', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '17', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '18', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '21', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '22', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '24', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '25', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '29', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('21', '30', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '1', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '4', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '7', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '8', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '9', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '13', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '14', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '15', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '16', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '17', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '18', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '19', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '20', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '21', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '22', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '23', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '24', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '25', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '27', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('22', '29', '1');
INSERT INTO "phpbb_acl_roles_data" VALUES ('23', '96', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('23', '97', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('23', '114', '0');
INSERT INTO "phpbb_acl_roles_data" VALUES ('24', '15', '0');

-- ----------------------------
-- Records of phpbb_acl_users
-- ----------------------------
INSERT INTO "phpbb_acl_users" VALUES ('2', '0', '0', '5', '0');

-- ----------------------------
-- Records of phpbb_bots
-- ----------------------------
INSERT INTO "phpbb_bots" VALUES ('1', '1', 'AdsBot [Google]', '3', 'AdsBot-Google', '');
INSERT INTO "phpbb_bots" VALUES ('2', '1', 'Alexa [Bot]', '4', 'ia_archiver', '');
INSERT INTO "phpbb_bots" VALUES ('3', '1', 'Alta Vista [Bot]', '5', 'Scooter/', '');
INSERT INTO "phpbb_bots" VALUES ('4', '1', 'Ask Jeeves [Bot]', '6', 'Ask Jeeves', '');
INSERT INTO "phpbb_bots" VALUES ('5', '1', 'Baidu [Spider]', '7', 'Baiduspider+(', '');
INSERT INTO "phpbb_bots" VALUES ('6', '1', 'Bing [Bot]', '8', 'bingbot/', '');
INSERT INTO "phpbb_bots" VALUES ('7', '1', 'Exabot [Bot]', '9', 'Exabot/', '');
INSERT INTO "phpbb_bots" VALUES ('8', '1', 'FAST Enterprise [Crawler]', '10', 'FAST Enterprise Crawler', '');
INSERT INTO "phpbb_bots" VALUES ('9', '1', 'FAST WebCrawler [Crawler]', '11', 'FAST-WebCrawler/', '');
INSERT INTO "phpbb_bots" VALUES ('10', '1', 'Francis [Bot]', '12', 'http://www.neomo.de/', '');
INSERT INTO "phpbb_bots" VALUES ('11', '1', 'Gigabot [Bot]', '13', 'Gigabot/', '');
INSERT INTO "phpbb_bots" VALUES ('12', '1', 'Google Adsense [Bot]', '14', 'Mediapartners-Google', '');
INSERT INTO "phpbb_bots" VALUES ('13', '1', 'Google Desktop', '15', 'Google Desktop', '');
INSERT INTO "phpbb_bots" VALUES ('14', '1', 'Google Feedfetcher', '16', 'Feedfetcher-Google', '');
INSERT INTO "phpbb_bots" VALUES ('15', '1', 'Google [Bot]', '17', 'Googlebot', '');
INSERT INTO "phpbb_bots" VALUES ('16', '1', 'Heise IT-Markt [Crawler]', '18', 'heise-IT-Markt-Crawler', '');
INSERT INTO "phpbb_bots" VALUES ('17', '1', 'Heritrix [Crawler]', '19', 'heritrix/1.', '');
INSERT INTO "phpbb_bots" VALUES ('18', '1', 'IBM Research [Bot]', '20', 'ibm.com/cs/crawler', '');
INSERT INTO "phpbb_bots" VALUES ('19', '1', 'ICCrawler - ICjobs', '21', 'ICCrawler - ICjobs', '');
INSERT INTO "phpbb_bots" VALUES ('20', '1', 'ichiro [Crawler]', '22', 'ichiro/', '');
INSERT INTO "phpbb_bots" VALUES ('21', '1', 'Majestic-12 [Bot]', '23', 'MJ12bot/', '');
INSERT INTO "phpbb_bots" VALUES ('22', '1', 'Metager [Bot]', '24', 'MetagerBot/', '');
INSERT INTO "phpbb_bots" VALUES ('23', '1', 'MSN NewsBlogs', '25', 'msnbot-NewsBlogs/', '');
INSERT INTO "phpbb_bots" VALUES ('24', '1', 'MSN [Bot]', '26', 'msnbot/', '');
INSERT INTO "phpbb_bots" VALUES ('25', '1', 'MSNbot Media', '27', 'msnbot-media/', '');
INSERT INTO "phpbb_bots" VALUES ('26', '1', 'NG-Search [Bot]', '28', 'NG-Search/', '');
INSERT INTO "phpbb_bots" VALUES ('27', '1', 'Nutch [Bot]', '29', 'http://lucene.apache.org/nutch/', '');
INSERT INTO "phpbb_bots" VALUES ('28', '1', 'Nutch/CVS [Bot]', '30', 'NutchCVS/', '');
INSERT INTO "phpbb_bots" VALUES ('29', '1', 'OmniExplorer [Bot]', '31', 'OmniExplorer_Bot/', '');
INSERT INTO "phpbb_bots" VALUES ('30', '1', 'Online link [Validator]', '32', 'online link validator', '');
INSERT INTO "phpbb_bots" VALUES ('31', '1', 'psbot [Picsearch]', '33', 'psbot/0', '');
INSERT INTO "phpbb_bots" VALUES ('32', '1', 'Seekport [Bot]', '34', 'Seekbot/', '');
INSERT INTO "phpbb_bots" VALUES ('33', '1', 'Sensis [Crawler]', '35', 'Sensis Web Crawler', '');
INSERT INTO "phpbb_bots" VALUES ('34', '1', 'SEO Crawler', '36', 'SEO search Crawler/', '');
INSERT INTO "phpbb_bots" VALUES ('35', '1', 'Seoma [Crawler]', '37', 'Seoma [SEO Crawler]', '');
INSERT INTO "phpbb_bots" VALUES ('36', '1', 'SEOSearch [Crawler]', '38', 'SEOsearch/', '');
INSERT INTO "phpbb_bots" VALUES ('37', '1', 'Snappy [Bot]', '39', 'Snappy/1.1 ( http://www.urltrends.com/ )', '');
INSERT INTO "phpbb_bots" VALUES ('38', '1', 'Steeler [Crawler]', '40', 'http://www.tkl.iis.u-tokyo.ac.jp/~crawler/', '');
INSERT INTO "phpbb_bots" VALUES ('39', '1', 'Synoo [Bot]', '41', 'SynooBot/', '');
INSERT INTO "phpbb_bots" VALUES ('40', '1', 'Telekom [Bot]', '42', 'crawleradmin.t-info@telekom.de', '');
INSERT INTO "phpbb_bots" VALUES ('41', '1', 'TurnitinBot [Bot]', '43', 'TurnitinBot/', '');
INSERT INTO "phpbb_bots" VALUES ('42', '1', 'Voyager [Bot]', '44', 'voyager/1.0', '');
INSERT INTO "phpbb_bots" VALUES ('43', '1', 'W3 [Sitesearch]', '45', 'W3 SiteSearch Crawler', '');
INSERT INTO "phpbb_bots" VALUES ('44', '1', 'W3C [Linkcheck]', '46', 'W3C-checklink/', '');
INSERT INTO "phpbb_bots" VALUES ('45', '1', 'W3C [Validator]', '47', 'W3C_*Validator', '');
INSERT INTO "phpbb_bots" VALUES ('46', '1', 'WiseNut [Bot]', '48', 'http://www.WISEnutbot.com', '');
INSERT INTO "phpbb_bots" VALUES ('47', '1', 'YaCy [Bot]', '49', 'yacybot', '');
INSERT INTO "phpbb_bots" VALUES ('48', '1', 'Yahoo MMCrawler [Bot]', '50', 'Yahoo-MMCrawler/', '');
INSERT INTO "phpbb_bots" VALUES ('49', '1', 'Yahoo Slurp [Bot]', '51', 'Yahoo! DE Slurp', '');
INSERT INTO "phpbb_bots" VALUES ('50', '1', 'Yahoo [Bot]', '52', 'Yahoo! Slurp', '');
INSERT INTO "phpbb_bots" VALUES ('51', '1', 'YahooSeeker [Bot]', '53', 'YahooSeeker/', '');

-- ----------------------------
-- Records of phpbb_config
-- ----------------------------
INSERT INTO "phpbb_config" VALUES ('active_sessions', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_attachments', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_autologin', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_avatar', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_avatar_local', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_avatar_remote', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_avatar_remote_upload', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_avatar_upload', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_bbcode', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_birthdays', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_bookmarks', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_emailreuse', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_forum_notify', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_mass_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_name_chars', 'USERNAME_ALPHA_ONLY', '0');
INSERT INTO "phpbb_config" VALUES ('allow_namechange', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_nocensors', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_pm_attach', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_pm_report', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_post_flash', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_post_links', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_privmsg', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_quick_reply', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig_bbcode', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig_flash', '0', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig_img', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig_links', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_sig_smilies', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_smilies', '1', '0');
INSERT INTO "phpbb_config" VALUES ('allow_topic_notify', '1', '0');
INSERT INTO "phpbb_config" VALUES ('attachment_quota', '52428800', '0');
INSERT INTO "phpbb_config" VALUES ('auth_bbcode_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('auth_flash_pm', '0', '0');
INSERT INTO "phpbb_config" VALUES ('auth_img_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('auth_method', 'db', '0');
INSERT INTO "phpbb_config" VALUES ('auth_smilies_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_filesize', '6144', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_gallery_path', 'images/avatars/gallery', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_max_height', '90', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_max_width', '90', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_min_height', '20', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_min_width', '20', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_path', 'images/avatars/upload', '0');
INSERT INTO "phpbb_config" VALUES ('avatar_salt', 'a159e80f0af06392aef899dc30c644da', '0');
INSERT INTO "phpbb_config" VALUES ('board_contact', 'webmaster@catroid.org', '0');
INSERT INTO "phpbb_config" VALUES ('board_disable', '0', '0');
INSERT INTO "phpbb_config" VALUES ('board_disable_msg', '', '0');
INSERT INTO "phpbb_config" VALUES ('board_dst', '0', '0');
INSERT INTO "phpbb_config" VALUES ('board_email', 'webmaster@catroid.org', '0');
INSERT INTO "phpbb_config" VALUES ('board_email_form', '0', '0');
INSERT INTO "phpbb_config" VALUES ('board_email_sig', 'Thanks, The Management', '0');
INSERT INTO "phpbb_config" VALUES ('board_hide_emails', '1', '0');
INSERT INTO "phpbb_config" VALUES ('board_startdate', '1297685283', '0');
INSERT INTO "phpbb_config" VALUES ('board_timezone', '0', '0');
INSERT INTO "phpbb_config" VALUES ('browser_check', '1', '0');
INSERT INTO "phpbb_config" VALUES ('bump_interval', '10', '0');
INSERT INTO "phpbb_config" VALUES ('bump_type', 'd', '0');
INSERT INTO "phpbb_config" VALUES ('cache_gc', '7200', '0');
INSERT INTO "phpbb_config" VALUES ('cache_last_gc', '1298466165', '1');
INSERT INTO "phpbb_config" VALUES ('captcha_gd', '1', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_gd_3d_noise', '1', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_gd_fonts', '1', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_gd_foreground_noise', '0', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_gd_wave', '0', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_gd_x_grid', '25', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_gd_y_grid', '25', '0');
INSERT INTO "phpbb_config" VALUES ('captcha_plugin', 'phpbb_captcha_gd', '0');
INSERT INTO "phpbb_config" VALUES ('check_attachment_content', '1', '0');
INSERT INTO "phpbb_config" VALUES ('check_dnsbl', '0', '0');
INSERT INTO "phpbb_config" VALUES ('chg_passforce', '0', '0');
INSERT INTO "phpbb_config" VALUES ('confirm_refresh', '1', '0');
INSERT INTO "phpbb_config" VALUES ('cookie_domain', '', '0');
INSERT INTO "phpbb_config" VALUES ('cookie_name', 'phpbb3_oh9mc', '0');
INSERT INTO "phpbb_config" VALUES ('cookie_path', '/', '0');
INSERT INTO "phpbb_config" VALUES ('cookie_secure', '0', '0');
INSERT INTO "phpbb_config" VALUES ('coppa_enable', '0', '0');
INSERT INTO "phpbb_config" VALUES ('coppa_fax', '', '0');
INSERT INTO "phpbb_config" VALUES ('coppa_mail', '', '0');
INSERT INTO "phpbb_config" VALUES ('cron_lock', '0', '1');
INSERT INTO "phpbb_config" VALUES ('database_gc', '604800', '0');
INSERT INTO "phpbb_config" VALUES ('database_last_gc', '1298451629', '1');
INSERT INTO "phpbb_config" VALUES ('dbms_version', '8.3.12, compiled by Visual C++ build 1400', '0');
INSERT INTO "phpbb_config" VALUES ('default_dateformat', 'D M d, Y g:i a', '0');
INSERT INTO "phpbb_config" VALUES ('default_lang', 'en', '0');
INSERT INTO "phpbb_config" VALUES ('default_style', '1', '0');
INSERT INTO "phpbb_config" VALUES ('delete_time', '0', '0');
INSERT INTO "phpbb_config" VALUES ('display_last_edited', '1', '0');
INSERT INTO "phpbb_config" VALUES ('display_order', '0', '0');
INSERT INTO "phpbb_config" VALUES ('edit_time', '0', '0');
INSERT INTO "phpbb_config" VALUES ('email_check_mx', '1', '0');
INSERT INTO "phpbb_config" VALUES ('email_enable', '1', '0');
INSERT INTO "phpbb_config" VALUES ('email_function_name', 'mail', '0');
INSERT INTO "phpbb_config" VALUES ('email_package_size', '20', '0');
INSERT INTO "phpbb_config" VALUES ('enable_confirm', '0', '0');
INSERT INTO "phpbb_config" VALUES ('enable_pm_icons', '1', '0');
INSERT INTO "phpbb_config" VALUES ('enable_post_confirm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('feed_enable', '0', '0');
INSERT INTO "phpbb_config" VALUES ('feed_forum', '1', '0');
INSERT INTO "phpbb_config" VALUES ('feed_http_auth', '0', '0');
INSERT INTO "phpbb_config" VALUES ('feed_item_statistics', '1', '0');
INSERT INTO "phpbb_config" VALUES ('feed_limit_post', '15', '0');
INSERT INTO "phpbb_config" VALUES ('feed_limit_topic', '10', '0');
INSERT INTO "phpbb_config" VALUES ('feed_overall', '1', '0');
INSERT INTO "phpbb_config" VALUES ('feed_overall_forums', '0', '0');
INSERT INTO "phpbb_config" VALUES ('feed_topic', '1', '0');
INSERT INTO "phpbb_config" VALUES ('feed_topics_active', '0', '0');
INSERT INTO "phpbb_config" VALUES ('feed_topics_new', '1', '0');
INSERT INTO "phpbb_config" VALUES ('flood_interval', '15', '0');
INSERT INTO "phpbb_config" VALUES ('force_server_vars', '0', '0');
INSERT INTO "phpbb_config" VALUES ('form_token_lifetime', '7200', '0');
INSERT INTO "phpbb_config" VALUES ('form_token_mintime', '0', '0');
INSERT INTO "phpbb_config" VALUES ('form_token_sid_guests', '1', '0');
INSERT INTO "phpbb_config" VALUES ('forward_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('forwarded_for_check', '0', '0');
INSERT INTO "phpbb_config" VALUES ('full_folder_action', '2', '0');
INSERT INTO "phpbb_config" VALUES ('fulltext_mysql_max_word_len', '254', '0');
INSERT INTO "phpbb_config" VALUES ('fulltext_mysql_min_word_len', '4', '0');
INSERT INTO "phpbb_config" VALUES ('fulltext_native_common_thres', '5', '0');
INSERT INTO "phpbb_config" VALUES ('fulltext_native_load_upd', '1', '0');
INSERT INTO "phpbb_config" VALUES ('fulltext_native_max_chars', '14', '0');
INSERT INTO "phpbb_config" VALUES ('fulltext_native_min_chars', '3', '0');
INSERT INTO "phpbb_config" VALUES ('gzip_compress', '0', '0');
INSERT INTO "phpbb_config" VALUES ('hot_threshold', '25', '0');
INSERT INTO "phpbb_config" VALUES ('icons_path', 'images/icons', '0');
INSERT INTO "phpbb_config" VALUES ('img_create_thumbnail', '0', '0');
INSERT INTO "phpbb_config" VALUES ('img_display_inlined', '1', '0');
INSERT INTO "phpbb_config" VALUES ('img_imagick', '', '0');
INSERT INTO "phpbb_config" VALUES ('img_link_height', '0', '0');
INSERT INTO "phpbb_config" VALUES ('img_link_width', '0', '0');
INSERT INTO "phpbb_config" VALUES ('img_max_height', '0', '0');
INSERT INTO "phpbb_config" VALUES ('img_max_thumb_width', '400', '0');
INSERT INTO "phpbb_config" VALUES ('img_max_width', '0', '0');
INSERT INTO "phpbb_config" VALUES ('img_min_thumb_filesize', '12000', '0');
INSERT INTO "phpbb_config" VALUES ('ip_check', '3', '0');
INSERT INTO "phpbb_config" VALUES ('jab_enable', '0', '0');
INSERT INTO "phpbb_config" VALUES ('jab_host', '', '0');
INSERT INTO "phpbb_config" VALUES ('jab_package_size', '20', '0');
INSERT INTO "phpbb_config" VALUES ('jab_password', '', '0');
INSERT INTO "phpbb_config" VALUES ('jab_port', '5222', '0');
INSERT INTO "phpbb_config" VALUES ('jab_use_ssl', '0', '0');
INSERT INTO "phpbb_config" VALUES ('jab_username', '', '0');
INSERT INTO "phpbb_config" VALUES ('last_queue_run', '0', '1');
INSERT INTO "phpbb_config" VALUES ('ldap_base_dn', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_email', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_password', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_port', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_server', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_uid', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_user', '', '0');
INSERT INTO "phpbb_config" VALUES ('ldap_user_filter', '', '0');
INSERT INTO "phpbb_config" VALUES ('limit_load', '0', '0');
INSERT INTO "phpbb_config" VALUES ('limit_search_load', '0', '0');
INSERT INTO "phpbb_config" VALUES ('load_anon_lastread', '0', '0');
INSERT INTO "phpbb_config" VALUES ('load_birthdays', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_cpf_memberlist', '0', '0');
INSERT INTO "phpbb_config" VALUES ('load_cpf_viewprofile', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_cpf_viewtopic', '0', '0');
INSERT INTO "phpbb_config" VALUES ('load_db_lastread', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_db_track', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_jumpbox', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_moderators', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_online', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_online_guests', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_online_time', '5', '0');
INSERT INTO "phpbb_config" VALUES ('load_onlinetrack', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_search', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_tplcompile', '0', '0');
INSERT INTO "phpbb_config" VALUES ('load_unreads_search', '1', '0');
INSERT INTO "phpbb_config" VALUES ('load_user_activity', '1', '0');
INSERT INTO "phpbb_config" VALUES ('max_attachments', '3', '0');
INSERT INTO "phpbb_config" VALUES ('max_attachments_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('max_autologin_time', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_filesize', '262144', '0');
INSERT INTO "phpbb_config" VALUES ('max_filesize_pm', '262144', '0');
INSERT INTO "phpbb_config" VALUES ('max_login_attempts', '999', '0');
INSERT INTO "phpbb_config" VALUES ('max_name_chars', '20', '0');
INSERT INTO "phpbb_config" VALUES ('max_num_search_keywords', '10', '0');
INSERT INTO "phpbb_config" VALUES ('max_pass_chars', '100', '0');
INSERT INTO "phpbb_config" VALUES ('max_poll_options', '10', '0');
INSERT INTO "phpbb_config" VALUES ('max_post_chars', '60000', '0');
INSERT INTO "phpbb_config" VALUES ('max_post_font_size', '200', '0');
INSERT INTO "phpbb_config" VALUES ('max_post_img_height', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_post_img_width', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_post_smilies', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_post_urls', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_quote_depth', '3', '0');
INSERT INTO "phpbb_config" VALUES ('max_reg_attempts', '5', '0');
INSERT INTO "phpbb_config" VALUES ('max_sig_chars', '255', '0');
INSERT INTO "phpbb_config" VALUES ('max_sig_font_size', '200', '0');
INSERT INTO "phpbb_config" VALUES ('max_sig_img_height', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_sig_img_width', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_sig_smilies', '0', '0');
INSERT INTO "phpbb_config" VALUES ('max_sig_urls', '5', '0');
INSERT INTO "phpbb_config" VALUES ('mime_triggers', 'body|head|html|img|plaintext|a href|pre|script|table|title', '0');
INSERT INTO "phpbb_config" VALUES ('min_name_chars', '3', '0');
INSERT INTO "phpbb_config" VALUES ('min_pass_chars', '6', '0');
INSERT INTO "phpbb_config" VALUES ('min_post_chars', '1', '0');
INSERT INTO "phpbb_config" VALUES ('min_search_author_chars', '3', '0');
INSERT INTO "phpbb_config" VALUES ('mobile_agents', '/iPad|iPhone|iOS|Opera Mobi|BlackBerry|Android|IEMobile|Symbian/', '0');
INSERT INTO "phpbb_config" VALUES ('mobile_enabled', '1', '0');
INSERT INTO "phpbb_config" VALUES ('new_member_group_default', '0', '0');
INSERT INTO "phpbb_config" VALUES ('new_member_post_limit', '3', '0');
INSERT INTO "phpbb_config" VALUES ('newest_user_colour', '', '1');
INSERT INTO "phpbb_config" VALUES ('num_files', '0', '1');
INSERT INTO "phpbb_config" VALUES ('num_posts', '2', '1');
INSERT INTO "phpbb_config" VALUES ('num_topics', '1', '1');
INSERT INTO "phpbb_config" VALUES ('num_users', '1', '1');
INSERT INTO "phpbb_config" VALUES ('override_user_style', '0', '0');
INSERT INTO "phpbb_config" VALUES ('pass_complex', 'PASS_TYPE_ANY', '0');
INSERT INTO "phpbb_config" VALUES ('phpbb_mobile_version', '2.0.0-RC1', '0');
INSERT INTO "phpbb_config" VALUES ('pm_edit_time', '0', '0');
INSERT INTO "phpbb_config" VALUES ('pm_max_boxes', '4', '0');
INSERT INTO "phpbb_config" VALUES ('pm_max_msgs', '50', '0');
INSERT INTO "phpbb_config" VALUES ('pm_max_recipients', '0', '0');
INSERT INTO "phpbb_config" VALUES ('posts_per_page', '10', '0');
INSERT INTO "phpbb_config" VALUES ('print_pm', '1', '0');
INSERT INTO "phpbb_config" VALUES ('questionnaire_unique_id', 'a3bae5cf6787643e', '0');
INSERT INTO "phpbb_config" VALUES ('queue_interval', '60', '0');
INSERT INTO "phpbb_config" VALUES ('rand_seed', 'aa3b41532fdb86659962f53680c356c2', '1');
INSERT INTO "phpbb_config" VALUES ('rand_seed_last_update', '1298466866', '1');
INSERT INTO "phpbb_config" VALUES ('ranks_path', 'images/ranks', '0');
INSERT INTO "phpbb_config" VALUES ('record_online_date', '1298138628', '1');
INSERT INTO "phpbb_config" VALUES ('record_online_users', '4', '1');
INSERT INTO "phpbb_config" VALUES ('referer_validation', '1', '0');
INSERT INTO "phpbb_config" VALUES ('require_activation', '0', '0');
INSERT INTO "phpbb_config" VALUES ('script_path', '/addons/board', '0');
INSERT INTO "phpbb_config" VALUES ('search_anonymous_interval', '0', '0');
INSERT INTO "phpbb_config" VALUES ('search_block_size', '250', '0');
INSERT INTO "phpbb_config" VALUES ('search_gc', '7200', '0');
INSERT INTO "phpbb_config" VALUES ('search_indexing_state', '', '1');
INSERT INTO "phpbb_config" VALUES ('search_interval', '0', '0');
INSERT INTO "phpbb_config" VALUES ('search_last_gc', '1298466274', '1');
INSERT INTO "phpbb_config" VALUES ('search_store_results', '1800', '0');
INSERT INTO "phpbb_config" VALUES ('search_type', 'fulltext_native', '0');
INSERT INTO "phpbb_config" VALUES ('secure_allow_deny', '1', '0');
INSERT INTO "phpbb_config" VALUES ('secure_allow_empty_referer', '1', '0');
INSERT INTO "phpbb_config" VALUES ('secure_downloads', '0', '0');
INSERT INTO "phpbb_config" VALUES ('server_name', '', '0');
INSERT INTO "phpbb_config" VALUES ('server_port', '80', '0');
INSERT INTO "phpbb_config" VALUES ('server_protocol', 'http://', '0');
INSERT INTO "phpbb_config" VALUES ('session_gc', '3600', '0');
INSERT INTO "phpbb_config" VALUES ('session_last_gc', '1298466339', '1');
INSERT INTO "phpbb_config" VALUES ('session_length', '3600', '0');
INSERT INTO "phpbb_config" VALUES ('site_desc', 'A short text to describe your forum', '0');
INSERT INTO "phpbb_config" VALUES ('sitename', 'yourdomain.com', '0');
INSERT INTO "phpbb_config" VALUES ('smilies_path', 'images/smilies', '0');
INSERT INTO "phpbb_config" VALUES ('smilies_per_page', '50', '0');
INSERT INTO "phpbb_config" VALUES ('smtp_auth_method', 'PLAIN', '0');
INSERT INTO "phpbb_config" VALUES ('smtp_delivery', '0', '0');
INSERT INTO "phpbb_config" VALUES ('smtp_host', '', '0');
INSERT INTO "phpbb_config" VALUES ('smtp_password', '', '0');
INSERT INTO "phpbb_config" VALUES ('smtp_port', '25', '0');
INSERT INTO "phpbb_config" VALUES ('smtp_username', '', '0');
INSERT INTO "phpbb_config" VALUES ('topics_per_page', '25', '0');
INSERT INTO "phpbb_config" VALUES ('tpl_allow_php', '0', '0');
INSERT INTO "phpbb_config" VALUES ('upload_dir_size', '0', '1');
INSERT INTO "phpbb_config" VALUES ('upload_icons_path', 'images/upload_icons', '0');
INSERT INTO "phpbb_config" VALUES ('upload_path', 'files', '0');
INSERT INTO "phpbb_config" VALUES ('version', '3.0.8', '0');
INSERT INTO "phpbb_config" VALUES ('warnings_expire_days', '90', '0');
INSERT INTO "phpbb_config" VALUES ('warnings_gc', '14400', '0');
INSERT INTO "phpbb_config" VALUES ('warnings_last_gc', '1298466269', '1');

-- ----------------------------
-- Records of phpbb_extensions
-- ----------------------------
INSERT INTO "phpbb_extensions" VALUES ('1', '1', 'gif');
INSERT INTO "phpbb_extensions" VALUES ('2', '1', 'png');
INSERT INTO "phpbb_extensions" VALUES ('3', '1', 'jpeg');
INSERT INTO "phpbb_extensions" VALUES ('4', '1', 'jpg');
INSERT INTO "phpbb_extensions" VALUES ('5', '1', 'tif');
INSERT INTO "phpbb_extensions" VALUES ('6', '1', 'tiff');
INSERT INTO "phpbb_extensions" VALUES ('7', '1', 'tga');
INSERT INTO "phpbb_extensions" VALUES ('8', '2', 'gtar');
INSERT INTO "phpbb_extensions" VALUES ('9', '2', 'gz');
INSERT INTO "phpbb_extensions" VALUES ('10', '2', 'tar');
INSERT INTO "phpbb_extensions" VALUES ('11', '2', 'zip');
INSERT INTO "phpbb_extensions" VALUES ('12', '2', 'rar');
INSERT INTO "phpbb_extensions" VALUES ('13', '2', 'ace');
INSERT INTO "phpbb_extensions" VALUES ('14', '2', 'torrent');
INSERT INTO "phpbb_extensions" VALUES ('15', '2', 'tgz');
INSERT INTO "phpbb_extensions" VALUES ('16', '2', 'bz2');
INSERT INTO "phpbb_extensions" VALUES ('17', '2', '7z');
INSERT INTO "phpbb_extensions" VALUES ('18', '3', 'txt');
INSERT INTO "phpbb_extensions" VALUES ('19', '3', 'c');
INSERT INTO "phpbb_extensions" VALUES ('20', '3', 'h');
INSERT INTO "phpbb_extensions" VALUES ('21', '3', 'cpp');
INSERT INTO "phpbb_extensions" VALUES ('22', '3', 'hpp');
INSERT INTO "phpbb_extensions" VALUES ('23', '3', 'diz');
INSERT INTO "phpbb_extensions" VALUES ('24', '3', 'csv');
INSERT INTO "phpbb_extensions" VALUES ('25', '3', 'ini');
INSERT INTO "phpbb_extensions" VALUES ('26', '3', 'log');
INSERT INTO "phpbb_extensions" VALUES ('27', '3', 'js');
INSERT INTO "phpbb_extensions" VALUES ('28', '3', 'xml');
INSERT INTO "phpbb_extensions" VALUES ('29', '4', 'xls');
INSERT INTO "phpbb_extensions" VALUES ('30', '4', 'xlsx');
INSERT INTO "phpbb_extensions" VALUES ('31', '4', 'xlsm');
INSERT INTO "phpbb_extensions" VALUES ('32', '4', 'xlsb');
INSERT INTO "phpbb_extensions" VALUES ('33', '4', 'doc');
INSERT INTO "phpbb_extensions" VALUES ('34', '4', 'docx');
INSERT INTO "phpbb_extensions" VALUES ('35', '4', 'docm');
INSERT INTO "phpbb_extensions" VALUES ('36', '4', 'dot');
INSERT INTO "phpbb_extensions" VALUES ('37', '4', 'dotx');
INSERT INTO "phpbb_extensions" VALUES ('38', '4', 'dotm');
INSERT INTO "phpbb_extensions" VALUES ('39', '4', 'pdf');
INSERT INTO "phpbb_extensions" VALUES ('40', '4', 'ai');
INSERT INTO "phpbb_extensions" VALUES ('41', '4', 'ps');
INSERT INTO "phpbb_extensions" VALUES ('42', '4', 'ppt');
INSERT INTO "phpbb_extensions" VALUES ('43', '4', 'pptx');
INSERT INTO "phpbb_extensions" VALUES ('44', '4', 'pptm');
INSERT INTO "phpbb_extensions" VALUES ('45', '4', 'odg');
INSERT INTO "phpbb_extensions" VALUES ('46', '4', 'odp');
INSERT INTO "phpbb_extensions" VALUES ('47', '4', 'ods');
INSERT INTO "phpbb_extensions" VALUES ('48', '4', 'odt');
INSERT INTO "phpbb_extensions" VALUES ('49', '4', 'rtf');
INSERT INTO "phpbb_extensions" VALUES ('50', '5', 'rm');
INSERT INTO "phpbb_extensions" VALUES ('51', '5', 'ram');
INSERT INTO "phpbb_extensions" VALUES ('52', '6', 'wma');
INSERT INTO "phpbb_extensions" VALUES ('53', '6', 'wmv');
INSERT INTO "phpbb_extensions" VALUES ('54', '7', 'swf');
INSERT INTO "phpbb_extensions" VALUES ('55', '8', 'mov');
INSERT INTO "phpbb_extensions" VALUES ('56', '8', 'm4v');
INSERT INTO "phpbb_extensions" VALUES ('57', '8', 'm4a');
INSERT INTO "phpbb_extensions" VALUES ('58', '8', 'mp4');
INSERT INTO "phpbb_extensions" VALUES ('59', '8', '3gp');
INSERT INTO "phpbb_extensions" VALUES ('60', '8', '3g2');
INSERT INTO "phpbb_extensions" VALUES ('61', '8', 'qt');
INSERT INTO "phpbb_extensions" VALUES ('62', '9', 'mpeg');
INSERT INTO "phpbb_extensions" VALUES ('63', '9', 'mpg');
INSERT INTO "phpbb_extensions" VALUES ('64', '9', 'mp3');
INSERT INTO "phpbb_extensions" VALUES ('65', '9', 'ogg');
INSERT INTO "phpbb_extensions" VALUES ('66', '9', 'ogm');

-- ----------------------------
-- Records of phpbb_extension_groups
-- ----------------------------
INSERT INTO "phpbb_extension_groups" VALUES ('1', 'EXT_GROUP_IMAGES', '1', '1', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('2', 'EXT_GROUP_ARCHIVES', '0', '1', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('3', 'EXT_GROUP_PLAIN_TEXT', '0', '0', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('4', 'EXT_GROUP_DOCUMENTS', '0', '0', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('5', 'EXT_GROUP_REAL_MEDIA', '3', '0', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('6', 'EXT_GROUP_WINDOWS_MEDIA', '2', '0', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('7', 'EXT_GROUP_FLASH_FILES', '5', '0', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('8', 'EXT_GROUP_QUICKTIME_MEDIA', '6', '0', '1', '', '0', '', '0');
INSERT INTO "phpbb_extension_groups" VALUES ('9', 'EXT_GROUP_DOWNLOADABLE_FILES', '0', '0', '1', '', '0', '', '0');

-- ----------------------------
-- Records of phpbb_forums
-- ----------------------------
INSERT INTO "phpbb_forums" VALUES ('1', '0', '1', '4', '', 'Your first category', '', '', '7', '', '', '', '0', '', '', '', '', '7', '', '0', '0', '0', '1', '1', '1', '1', '2', '', '1297685283', 'catroweb', 'AA0000', '32', '0', '1', '1', '1', '1', '0', '0', '0', '0', '0');
INSERT INTO "phpbb_forums" VALUES ('2', '1', '2', '3', 'a:1:{i:1;a:2:{i:0;s:19:"Your first category";i:1;i:0;}}', 'Your first forum', 'Description of your first forum.', '', '7', '', '', '', '0', '', '', '', '', '7', '', '0', '1', '0', '2', '1', '1', '2', '2', 'Re: Welcome to phpBB3', '1297838793', 'catroweb', 'AA0000', '48', '0', '1', '1', '1', '1', '0', '0', '0', '0', '0');

-- ----------------------------
-- Records of phpbb_forums_track
-- ----------------------------
INSERT INTO "phpbb_forums_track" VALUES ('2', '2', '1297838793');

-- ----------------------------
-- Records of phpbb_groups
-- ----------------------------
INSERT INTO "phpbb_groups" VALUES ('1', '3', '0', '0', 'GUESTS', '', '', '7', '', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '5', '0');
INSERT INTO "phpbb_groups" VALUES ('2', '3', '0', '0', 'REGISTERED', '', '', '7', '', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '5', '0');
INSERT INTO "phpbb_groups" VALUES ('3', '3', '0', '0', 'REGISTERED_COPPA', '', '', '7', '', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '5', '0');
INSERT INTO "phpbb_groups" VALUES ('4', '3', '0', '0', 'GLOBAL_MODERATORS', '', '', '7', '', '0', '', '0', '0', '0', '0', '00AA00', '0', '0', '0', '0', '1');
INSERT INTO "phpbb_groups" VALUES ('5', '3', '1', '0', 'ADMINISTRATORS', '', '', '7', '', '0', '', '0', '0', '0', '0', 'AA0000', '0', '0', '0', '0', '1');
INSERT INTO "phpbb_groups" VALUES ('6', '3', '0', '0', 'BOTS', '', '', '7', '', '0', '', '0', '0', '0', '0', '9E8DA7', '0', '0', '0', '5', '0');
INSERT INTO "phpbb_groups" VALUES ('7', '3', '0', '0', 'NEWLY_REGISTERED', '', '', '7', '', '0', '', '0', '0', '0', '0', '', '0', '0', '0', '5', '0');

-- ----------------------------
-- Records of phpbb_icons
-- ----------------------------
INSERT INTO "phpbb_icons" VALUES ('1', 'misc/fire.gif', '16', '16', '1', '1');
INSERT INTO "phpbb_icons" VALUES ('2', 'smile/redface.gif', '16', '16', '9', '1');
INSERT INTO "phpbb_icons" VALUES ('3', 'smile/mrgreen.gif', '16', '16', '10', '1');
INSERT INTO "phpbb_icons" VALUES ('4', 'misc/heart.gif', '16', '16', '4', '1');
INSERT INTO "phpbb_icons" VALUES ('5', 'misc/star.gif', '16', '16', '2', '1');
INSERT INTO "phpbb_icons" VALUES ('6', 'misc/radioactive.gif', '16', '16', '3', '1');
INSERT INTO "phpbb_icons" VALUES ('7', 'misc/thinking.gif', '16', '16', '5', '1');
INSERT INTO "phpbb_icons" VALUES ('8', 'smile/info.gif', '16', '16', '8', '1');
INSERT INTO "phpbb_icons" VALUES ('9', 'smile/question.gif', '16', '16', '6', '1');
INSERT INTO "phpbb_icons" VALUES ('10', 'smile/alert.gif', '16', '16', '7', '1');

-- ----------------------------
-- Records of phpbb_lang
-- ----------------------------
INSERT INTO "phpbb_lang" VALUES ('1', 'de', 'de', 'German (Casual Honorifics)', 'Deutsch (Du)', 'phpBB.de');
INSERT INTO "phpbb_lang" VALUES ('2', 'en', 'en', 'British English', 'British English', 'phpBB Group');

-- ----------------------------
-- Records of phpbb_log
-- ----------------------------

-- ----------------------------
-- Records of phpbb_modules
-- ----------------------------
INSERT INTO "phpbb_modules" VALUES ('1', '1', '1', '', 'acp', '0', '1', '64', 'ACP_CAT_GENERAL', '', '');
INSERT INTO "phpbb_modules" VALUES ('2', '1', '1', '', 'acp', '1', '4', '17', 'ACP_QUICK_ACCESS', '', '');
INSERT INTO "phpbb_modules" VALUES ('3', '1', '1', '', 'acp', '1', '18', '41', 'ACP_BOARD_CONFIGURATION', '', '');
INSERT INTO "phpbb_modules" VALUES ('4', '1', '1', '', 'acp', '1', '42', '49', 'ACP_CLIENT_COMMUNICATION', '', '');
INSERT INTO "phpbb_modules" VALUES ('5', '1', '1', '', 'acp', '1', '50', '63', 'ACP_SERVER_CONFIGURATION', '', '');
INSERT INTO "phpbb_modules" VALUES ('6', '1', '1', '', 'acp', '0', '65', '84', 'ACP_CAT_FORUMS', '', '');
INSERT INTO "phpbb_modules" VALUES ('7', '1', '1', '', 'acp', '6', '66', '71', 'ACP_MANAGE_FORUMS', '', '');
INSERT INTO "phpbb_modules" VALUES ('8', '1', '1', '', 'acp', '6', '72', '83', 'ACP_FORUM_BASED_PERMISSIONS', '', '');
INSERT INTO "phpbb_modules" VALUES ('9', '1', '1', '', 'acp', '0', '85', '110', 'ACP_CAT_POSTING', '', '');
INSERT INTO "phpbb_modules" VALUES ('10', '1', '1', '', 'acp', '9', '86', '99', 'ACP_MESSAGES', '', '');
INSERT INTO "phpbb_modules" VALUES ('11', '1', '1', '', 'acp', '9', '100', '109', 'ACP_ATTACHMENTS', '', '');
INSERT INTO "phpbb_modules" VALUES ('12', '1', '1', '', 'acp', '0', '111', '166', 'ACP_CAT_USERGROUP', '', '');
INSERT INTO "phpbb_modules" VALUES ('13', '1', '1', '', 'acp', '12', '112', '145', 'ACP_CAT_USERS', '', '');
INSERT INTO "phpbb_modules" VALUES ('14', '1', '1', '', 'acp', '12', '146', '153', 'ACP_GROUPS', '', '');
INSERT INTO "phpbb_modules" VALUES ('15', '1', '1', '', 'acp', '12', '154', '165', 'ACP_USER_SECURITY', '', '');
INSERT INTO "phpbb_modules" VALUES ('16', '1', '1', '', 'acp', '0', '167', '216', 'ACP_CAT_PERMISSIONS', '', '');
INSERT INTO "phpbb_modules" VALUES ('17', '1', '1', '', 'acp', '16', '170', '179', 'ACP_GLOBAL_PERMISSIONS', '', '');
INSERT INTO "phpbb_modules" VALUES ('18', '1', '1', '', 'acp', '16', '180', '191', 'ACP_FORUM_BASED_PERMISSIONS', '', '');
INSERT INTO "phpbb_modules" VALUES ('19', '1', '1', '', 'acp', '16', '192', '201', 'ACP_PERMISSION_ROLES', '', '');
INSERT INTO "phpbb_modules" VALUES ('20', '1', '1', '', 'acp', '16', '202', '215', 'ACP_PERMISSION_MASKS', '', '');
INSERT INTO "phpbb_modules" VALUES ('21', '1', '1', '', 'acp', '0', '217', '232', 'ACP_CAT_STYLES', '', '');
INSERT INTO "phpbb_modules" VALUES ('22', '1', '1', '', 'acp', '21', '218', '223', 'ACP_STYLE_MANAGEMENT', '', '');
INSERT INTO "phpbb_modules" VALUES ('23', '1', '1', '', 'acp', '21', '224', '231', 'ACP_STYLE_COMPONENTS', '', '');
INSERT INTO "phpbb_modules" VALUES ('24', '1', '1', '', 'acp', '0', '233', '252', 'ACP_CAT_MAINTENANCE', '', '');
INSERT INTO "phpbb_modules" VALUES ('25', '1', '1', '', 'acp', '24', '234', '243', 'ACP_FORUM_LOGS', '', '');
INSERT INTO "phpbb_modules" VALUES ('26', '1', '1', '', 'acp', '24', '244', '251', 'ACP_CAT_DATABASE', '', '');
INSERT INTO "phpbb_modules" VALUES ('27', '1', '1', '', 'acp', '0', '253', '278', 'ACP_CAT_SYSTEM', '', '');
INSERT INTO "phpbb_modules" VALUES ('28', '1', '1', '', 'acp', '27', '254', '257', 'ACP_AUTOMATION', '', '');
INSERT INTO "phpbb_modules" VALUES ('29', '1', '1', '', 'acp', '27', '258', '269', 'ACP_GENERAL_TASKS', '', '');
INSERT INTO "phpbb_modules" VALUES ('30', '1', '1', '', 'acp', '27', '270', '277', 'ACP_MODULE_MANAGEMENT', '', '');
INSERT INTO "phpbb_modules" VALUES ('31', '1', '1', '', 'acp', '0', '279', '280', 'ACP_CAT_DOT_MODS', '', '');
INSERT INTO "phpbb_modules" VALUES ('32', '1', '1', 'attachments', 'acp', '3', '19', '20', 'ACP_ATTACHMENT_SETTINGS', 'attach', 'acl_a_attach');
INSERT INTO "phpbb_modules" VALUES ('33', '1', '1', 'attachments', 'acp', '11', '101', '102', 'ACP_ATTACHMENT_SETTINGS', 'attach', 'acl_a_attach');
INSERT INTO "phpbb_modules" VALUES ('34', '1', '1', 'attachments', 'acp', '11', '103', '104', 'ACP_MANAGE_EXTENSIONS', 'extensions', 'acl_a_attach');
INSERT INTO "phpbb_modules" VALUES ('35', '1', '1', 'attachments', 'acp', '11', '105', '106', 'ACP_EXTENSION_GROUPS', 'ext_groups', 'acl_a_attach');
INSERT INTO "phpbb_modules" VALUES ('36', '1', '1', 'attachments', 'acp', '11', '107', '108', 'ACP_ORPHAN_ATTACHMENTS', 'orphan', 'acl_a_attach');
INSERT INTO "phpbb_modules" VALUES ('37', '1', '1', 'ban', 'acp', '15', '155', '156', 'ACP_BAN_EMAILS', 'email', 'acl_a_ban');
INSERT INTO "phpbb_modules" VALUES ('38', '1', '1', 'ban', 'acp', '15', '157', '158', 'ACP_BAN_IPS', 'ip', 'acl_a_ban');
INSERT INTO "phpbb_modules" VALUES ('39', '1', '1', 'ban', 'acp', '15', '159', '160', 'ACP_BAN_USERNAMES', 'user', 'acl_a_ban');
INSERT INTO "phpbb_modules" VALUES ('40', '1', '1', 'bbcodes', 'acp', '10', '87', '88', 'ACP_BBCODES', 'bbcodes', 'acl_a_bbcode');
INSERT INTO "phpbb_modules" VALUES ('41', '1', '1', 'board', 'acp', '3', '21', '22', 'ACP_BOARD_SETTINGS', 'settings', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('42', '1', '1', 'board', 'acp', '3', '23', '24', 'ACP_BOARD_FEATURES', 'features', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('43', '1', '1', 'board', 'acp', '3', '25', '26', 'ACP_AVATAR_SETTINGS', 'avatar', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('44', '1', '1', 'board', 'acp', '3', '27', '28', 'ACP_MESSAGE_SETTINGS', 'message', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('45', '1', '1', 'board', 'acp', '10', '89', '90', 'ACP_MESSAGE_SETTINGS', 'message', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('46', '1', '1', 'board', 'acp', '3', '29', '30', 'ACP_POST_SETTINGS', 'post', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('47', '1', '1', 'board', 'acp', '10', '91', '92', 'ACP_POST_SETTINGS', 'post', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('48', '1', '1', 'board', 'acp', '3', '31', '32', 'ACP_SIGNATURE_SETTINGS', 'signature', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('49', '1', '1', 'board', 'acp', '3', '33', '34', 'ACP_FEED_SETTINGS', 'feed', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('50', '1', '1', 'board', 'acp', '3', '35', '36', 'ACP_REGISTER_SETTINGS', 'registration', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('51', '1', '1', 'board', 'acp', '4', '43', '44', 'ACP_AUTH_SETTINGS', 'auth', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('52', '1', '1', 'board', 'acp', '4', '45', '46', 'ACP_EMAIL_SETTINGS', 'email', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('53', '1', '1', 'board', 'acp', '5', '51', '52', 'ACP_COOKIE_SETTINGS', 'cookie', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('54', '1', '1', 'board', 'acp', '5', '53', '54', 'ACP_SERVER_SETTINGS', 'server', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('55', '1', '1', 'board', 'acp', '5', '55', '56', 'ACP_SECURITY_SETTINGS', 'security', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('56', '1', '1', 'board', 'acp', '5', '57', '58', 'ACP_LOAD_SETTINGS', 'load', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('57', '1', '1', 'bots', 'acp', '29', '259', '260', 'ACP_BOTS', 'bots', 'acl_a_bots');
INSERT INTO "phpbb_modules" VALUES ('58', '1', '1', 'captcha', 'acp', '3', '37', '38', 'ACP_VC_SETTINGS', 'visual', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('59', '1', '0', 'captcha', 'acp', '3', '39', '40', 'ACP_VC_CAPTCHA_DISPLAY', 'img', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('60', '1', '1', 'database', 'acp', '26', '245', '246', 'ACP_BACKUP', 'backup', 'acl_a_backup');
INSERT INTO "phpbb_modules" VALUES ('61', '1', '1', 'database', 'acp', '26', '247', '248', 'ACP_RESTORE', 'restore', 'acl_a_backup');
INSERT INTO "phpbb_modules" VALUES ('62', '1', '1', 'disallow', 'acp', '15', '161', '162', 'ACP_DISALLOW_USERNAMES', 'usernames', 'acl_a_names');
INSERT INTO "phpbb_modules" VALUES ('63', '1', '1', 'email', 'acp', '29', '261', '262', 'ACP_MASS_EMAIL', 'email', 'acl_a_email && cfg_email_enable');
INSERT INTO "phpbb_modules" VALUES ('64', '1', '1', 'forums', 'acp', '7', '67', '68', 'ACP_MANAGE_FORUMS', 'manage', 'acl_a_forum');
INSERT INTO "phpbb_modules" VALUES ('65', '1', '1', 'groups', 'acp', '14', '147', '148', 'ACP_GROUPS_MANAGE', 'manage', 'acl_a_group');
INSERT INTO "phpbb_modules" VALUES ('66', '1', '1', 'icons', 'acp', '10', '93', '94', 'ACP_ICONS', 'icons', 'acl_a_icons');
INSERT INTO "phpbb_modules" VALUES ('67', '1', '1', 'icons', 'acp', '10', '95', '96', 'ACP_SMILIES', 'smilies', 'acl_a_icons');
INSERT INTO "phpbb_modules" VALUES ('68', '1', '1', 'inactive', 'acp', '13', '115', '116', 'ACP_INACTIVE_USERS', 'list', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('69', '1', '1', 'jabber', 'acp', '4', '47', '48', 'ACP_JABBER_SETTINGS', 'settings', 'acl_a_jabber');
INSERT INTO "phpbb_modules" VALUES ('70', '1', '1', 'language', 'acp', '29', '263', '264', 'ACP_LANGUAGE_PACKS', 'lang_packs', 'acl_a_language');
INSERT INTO "phpbb_modules" VALUES ('71', '1', '1', 'logs', 'acp', '25', '235', '236', 'ACP_ADMIN_LOGS', 'admin', 'acl_a_viewlogs');
INSERT INTO "phpbb_modules" VALUES ('72', '1', '1', 'logs', 'acp', '25', '237', '238', 'ACP_MOD_LOGS', 'mod', 'acl_a_viewlogs');
INSERT INTO "phpbb_modules" VALUES ('73', '1', '1', 'logs', 'acp', '25', '239', '240', 'ACP_USERS_LOGS', 'users', 'acl_a_viewlogs');
INSERT INTO "phpbb_modules" VALUES ('74', '1', '1', 'logs', 'acp', '25', '241', '242', 'ACP_CRITICAL_LOGS', 'critical', 'acl_a_viewlogs');
INSERT INTO "phpbb_modules" VALUES ('75', '1', '1', 'main', 'acp', '1', '2', '3', 'ACP_INDEX', 'main', '');
INSERT INTO "phpbb_modules" VALUES ('76', '1', '1', 'modules', 'acp', '30', '271', '272', 'ACP', 'acp', 'acl_a_modules');
INSERT INTO "phpbb_modules" VALUES ('77', '1', '1', 'modules', 'acp', '30', '273', '274', 'UCP', 'ucp', 'acl_a_modules');
INSERT INTO "phpbb_modules" VALUES ('78', '1', '1', 'modules', 'acp', '30', '275', '276', 'MCP', 'mcp', 'acl_a_modules');
INSERT INTO "phpbb_modules" VALUES ('79', '1', '1', 'permission_roles', 'acp', '19', '193', '194', 'ACP_ADMIN_ROLES', 'admin_roles', 'acl_a_roles && acl_a_aauth');
INSERT INTO "phpbb_modules" VALUES ('80', '1', '1', 'permission_roles', 'acp', '19', '195', '196', 'ACP_USER_ROLES', 'user_roles', 'acl_a_roles && acl_a_uauth');
INSERT INTO "phpbb_modules" VALUES ('81', '1', '1', 'permission_roles', 'acp', '19', '197', '198', 'ACP_MOD_ROLES', 'mod_roles', 'acl_a_roles && acl_a_mauth');
INSERT INTO "phpbb_modules" VALUES ('82', '1', '1', 'permission_roles', 'acp', '19', '199', '200', 'ACP_FORUM_ROLES', 'forum_roles', 'acl_a_roles && acl_a_fauth');
INSERT INTO "phpbb_modules" VALUES ('83', '1', '1', 'permissions', 'acp', '16', '168', '169', 'ACP_PERMISSIONS', 'intro', 'acl_a_authusers || acl_a_authgroups || acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('84', '1', '0', 'permissions', 'acp', '20', '203', '204', 'ACP_PERMISSION_TRACE', 'trace', 'acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('85', '1', '1', 'permissions', 'acp', '18', '181', '182', 'ACP_FORUM_PERMISSIONS', 'setting_forum_local', 'acl_a_fauth && (acl_a_authusers || acl_a_authgroups)');
INSERT INTO "phpbb_modules" VALUES ('86', '1', '1', 'permissions', 'acp', '18', '183', '184', 'ACP_FORUM_PERMISSIONS_COPY', 'setting_forum_copy', 'acl_a_fauth && acl_a_authusers && acl_a_authgroups && acl_a_mauth');
INSERT INTO "phpbb_modules" VALUES ('87', '1', '1', 'permissions', 'acp', '18', '185', '186', 'ACP_FORUM_MODERATORS', 'setting_mod_local', 'acl_a_mauth && (acl_a_authusers || acl_a_authgroups)');
INSERT INTO "phpbb_modules" VALUES ('88', '1', '1', 'permissions', 'acp', '17', '171', '172', 'ACP_USERS_PERMISSIONS', 'setting_user_global', 'acl_a_authusers && (acl_a_aauth || acl_a_mauth || acl_a_uauth)');
INSERT INTO "phpbb_modules" VALUES ('89', '1', '1', 'permissions', 'acp', '13', '117', '118', 'ACP_USERS_PERMISSIONS', 'setting_user_global', 'acl_a_authusers && (acl_a_aauth || acl_a_mauth || acl_a_uauth)');
INSERT INTO "phpbb_modules" VALUES ('90', '1', '1', 'permissions', 'acp', '18', '187', '188', 'ACP_USERS_FORUM_PERMISSIONS', 'setting_user_local', 'acl_a_authusers && (acl_a_mauth || acl_a_fauth)');
INSERT INTO "phpbb_modules" VALUES ('91', '1', '1', 'permissions', 'acp', '13', '119', '120', 'ACP_USERS_FORUM_PERMISSIONS', 'setting_user_local', 'acl_a_authusers && (acl_a_mauth || acl_a_fauth)');
INSERT INTO "phpbb_modules" VALUES ('92', '1', '1', 'permissions', 'acp', '17', '173', '174', 'ACP_GROUPS_PERMISSIONS', 'setting_group_global', 'acl_a_authgroups && (acl_a_aauth || acl_a_mauth || acl_a_uauth)');
INSERT INTO "phpbb_modules" VALUES ('93', '1', '1', 'permissions', 'acp', '14', '149', '150', 'ACP_GROUPS_PERMISSIONS', 'setting_group_global', 'acl_a_authgroups && (acl_a_aauth || acl_a_mauth || acl_a_uauth)');
INSERT INTO "phpbb_modules" VALUES ('94', '1', '1', 'permissions', 'acp', '18', '189', '190', 'ACP_GROUPS_FORUM_PERMISSIONS', 'setting_group_local', 'acl_a_authgroups && (acl_a_mauth || acl_a_fauth)');
INSERT INTO "phpbb_modules" VALUES ('95', '1', '1', 'permissions', 'acp', '14', '151', '152', 'ACP_GROUPS_FORUM_PERMISSIONS', 'setting_group_local', 'acl_a_authgroups && (acl_a_mauth || acl_a_fauth)');
INSERT INTO "phpbb_modules" VALUES ('96', '1', '1', 'permissions', 'acp', '17', '175', '176', 'ACP_ADMINISTRATORS', 'setting_admin_global', 'acl_a_aauth && (acl_a_authusers || acl_a_authgroups)');
INSERT INTO "phpbb_modules" VALUES ('97', '1', '1', 'permissions', 'acp', '17', '177', '178', 'ACP_GLOBAL_MODERATORS', 'setting_mod_global', 'acl_a_mauth && (acl_a_authusers || acl_a_authgroups)');
INSERT INTO "phpbb_modules" VALUES ('98', '1', '1', 'permissions', 'acp', '20', '205', '206', 'ACP_VIEW_ADMIN_PERMISSIONS', 'view_admin_global', 'acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('99', '1', '1', 'permissions', 'acp', '20', '207', '208', 'ACP_VIEW_USER_PERMISSIONS', 'view_user_global', 'acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('100', '1', '1', 'permissions', 'acp', '20', '209', '210', 'ACP_VIEW_GLOBAL_MOD_PERMISSIONS', 'view_mod_global', 'acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('101', '1', '1', 'permissions', 'acp', '20', '211', '212', 'ACP_VIEW_FORUM_MOD_PERMISSIONS', 'view_mod_local', 'acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('102', '1', '1', 'permissions', 'acp', '20', '213', '214', 'ACP_VIEW_FORUM_PERMISSIONS', 'view_forum_local', 'acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('103', '1', '1', 'php_info', 'acp', '29', '265', '266', 'ACP_PHP_INFO', 'info', 'acl_a_phpinfo');
INSERT INTO "phpbb_modules" VALUES ('104', '1', '1', 'profile', 'acp', '13', '121', '122', 'ACP_CUSTOM_PROFILE_FIELDS', 'profile', 'acl_a_profile');
INSERT INTO "phpbb_modules" VALUES ('105', '1', '1', 'prune', 'acp', '7', '69', '70', 'ACP_PRUNE_FORUMS', 'forums', 'acl_a_prune');
INSERT INTO "phpbb_modules" VALUES ('106', '1', '1', 'prune', 'acp', '15', '163', '164', 'ACP_PRUNE_USERS', 'users', 'acl_a_userdel');
INSERT INTO "phpbb_modules" VALUES ('107', '1', '1', 'ranks', 'acp', '13', '123', '124', 'ACP_MANAGE_RANKS', 'ranks', 'acl_a_ranks');
INSERT INTO "phpbb_modules" VALUES ('108', '1', '1', 'reasons', 'acp', '29', '267', '268', 'ACP_MANAGE_REASONS', 'main', 'acl_a_reasons');
INSERT INTO "phpbb_modules" VALUES ('109', '1', '1', 'search', 'acp', '5', '59', '60', 'ACP_SEARCH_SETTINGS', 'settings', 'acl_a_search');
INSERT INTO "phpbb_modules" VALUES ('110', '1', '1', 'search', 'acp', '26', '249', '250', 'ACP_SEARCH_INDEX', 'index', 'acl_a_search');
INSERT INTO "phpbb_modules" VALUES ('111', '1', '1', 'send_statistics', 'acp', '5', '61', '62', 'ACP_SEND_STATISTICS', 'send_statistics', 'acl_a_server');
INSERT INTO "phpbb_modules" VALUES ('112', '1', '1', 'styles', 'acp', '22', '219', '220', 'ACP_STYLES', 'style', 'acl_a_styles');
INSERT INTO "phpbb_modules" VALUES ('113', '1', '1', 'styles', 'acp', '23', '225', '226', 'ACP_TEMPLATES', 'template', 'acl_a_styles');
INSERT INTO "phpbb_modules" VALUES ('114', '1', '1', 'styles', 'acp', '23', '227', '228', 'ACP_THEMES', 'theme', 'acl_a_styles');
INSERT INTO "phpbb_modules" VALUES ('115', '1', '1', 'styles', 'acp', '23', '229', '230', 'ACP_IMAGESETS', 'imageset', 'acl_a_styles');
INSERT INTO "phpbb_modules" VALUES ('116', '1', '1', 'update', 'acp', '28', '255', '256', 'ACP_VERSION_CHECK', 'version_check', 'acl_a_board');
INSERT INTO "phpbb_modules" VALUES ('117', '1', '1', 'users', 'acp', '13', '113', '114', 'ACP_MANAGE_USERS', 'overview', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('118', '1', '0', 'users', 'acp', '13', '125', '126', 'ACP_USER_FEEDBACK', 'feedback', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('119', '1', '0', 'users', 'acp', '13', '127', '128', 'ACP_USER_WARNINGS', 'warnings', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('120', '1', '0', 'users', 'acp', '13', '129', '130', 'ACP_USER_PROFILE', 'profile', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('121', '1', '0', 'users', 'acp', '13', '131', '132', 'ACP_USER_PREFS', 'prefs', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('122', '1', '0', 'users', 'acp', '13', '133', '134', 'ACP_USER_AVATAR', 'avatar', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('123', '1', '0', 'users', 'acp', '13', '135', '136', 'ACP_USER_RANK', 'rank', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('124', '1', '0', 'users', 'acp', '13', '137', '138', 'ACP_USER_SIG', 'sig', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('125', '1', '0', 'users', 'acp', '13', '139', '140', 'ACP_USER_GROUPS', 'groups', 'acl_a_user && acl_a_group');
INSERT INTO "phpbb_modules" VALUES ('126', '1', '0', 'users', 'acp', '13', '141', '142', 'ACP_USER_PERM', 'perm', 'acl_a_user && acl_a_viewauth');
INSERT INTO "phpbb_modules" VALUES ('127', '1', '0', 'users', 'acp', '13', '143', '144', 'ACP_USER_ATTACH', 'attach', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('128', '1', '1', 'words', 'acp', '10', '97', '98', 'ACP_WORDS', 'words', 'acl_a_words');
INSERT INTO "phpbb_modules" VALUES ('129', '1', '1', 'users', 'acp', '2', '5', '6', 'ACP_MANAGE_USERS', 'overview', 'acl_a_user');
INSERT INTO "phpbb_modules" VALUES ('130', '1', '1', 'groups', 'acp', '2', '7', '8', 'ACP_GROUPS_MANAGE', 'manage', 'acl_a_group');
INSERT INTO "phpbb_modules" VALUES ('131', '1', '1', 'forums', 'acp', '2', '9', '10', 'ACP_MANAGE_FORUMS', 'manage', 'acl_a_forum');
INSERT INTO "phpbb_modules" VALUES ('132', '1', '1', 'logs', 'acp', '2', '11', '12', 'ACP_MOD_LOGS', 'mod', 'acl_a_viewlogs');
INSERT INTO "phpbb_modules" VALUES ('133', '1', '1', 'bots', 'acp', '2', '13', '14', 'ACP_BOTS', 'bots', 'acl_a_bots');
INSERT INTO "phpbb_modules" VALUES ('134', '1', '1', 'php_info', 'acp', '2', '15', '16', 'ACP_PHP_INFO', 'info', 'acl_a_phpinfo');
INSERT INTO "phpbb_modules" VALUES ('135', '1', '1', 'permissions', 'acp', '8', '73', '74', 'ACP_FORUM_PERMISSIONS', 'setting_forum_local', 'acl_a_fauth && (acl_a_authusers || acl_a_authgroups)');
INSERT INTO "phpbb_modules" VALUES ('136', '1', '1', 'permissions', 'acp', '8', '75', '76', 'ACP_FORUM_PERMISSIONS_COPY', 'setting_forum_copy', 'acl_a_fauth && acl_a_authusers && acl_a_authgroups && acl_a_mauth');
INSERT INTO "phpbb_modules" VALUES ('137', '1', '1', 'permissions', 'acp', '8', '77', '78', 'ACP_FORUM_MODERATORS', 'setting_mod_local', 'acl_a_mauth && (acl_a_authusers || acl_a_authgroups)');
INSERT INTO "phpbb_modules" VALUES ('138', '1', '1', 'permissions', 'acp', '8', '79', '80', 'ACP_USERS_FORUM_PERMISSIONS', 'setting_user_local', 'acl_a_authusers && (acl_a_mauth || acl_a_fauth)');
INSERT INTO "phpbb_modules" VALUES ('139', '1', '1', 'permissions', 'acp', '8', '81', '82', 'ACP_GROUPS_FORUM_PERMISSIONS', 'setting_group_local', 'acl_a_authgroups && (acl_a_mauth || acl_a_fauth)');
INSERT INTO "phpbb_modules" VALUES ('140', '1', '1', '', 'mcp', '0', '1', '10', 'MCP_MAIN', '', '');
INSERT INTO "phpbb_modules" VALUES ('141', '1', '1', '', 'mcp', '0', '11', '18', 'MCP_QUEUE', '', '');
INSERT INTO "phpbb_modules" VALUES ('142', '1', '1', '', 'mcp', '0', '19', '32', 'MCP_REPORTS', '', '');
INSERT INTO "phpbb_modules" VALUES ('143', '1', '1', '', 'mcp', '0', '33', '38', 'MCP_NOTES', '', '');
INSERT INTO "phpbb_modules" VALUES ('144', '1', '1', '', 'mcp', '0', '39', '48', 'MCP_WARN', '', '');
INSERT INTO "phpbb_modules" VALUES ('145', '1', '1', '', 'mcp', '0', '49', '56', 'MCP_LOGS', '', '');
INSERT INTO "phpbb_modules" VALUES ('146', '1', '1', '', 'mcp', '0', '57', '64', 'MCP_BAN', '', '');
INSERT INTO "phpbb_modules" VALUES ('147', '1', '1', 'ban', 'mcp', '146', '58', '59', 'MCP_BAN_USERNAMES', 'user', 'acl_m_ban');
INSERT INTO "phpbb_modules" VALUES ('148', '1', '1', 'ban', 'mcp', '146', '60', '61', 'MCP_BAN_IPS', 'ip', 'acl_m_ban');
INSERT INTO "phpbb_modules" VALUES ('149', '1', '1', 'ban', 'mcp', '146', '62', '63', 'MCP_BAN_EMAILS', 'email', 'acl_m_ban');
INSERT INTO "phpbb_modules" VALUES ('150', '1', '1', 'logs', 'mcp', '145', '50', '51', 'MCP_LOGS_FRONT', 'front', 'acl_m_ || aclf_m_');
INSERT INTO "phpbb_modules" VALUES ('151', '1', '1', 'logs', 'mcp', '145', '52', '53', 'MCP_LOGS_FORUM_VIEW', 'forum_logs', 'acl_m_,$id');
INSERT INTO "phpbb_modules" VALUES ('152', '1', '1', 'logs', 'mcp', '145', '54', '55', 'MCP_LOGS_TOPIC_VIEW', 'topic_logs', 'acl_m_,$id');
INSERT INTO "phpbb_modules" VALUES ('153', '1', '1', 'main', 'mcp', '140', '2', '3', 'MCP_MAIN_FRONT', 'front', '');
INSERT INTO "phpbb_modules" VALUES ('154', '1', '1', 'main', 'mcp', '140', '4', '5', 'MCP_MAIN_FORUM_VIEW', 'forum_view', 'acl_m_,$id');
INSERT INTO "phpbb_modules" VALUES ('155', '1', '1', 'main', 'mcp', '140', '6', '7', 'MCP_MAIN_TOPIC_VIEW', 'topic_view', 'acl_m_,$id');
INSERT INTO "phpbb_modules" VALUES ('156', '1', '1', 'main', 'mcp', '140', '8', '9', 'MCP_MAIN_POST_DETAILS', 'post_details', 'acl_m_,$id || (!$id && aclf_m_)');
INSERT INTO "phpbb_modules" VALUES ('157', '1', '1', 'notes', 'mcp', '143', '34', '35', 'MCP_NOTES_FRONT', 'front', '');
INSERT INTO "phpbb_modules" VALUES ('158', '1', '1', 'notes', 'mcp', '143', '36', '37', 'MCP_NOTES_USER', 'user_notes', '');
INSERT INTO "phpbb_modules" VALUES ('159', '1', '1', 'pm_reports', 'mcp', '142', '20', '21', 'MCP_PM_REPORTS_OPEN', 'pm_reports', 'aclf_m_report');
INSERT INTO "phpbb_modules" VALUES ('160', '1', '1', 'pm_reports', 'mcp', '142', '22', '23', 'MCP_PM_REPORTS_CLOSED', 'pm_reports_closed', 'aclf_m_report');
INSERT INTO "phpbb_modules" VALUES ('161', '1', '1', 'pm_reports', 'mcp', '142', '24', '25', 'MCP_PM_REPORT_DETAILS', 'pm_report_details', 'aclf_m_report');
INSERT INTO "phpbb_modules" VALUES ('162', '1', '1', 'queue', 'mcp', '141', '12', '13', 'MCP_QUEUE_UNAPPROVED_TOPICS', 'unapproved_topics', 'aclf_m_approve');
INSERT INTO "phpbb_modules" VALUES ('163', '1', '1', 'queue', 'mcp', '141', '14', '15', 'MCP_QUEUE_UNAPPROVED_POSTS', 'unapproved_posts', 'aclf_m_approve');
INSERT INTO "phpbb_modules" VALUES ('164', '1', '1', 'queue', 'mcp', '141', '16', '17', 'MCP_QUEUE_APPROVE_DETAILS', 'approve_details', 'acl_m_approve,$id || (!$id && aclf_m_approve)');
INSERT INTO "phpbb_modules" VALUES ('165', '1', '1', 'reports', 'mcp', '142', '26', '27', 'MCP_REPORTS_OPEN', 'reports', 'aclf_m_report');
INSERT INTO "phpbb_modules" VALUES ('166', '1', '1', 'reports', 'mcp', '142', '28', '29', 'MCP_REPORTS_CLOSED', 'reports_closed', 'aclf_m_report');
INSERT INTO "phpbb_modules" VALUES ('167', '1', '1', 'reports', 'mcp', '142', '30', '31', 'MCP_REPORT_DETAILS', 'report_details', 'acl_m_report,$id || (!$id && aclf_m_report)');
INSERT INTO "phpbb_modules" VALUES ('168', '1', '1', 'warn', 'mcp', '144', '40', '41', 'MCP_WARN_FRONT', 'front', 'aclf_m_warn');
INSERT INTO "phpbb_modules" VALUES ('169', '1', '1', 'warn', 'mcp', '144', '42', '43', 'MCP_WARN_LIST', 'list', 'aclf_m_warn');
INSERT INTO "phpbb_modules" VALUES ('170', '1', '1', 'warn', 'mcp', '144', '44', '45', 'MCP_WARN_USER', 'warn_user', 'aclf_m_warn');
INSERT INTO "phpbb_modules" VALUES ('171', '1', '1', 'warn', 'mcp', '144', '46', '47', 'MCP_WARN_POST', 'warn_post', 'acl_m_warn && acl_f_read,$id');
INSERT INTO "phpbb_modules" VALUES ('172', '1', '1', '', 'ucp', '0', '1', '12', 'UCP_MAIN', '', '');
INSERT INTO "phpbb_modules" VALUES ('173', '1', '1', '', 'ucp', '0', '13', '22', 'UCP_PROFILE', '', '');
INSERT INTO "phpbb_modules" VALUES ('174', '1', '1', '', 'ucp', '0', '23', '30', 'UCP_PREFS', '', '');
INSERT INTO "phpbb_modules" VALUES ('175', '1', '1', '', 'ucp', '0', '31', '42', 'UCP_PM', '', '');
INSERT INTO "phpbb_modules" VALUES ('176', '1', '1', '', 'ucp', '0', '43', '48', 'UCP_USERGROUPS', '', '');
INSERT INTO "phpbb_modules" VALUES ('177', '1', '1', '', 'ucp', '0', '49', '54', 'UCP_ZEBRA', '', '');
INSERT INTO "phpbb_modules" VALUES ('178', '1', '1', 'attachments', 'ucp', '172', '10', '11', 'UCP_MAIN_ATTACHMENTS', 'attachments', 'acl_u_attach');
INSERT INTO "phpbb_modules" VALUES ('179', '1', '1', 'groups', 'ucp', '176', '44', '45', 'UCP_USERGROUPS_MEMBER', 'membership', '');
INSERT INTO "phpbb_modules" VALUES ('180', '1', '1', 'groups', 'ucp', '176', '46', '47', 'UCP_USERGROUPS_MANAGE', 'manage', '');
INSERT INTO "phpbb_modules" VALUES ('181', '1', '1', 'main', 'ucp', '172', '2', '3', 'UCP_MAIN_FRONT', 'front', '');
INSERT INTO "phpbb_modules" VALUES ('182', '1', '1', 'main', 'ucp', '172', '4', '5', 'UCP_MAIN_SUBSCRIBED', 'subscribed', '');
INSERT INTO "phpbb_modules" VALUES ('183', '1', '1', 'main', 'ucp', '172', '6', '7', 'UCP_MAIN_BOOKMARKS', 'bookmarks', 'cfg_allow_bookmarks');
INSERT INTO "phpbb_modules" VALUES ('184', '1', '1', 'main', 'ucp', '172', '8', '9', 'UCP_MAIN_DRAFTS', 'drafts', '');
INSERT INTO "phpbb_modules" VALUES ('185', '1', '0', 'pm', 'ucp', '175', '32', '33', 'UCP_PM_VIEW', 'view', 'cfg_allow_privmsg');
INSERT INTO "phpbb_modules" VALUES ('186', '1', '1', 'pm', 'ucp', '175', '34', '35', 'UCP_PM_COMPOSE', 'compose', 'cfg_allow_privmsg');
INSERT INTO "phpbb_modules" VALUES ('187', '1', '1', 'pm', 'ucp', '175', '36', '37', 'UCP_PM_DRAFTS', 'drafts', 'cfg_allow_privmsg');
INSERT INTO "phpbb_modules" VALUES ('188', '1', '1', 'pm', 'ucp', '175', '38', '39', 'UCP_PM_OPTIONS', 'options', 'cfg_allow_privmsg');
INSERT INTO "phpbb_modules" VALUES ('189', '1', '0', 'pm', 'ucp', '175', '40', '41', 'UCP_PM_POPUP_TITLE', 'popup', 'cfg_allow_privmsg');
INSERT INTO "phpbb_modules" VALUES ('190', '1', '1', 'prefs', 'ucp', '174', '24', '25', 'UCP_PREFS_PERSONAL', 'personal', '');
INSERT INTO "phpbb_modules" VALUES ('191', '1', '1', 'prefs', 'ucp', '174', '26', '27', 'UCP_PREFS_POST', 'post', '');
INSERT INTO "phpbb_modules" VALUES ('192', '1', '1', 'prefs', 'ucp', '174', '28', '29', 'UCP_PREFS_VIEW', 'view', '');
INSERT INTO "phpbb_modules" VALUES ('193', '1', '1', 'profile', 'ucp', '173', '14', '15', 'UCP_PROFILE_PROFILE_INFO', 'profile_info', '');
INSERT INTO "phpbb_modules" VALUES ('194', '1', '1', 'profile', 'ucp', '173', '16', '17', 'UCP_PROFILE_SIGNATURE', 'signature', '');
INSERT INTO "phpbb_modules" VALUES ('195', '1', '1', 'profile', 'ucp', '173', '18', '19', 'UCP_PROFILE_AVATAR', 'avatar', 'cfg_allow_avatar && (cfg_allow_avatar_local || cfg_allow_avatar_remote || cfg_allow_avatar_upload || cfg_allow_avatar_remote_upload)');
INSERT INTO "phpbb_modules" VALUES ('196', '1', '1', 'profile', 'ucp', '173', '20', '21', 'UCP_PROFILE_REG_DETAILS', 'reg_details', '');
INSERT INTO "phpbb_modules" VALUES ('197', '1', '1', 'zebra', 'ucp', '177', '50', '51', 'UCP_ZEBRA_FRIENDS', 'friends', '');
INSERT INTO "phpbb_modules" VALUES ('198', '1', '1', 'zebra', 'ucp', '177', '52', '53', 'UCP_ZEBRA_FOES', 'foes', '');
INSERT INTO "phpbb_modules" VALUES ('200', '1', '1', 'mobile', 'acp', '22', '221', '222', 'ACP_MOBILE', 'words', 'acl_a_styles');

-- ----------------------------
-- Records of phpbb_posts
-- ----------------------------
INSERT INTO "phpbb_posts" VALUES ('1', '1', '2', '2', '0', '127.0.0.1', '1297685283', '1', '0', '1', '1', '1', '1', '', 'Welcome to phpBB3', 'This is an example post in your phpBB3 installation. Everything seems to be working. You may delete this post if you like and continue to set up your board. During the installation process your first category and your first forum are assigned an appropriate set of permissions for the predefined usergroups administrators, bots, global moderators, guests, registered users and registered COPPA users. If you also choose to delete your first category and your first forum, do not forget to assign permissions for all these usergroups for all new categories and forums you create. It is recommended to rename your first category and your first forum and copy permissions from these while creating new categories and forums. Have fun!', '5dd683b17f641daf84c040bfefc58ce9', '0', '', '', '1', '0', '', '0', '0', '0');
INSERT INTO "phpbb_posts" VALUES ('2', '1', '2', '2', '0', '127.0.0.1', '1297838793', '1', '0', '1', '1', '1', '1', '', 'Re: Welcome to phpBB3', 'test1', '5a105e8b9d40e1329780d62ea2265d8a', '0', '', '2zf1b770', '1', '0', '', '0', '0', '0');

-- ----------------------------
-- Records of phpbb_ranks
-- ----------------------------
INSERT INTO "phpbb_ranks" VALUES ('1', 'Site Admin', '0', '1', '');

-- ----------------------------
-- Records of phpbb_reports_reasons
-- ----------------------------
INSERT INTO "phpbb_reports_reasons" VALUES ('1', 'warez', 'The post contains links to illegal or pirated software.', '1');
INSERT INTO "phpbb_reports_reasons" VALUES ('2', 'spam', 'The reported post has the only purpose to advertise for a website or another product.', '2');
INSERT INTO "phpbb_reports_reasons" VALUES ('3', 'off_topic', 'The reported post is off topic.', '3');
INSERT INTO "phpbb_reports_reasons" VALUES ('4', 'other', 'The reported post does not fit into any other category, please use the further information field.', '4');

-- ----------------------------
-- Records of phpbb_search_wordlist
-- ----------------------------
INSERT INTO "phpbb_search_wordlist" VALUES ('1', 'this', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('2', 'example', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('3', 'post', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('4', 'your', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('5', 'phpbb3', '0', '3');
INSERT INTO "phpbb_search_wordlist" VALUES ('6', 'installation', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('7', 'everything', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('8', 'seems', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('9', 'working', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('10', 'you', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('11', 'may', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('12', 'delete', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('13', 'like', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('14', 'and', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('15', 'continue', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('16', 'set', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('17', 'board', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('18', 'during', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('19', 'the', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('20', 'process', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('21', 'first', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('22', 'category', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('23', 'forum', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('24', 'are', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('25', 'assigned', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('26', 'appropriate', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('27', 'permissions', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('28', 'for', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('29', 'predefined', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('30', 'usergroups', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('31', 'administrators', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('32', 'bots', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('33', 'global', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('34', 'moderators', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('35', 'guests', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('36', 'registered', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('37', 'users', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('38', 'coppa', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('39', 'also', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('40', 'choose', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('41', 'not', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('42', 'forget', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('43', 'assign', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('44', 'all', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('45', 'these', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('46', 'new', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('47', 'categories', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('48', 'forums', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('49', 'create', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('50', 'recommended', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('51', 'rename', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('52', 'copy', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('53', 'from', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('54', 'while', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('55', 'creating', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('56', 'have', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('57', 'fun', '0', '1');
INSERT INTO "phpbb_search_wordlist" VALUES ('58', 'welcome', '0', '2');
INSERT INTO "phpbb_search_wordlist" VALUES ('59', 'test1', '0', '1');

-- ----------------------------
-- Records of phpbb_search_wordmatch
-- ----------------------------
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '1', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '2', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '3', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '4', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '5', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '5', '1');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '6', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '7', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '8', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '9', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '10', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '11', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '12', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '13', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '14', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '15', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '16', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '17', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '18', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '19', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '20', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '21', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '22', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '23', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '24', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '25', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '26', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '27', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '28', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '29', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '30', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '31', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '32', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '33', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '34', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '35', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '36', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '37', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '38', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '39', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '40', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '41', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '42', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '43', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '44', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '45', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '46', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '47', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '48', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '49', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '50', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '51', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '52', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '53', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '54', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '55', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '56', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '57', '0');
INSERT INTO "phpbb_search_wordmatch" VALUES ('1', '58', '1');
INSERT INTO "phpbb_search_wordmatch" VALUES ('2', '5', '1');
INSERT INTO "phpbb_search_wordmatch" VALUES ('2', '58', '1');
INSERT INTO "phpbb_search_wordmatch" VALUES ('2', '59', '0');

-- ----------------------------
-- Records of phpbb_sessions
-- ----------------------------


-- ----------------------------
-- Records of phpbb_smilies
-- ----------------------------
INSERT INTO "phpbb_smilies" VALUES ('1', ':D', 'Very Happy', 'icon_e_biggrin.gif', '15', '17', '1', '1');
INSERT INTO "phpbb_smilies" VALUES ('2', ':-D', 'Very Happy', 'icon_e_biggrin.gif', '15', '17', '2', '1');
INSERT INTO "phpbb_smilies" VALUES ('3', ':grin:', 'Very Happy', 'icon_e_biggrin.gif', '15', '17', '3', '1');
INSERT INTO "phpbb_smilies" VALUES ('4', ':)', 'Smile', 'icon_e_smile.gif', '15', '17', '4', '1');
INSERT INTO "phpbb_smilies" VALUES ('5', ':-)', 'Smile', 'icon_e_smile.gif', '15', '17', '5', '1');
INSERT INTO "phpbb_smilies" VALUES ('6', ':smile:', 'Smile', 'icon_e_smile.gif', '15', '17', '6', '1');
INSERT INTO "phpbb_smilies" VALUES ('7', ';)', 'Wink', 'icon_e_wink.gif', '15', '17', '7', '1');
INSERT INTO "phpbb_smilies" VALUES ('8', ';-)', 'Wink', 'icon_e_wink.gif', '15', '17', '8', '1');
INSERT INTO "phpbb_smilies" VALUES ('9', ':wink:', 'Wink', 'icon_e_wink.gif', '15', '17', '9', '1');
INSERT INTO "phpbb_smilies" VALUES ('10', ':(', 'Sad', 'icon_e_sad.gif', '15', '17', '10', '1');
INSERT INTO "phpbb_smilies" VALUES ('11', ':-(', 'Sad', 'icon_e_sad.gif', '15', '17', '11', '1');
INSERT INTO "phpbb_smilies" VALUES ('12', ':sad:', 'Sad', 'icon_e_sad.gif', '15', '17', '12', '1');
INSERT INTO "phpbb_smilies" VALUES ('13', ':o', 'Surprised', 'icon_e_surprised.gif', '15', '17', '13', '1');
INSERT INTO "phpbb_smilies" VALUES ('14', ':-o', 'Surprised', 'icon_e_surprised.gif', '15', '17', '14', '1');
INSERT INTO "phpbb_smilies" VALUES ('15', ':eek:', 'Surprised', 'icon_e_surprised.gif', '15', '17', '15', '1');
INSERT INTO "phpbb_smilies" VALUES ('16', ':shock:', 'Shocked', 'icon_eek.gif', '15', '17', '16', '1');
INSERT INTO "phpbb_smilies" VALUES ('17', ':?', 'Confused', 'icon_e_confused.gif', '15', '17', '17', '1');
INSERT INTO "phpbb_smilies" VALUES ('18', ':-?', 'Confused', 'icon_e_confused.gif', '15', '17', '18', '1');
INSERT INTO "phpbb_smilies" VALUES ('19', ':???:', 'Confused', 'icon_e_confused.gif', '15', '17', '19', '1');
INSERT INTO "phpbb_smilies" VALUES ('20', '8-)', 'Cool', 'icon_cool.gif', '15', '17', '20', '1');
INSERT INTO "phpbb_smilies" VALUES ('21', ':cool:', 'Cool', 'icon_cool.gif', '15', '17', '21', '1');
INSERT INTO "phpbb_smilies" VALUES ('22', ':lol:', 'Laughing', 'icon_lol.gif', '15', '17', '22', '1');
INSERT INTO "phpbb_smilies" VALUES ('23', ':x', 'Mad', 'icon_mad.gif', '15', '17', '23', '1');
INSERT INTO "phpbb_smilies" VALUES ('24', ':-x', 'Mad', 'icon_mad.gif', '15', '17', '24', '1');
INSERT INTO "phpbb_smilies" VALUES ('25', ':mad:', 'Mad', 'icon_mad.gif', '15', '17', '25', '1');
INSERT INTO "phpbb_smilies" VALUES ('26', ':P', 'Razz', 'icon_razz.gif', '15', '17', '26', '1');
INSERT INTO "phpbb_smilies" VALUES ('27', ':-P', 'Razz', 'icon_razz.gif', '15', '17', '27', '1');
INSERT INTO "phpbb_smilies" VALUES ('28', ':razz:', 'Razz', 'icon_razz.gif', '15', '17', '28', '1');
INSERT INTO "phpbb_smilies" VALUES ('29', ':oops:', 'Embarrassed', 'icon_redface.gif', '15', '17', '29', '1');
INSERT INTO "phpbb_smilies" VALUES ('30', ':cry:', 'Crying or Very Sad', 'icon_cry.gif', '15', '17', '30', '1');
INSERT INTO "phpbb_smilies" VALUES ('31', ':evil:', 'Evil or Very Mad', 'icon_evil.gif', '15', '17', '31', '1');
INSERT INTO "phpbb_smilies" VALUES ('32', ':twisted:', 'Twisted Evil', 'icon_twisted.gif', '15', '17', '32', '1');
INSERT INTO "phpbb_smilies" VALUES ('33', ':roll:', 'Rolling Eyes', 'icon_rolleyes.gif', '15', '17', '33', '1');
INSERT INTO "phpbb_smilies" VALUES ('34', ':!:', 'Exclamation', 'icon_exclaim.gif', '15', '17', '34', '1');
INSERT INTO "phpbb_smilies" VALUES ('35', ':?:', 'Question', 'icon_question.gif', '15', '17', '35', '1');
INSERT INTO "phpbb_smilies" VALUES ('36', ':idea:', 'Idea', 'icon_idea.gif', '15', '17', '36', '1');
INSERT INTO "phpbb_smilies" VALUES ('37', ':arrow:', 'Arrow', 'icon_arrow.gif', '15', '17', '37', '1');
INSERT INTO "phpbb_smilies" VALUES ('38', ':|', 'Neutral', 'icon_neutral.gif', '15', '17', '38', '1');
INSERT INTO "phpbb_smilies" VALUES ('39', ':-|', 'Neutral', 'icon_neutral.gif', '15', '17', '39', '1');
INSERT INTO "phpbb_smilies" VALUES ('40', ':mrgreen:', 'Mr. Green', 'icon_mrgreen.gif', '15', '17', '40', '1');
INSERT INTO "phpbb_smilies" VALUES ('41', ':geek:', 'Geek', 'icon_e_geek.gif', '17', '17', '41', '1');
INSERT INTO "phpbb_smilies" VALUES ('42', ':ugeek:', 'Uber Geek', 'icon_e_ugeek.gif', '17', '18', '42', '1');

-- ----------------------------
-- Records of phpbb_styles
-- ----------------------------
INSERT INTO "phpbb_styles" VALUES ('1', 'prosilver', '&copy; phpBB Group', '1', '1', '1', '1');

-- ----------------------------
-- Records of phpbb_styles_imageset
-- ----------------------------
INSERT INTO "phpbb_styles_imageset" VALUES ('1', 'prosilver', '&copy; phpBB Group', 'prosilver');
INSERT INTO "phpbb_styles_imageset" VALUES ('5', 'phpBB iPhone', '&copy; 2010 Callum Macrae', 'mobile');

-- ----------------------------
-- Records of phpbb_styles_imageset_data
-- ----------------------------
INSERT INTO "phpbb_styles_imageset_data" VALUES ('1', 'site_logo', 'site_logo.gif', '', '52', '139', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('2', 'forum_link', 'forum_link.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('3', 'forum_read', 'forum_read.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('4', 'forum_read_locked', 'forum_read_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('5', 'forum_read_subforum', 'forum_read_subforum.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('6', 'forum_unread', 'forum_unread.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('7', 'forum_unread_locked', 'forum_unread_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('8', 'forum_unread_subforum', 'forum_unread_subforum.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('9', 'topic_moved', 'topic_moved.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('10', 'topic_read', 'topic_read.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('11', 'topic_read_mine', 'topic_read_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('12', 'topic_read_hot', 'topic_read_hot.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('13', 'topic_read_hot_mine', 'topic_read_hot_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('14', 'topic_read_locked', 'topic_read_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('15', 'topic_read_locked_mine', 'topic_read_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('16', 'topic_unread', 'topic_unread.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('17', 'topic_unread_mine', 'topic_unread_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('18', 'topic_unread_hot', 'topic_unread_hot.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('19', 'topic_unread_hot_mine', 'topic_unread_hot_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('20', 'topic_unread_locked', 'topic_unread_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('21', 'topic_unread_locked_mine', 'topic_unread_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('22', 'sticky_read', 'sticky_read.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('23', 'sticky_read_mine', 'sticky_read_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('24', 'sticky_read_locked', 'sticky_read_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('25', 'sticky_read_locked_mine', 'sticky_read_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('26', 'sticky_unread', 'sticky_unread.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('27', 'sticky_unread_mine', 'sticky_unread_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('28', 'sticky_unread_locked', 'sticky_unread_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('29', 'sticky_unread_locked_mine', 'sticky_unread_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('30', 'announce_read', 'announce_read.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('31', 'announce_read_mine', 'announce_read_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('32', 'announce_read_locked', 'announce_read_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('33', 'announce_read_locked_mine', 'announce_read_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('34', 'announce_unread', 'announce_unread.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('35', 'announce_unread_mine', 'announce_unread_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('36', 'announce_unread_locked', 'announce_unread_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('37', 'announce_unread_locked_mine', 'announce_unread_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('38', 'global_read', 'announce_read.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('39', 'global_read_mine', 'announce_read_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('40', 'global_read_locked', 'announce_read_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('41', 'global_read_locked_mine', 'announce_read_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('42', 'global_unread', 'announce_unread.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('43', 'global_unread_mine', 'announce_unread_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('44', 'global_unread_locked', 'announce_unread_locked.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('45', 'global_unread_locked_mine', 'announce_unread_locked_mine.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('46', 'pm_read', 'topic_read.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('47', 'pm_unread', 'topic_unread.gif', '', '27', '27', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('48', 'icon_back_top', 'icon_back_top.gif', '', '11', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('49', 'icon_contact_aim', 'icon_contact_aim.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('50', 'icon_contact_email', 'icon_contact_email.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('51', 'icon_contact_icq', 'icon_contact_icq.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('52', 'icon_contact_jabber', 'icon_contact_jabber.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('53', 'icon_contact_msnm', 'icon_contact_msnm.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('54', 'icon_contact_www', 'icon_contact_www.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('55', 'icon_contact_yahoo', 'icon_contact_yahoo.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('56', 'icon_post_delete', 'icon_post_delete.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('57', 'icon_post_info', 'icon_post_info.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('58', 'icon_post_report', 'icon_post_report.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('59', 'icon_post_target', 'icon_post_target.gif', '', '9', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('60', 'icon_post_target_unread', 'icon_post_target_unread.gif', '', '9', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('61', 'icon_topic_attach', 'icon_topic_attach.gif', '', '10', '7', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('62', 'icon_topic_latest', 'icon_topic_latest.gif', '', '9', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('63', 'icon_topic_newest', 'icon_topic_newest.gif', '', '9', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('64', 'icon_topic_reported', 'icon_topic_reported.gif', '', '14', '16', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('65', 'icon_topic_unapproved', 'icon_topic_unapproved.gif', '', '14', '16', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('66', 'icon_user_warn', 'icon_user_warn.gif', '', '20', '20', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('67', 'subforum_read', 'subforum_read.gif', '', '9', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('68', 'subforum_unread', 'subforum_unread.gif', '', '9', '11', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('69', 'icon_contact_pm', 'icon_contact_pm.gif', 'de', '20', '28', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('70', 'icon_post_edit', 'icon_post_edit.gif', 'de', '20', '61', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('71', 'icon_post_quote', 'icon_post_quote.gif', 'de', '20', '65', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('72', 'icon_user_online', 'icon_user_online.gif', 'de', '58', '58', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('73', 'button_pm_forward', 'button_pm_forward.gif', 'de', '25', '119', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('74', 'button_pm_new', 'button_pm_new.gif', 'de', '25', '87', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('75', 'button_pm_reply', 'button_pm_reply.gif', 'de', '25', '102', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('76', 'button_topic_locked', 'button_topic_locked.gif', 'de', '25', '102', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('77', 'button_topic_new', 'button_topic_new.gif', 'de', '25', '119', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('78', 'button_topic_reply', 'button_topic_reply.gif', 'de', '25', '102', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('79', 'icon_contact_pm', 'icon_contact_pm.gif', 'en', '20', '28', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('80', 'icon_post_edit', 'icon_post_edit.gif', 'en', '20', '42', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('81', 'icon_post_quote', 'icon_post_quote.gif', 'en', '20', '54', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('82', 'icon_user_online', 'icon_user_online.gif', 'en', '58', '58', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('83', 'button_pm_forward', 'button_pm_forward.gif', 'en', '25', '96', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('84', 'button_pm_new', 'button_pm_new.gif', 'en', '25', '84', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('85', 'button_pm_reply', 'button_pm_reply.gif', 'en', '25', '96', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('86', 'button_topic_locked', 'button_topic_locked.gif', 'en', '25', '88', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('87', 'button_topic_new', 'button_topic_new.gif', 'en', '25', '96', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('88', 'button_topic_reply', 'button_topic_reply.gif', 'en', '25', '96', '1');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('275', 'site_logo', 'site_logo.gif', '', '52', '139', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('276', 'forum_link', 'forum_link.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('277', 'forum_read', 'forum_read.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('278', 'forum_read_locked', 'forum_read_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('279', 'forum_read_subforum', 'forum_read_subforum.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('280', 'forum_unread', 'forum_unread.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('281', 'forum_unread_locked', 'forum_unread_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('282', 'forum_unread_subforum', 'forum_unread_subforum.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('283', 'topic_moved', 'topic_moved.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('284', 'topic_read', 'topic_read.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('285', 'topic_read_mine', 'topic_read_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('286', 'topic_read_hot', 'topic_read_hot.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('287', 'topic_read_hot_mine', 'topic_read_hot_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('288', 'topic_read_locked', 'topic_read_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('289', 'topic_read_locked_mine', 'topic_read_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('290', 'topic_unread', 'topic_unread.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('291', 'topic_unread_mine', 'topic_unread_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('292', 'topic_unread_hot', 'topic_unread_hot.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('293', 'topic_unread_hot_mine', 'topic_unread_hot_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('294', 'topic_unread_locked', 'topic_unread_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('295', 'topic_unread_locked_mine', 'topic_unread_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('296', 'sticky_read', 'sticky_read.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('297', 'sticky_read_mine', 'sticky_read_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('298', 'sticky_read_locked', 'sticky_read_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('299', 'sticky_read_locked_mine', 'sticky_read_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('300', 'sticky_unread', 'sticky_unread.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('301', 'sticky_unread_mine', 'sticky_unread_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('302', 'sticky_unread_locked', 'sticky_unread_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('303', 'sticky_unread_locked_mine', 'sticky_unread_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('304', 'announce_read', 'announce_read.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('305', 'announce_read_mine', 'announce_read_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('306', 'announce_read_locked', 'announce_read_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('307', 'announce_read_locked_mine', 'announce_read_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('308', 'announce_unread', 'announce_unread.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('309', 'announce_unread_mine', 'announce_unread_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('310', 'announce_unread_locked', 'announce_unread_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('311', 'announce_unread_locked_mine', 'announce_unread_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('312', 'global_read', 'announce_read.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('313', 'global_read_mine', 'announce_read_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('314', 'global_read_locked', 'announce_read_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('315', 'global_read_locked_mine', 'announce_read_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('316', 'global_unread', 'announce_unread.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('317', 'global_unread_mine', 'announce_unread_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('318', 'global_unread_locked', 'announce_unread_locked.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('319', 'global_unread_locked_mine', 'announce_unread_locked_mine.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('320', 'subforum_read', 'subforum_read.gif', '', '9', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('321', 'subforum_unread', 'subforum_unread.gif', '', '9', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('322', 'pm_read', 'topic_read.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('323', 'pm_unread', 'topic_unread.gif', '', '27', '27', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('324', 'icon_back_top', 'icon_back_top.gif', '', '11', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('325', 'icon_contact_aim', 'icon_contact_aim.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('326', 'icon_contact_email', 'icon_contact_email.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('327', 'icon_contact_icq', 'icon_contact_icq.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('328', 'icon_contact_jabber', 'icon_contact_jabber.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('329', 'icon_contact_msnm', 'icon_contact_msnm.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('330', 'icon_contact_www', 'icon_contact_www.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('331', 'icon_contact_yahoo', 'icon_contact_yahoo.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('332', 'icon_post_delete', 'icon_post_delete.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('333', 'icon_post_info', 'icon_post_info.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('334', 'icon_post_report', 'icon_post_report.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('335', 'icon_post_target', 'icon_post_target.gif', '', '9', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('336', 'icon_post_target_unread', 'icon_post_target_unread.gif', '', '9', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('337', 'icon_topic_attach', 'icon_topic_attach.gif', '', '10', '7', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('338', 'icon_topic_latest', 'icon_topic_latest.gif', '', '9', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('339', 'icon_topic_newest', 'icon_topic_newest.gif', '', '9', '11', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('340', 'icon_topic_reported', 'icon_topic_reported.gif', '', '14', '16', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('341', 'icon_topic_unapproved', 'icon_topic_unapproved.gif', '', '14', '16', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('342', 'icon_user_warn', 'icon_user_warn.gif', '', '20', '20', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('343', 'icon_contact_aim', 'icon_contact_aim.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('344', 'icon_contact_email', 'icon_contact_email.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('345', 'icon_contact_icq', 'icon_contact_icq.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('346', 'icon_contact_jabber', 'icon_contact_jabber.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('347', 'icon_contact_msnm', 'icon_contact_msnm.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('348', 'icon_contact_pm', 'icon_contact_pm.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('349', 'icon_contact_yahoo', 'icon_contact_yahoo.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('350', 'icon_contact_www', 'icon_contact_www.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('351', 'icon_post_delete', 'icon_post_delete.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('352', 'icon_post_edit', 'icon_post_edit.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('353', 'icon_post_info', 'icon_post_info.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('354', 'icon_post_quote', 'icon_post_quote.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('355', 'icon_post_report', 'icon_post_report.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('356', 'icon_user_online', 'icon_user_online.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('357', 'icon_user_offline', 'icon_user_offline.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('358', 'icon_user_profile', 'icon_user_profile.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('359', 'icon_user_search', 'icon_user_search.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('360', 'icon_user_warn', 'icon_user_warn.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('361', 'button_pm_new', 'button_pm_new.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('362', 'button_pm_reply', 'button_pm_reply.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('363', 'button_topic_locked', 'button_topic_locked.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('364', 'button_topic_new', 'button_topic_new.gif', 'en', '0', '0', '5');
INSERT INTO "phpbb_styles_imageset_data" VALUES ('365', 'button_topic_reply', 'button_topic_reply.gif', 'en', '0', '0', '5');

-- ----------------------------
-- Records of phpbb_styles_template
-- ----------------------------
INSERT INTO "phpbb_styles_template" VALUES ('1', 'prosilver', '&copy; phpBB Group', 'prosilver', 'lNg=', '0', '0', '');
INSERT INTO "phpbb_styles_template" VALUES ('5', 'phpBB iPhone', '&copy; 2010 Callum Macrae', 'mobile', '+Ng=', '0', '0', '');

-- ----------------------------
-- Records of phpbb_styles_theme
-- ----------------------------
INSERT INTO "phpbb_styles_theme" VALUES ('1', 'prosilver', '&copy; phpBB Group', 'prosilver', '1', '1297936476', '/*  phpBB 3.0 Style Sheet
    --------------------------------------------------------------
	Style name:		proSilver
	Based on style:	proSilver (this is the default phpBB 3 style)
	Original author:	subBlue ( http://www.subBlue.com/ )
	Modified by:		
	
	Copyright 2006 phpBB Group ( http://www.phpbb.com/ )
    --------------------------------------------------------------
*/

/* BEGIN @include common.css */ 
 /* General proSilver Markup Styles
---------------------------------------- */

* {
	/* Reset browsers default margin, padding and font sizes */
	margin: 0;
	padding: 0;
}

html {
	font-size: 100%;
	/* Always show a scrollbar for short pages - stops the jump when the scrollbar appears. non-IE browsers */
	height: 101%;
}

body {
	/* Text-Sizing with ems: http://www.clagnut.com/blog/348/ */
	font-family: Verdana, Helvetica, Arial, sans-serif;
	color: #828282;
	background-color: #FFFFFF;
	/*font-size: 62.5%;			 This sets the default font size to be equivalent to 10px */
	font-size: 10px;
	margin: 0;
	padding: 12px 0;
}

h1 {
	/* Forum name */
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	margin-right: 200px;
	color: #FFFFFF;
	margin-top: 15px;
	font-weight: bold;
	font-size: 2em;
}

h2 {
	/* Forum header titles */
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight: normal;
	color: #3f3f3f;
	font-size: 2em;
	margin: 0.8em 0 0.2em 0;
}

h2.solo {
	margin-bottom: 1em;
}

h3 {
	/* Sub-headers (also used as post headers, but defined later) */
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	text-transform: uppercase;
	border-bottom: 1px solid #CCCCCC;
	margin-bottom: 3px;
	padding-bottom: 2px;
	font-size: 1.05em;
	color: #989898;
	margin-top: 20px;
}

h4 {
	/* Forum and topic list titles */
	font-family: "Trebuchet MS", Verdana, Helvetica, Arial, Sans-serif;
	font-size: 1.3em;
}

p {
	line-height: 1.3em;
	font-size: 1.1em;
	margin-bottom: 1.5em;
}

img {
	border-width: 0;
}

hr {
	/* Also see tweaks.css */
	border: 0 none #FFFFFF;
	border-top: 1px solid #CCCCCC;
	height: 1px;
	margin: 5px 0;
	display: block;
	clear: both;
}

hr.dashed {
	border-top: 1px dashed #CCCCCC;
	margin: 10px 0;
}

hr.divider {
	display: none;
}

p.right {
	text-align: right;
}

/* Main blocks
---------------------------------------- */
#wrap {
	padding: 0 20px;
	min-width: 650px;
}

#simple-wrap {
	padding: 6px 10px;
}

#page-body {
	margin: 4px 0;
	clear: both;
}

#page-footer {
	clear: both;
}

#page-footer h3 {
	margin-top: 20px;
}

#logo {
	float: left;
	width: auto;
	padding: 10px 13px 0 10px;
}

a#logo:hover {
	text-decoration: none;
}

/* Search box
--------------------------------------------- */
#search-box {
	color: #FFFFFF;
	position: relative;
	margin-top: 30px;
	margin-right: 5px;
	display: block;
	float: right;
	text-align: right;
	white-space: nowrap; /* For Opera */
}

#search-box #keywords {
	width: 95px;
	background-color: #FFF;
}

#search-box input {
	border: 1px solid #b0b0b0;
}

/* .button1 style defined later, just a few tweaks for the search button version */
#search-box input.button1 {
	padding: 1px 5px;
}

#search-box li {
	text-align: right;
	margin-top: 4px;
}

#search-box img {
	vertical-align: middle;
	margin-right: 3px;
}

/* Site description and logo */
#site-description {
	float: left;
	width: 70%;
}

#site-description h1 {
	margin-right: 0;
}

/* Round cornered boxes and backgrounds
---------------------------------------- */
.headerbar {
	background: #ebebeb none repeat-x 0 0;
	color: #FFFFFF;
	margin-bottom: 4px;
	padding: 0 5px;
}

.navbar {
	background-color: #ebebeb;
	padding: 0 10px;
}

.forabg {
	background: #b1b1b1 none repeat-x 0 0;
	margin-bottom: 4px;
	padding: 0 5px;
	clear: both;
}

.forumbg {
	background: #ebebeb none repeat-x 0 0;
	margin-bottom: 4px;
	padding: 0 5px;
	clear: both;
}

.panel {
	margin-bottom: 4px;
	padding: 0 10px;
	background-color: #f3f3f3;
	color: #3f3f3f;
}

.post {
	padding: 0 10px;
	margin-bottom: 4px;
	background-repeat: no-repeat;
	background-position: 100% 0;
}

.post:target .content {
	color: #000000;
}

.post:target h3 a {
	color: #000000;
}

.bg1	{ background-color: #f7f7f7;}
.bg2	{ background-color: #f2f2f2; }
.bg3	{ background-color: #ebebeb; }

.rowbg {
	margin: 5px 5px 2px 5px;
}

.ucprowbg {
	background-color: #e2e2e2;
}

.fieldsbg {
	/*border: 1px #DBDEE2 solid;*/
	background-color: #eaeaea;
}

span.corners-top, span.corners-bottom, span.corners-top span, span.corners-bottom span {
	font-size: 1px;
	line-height: 1px;
	display: block;
	height: 5px;
	background-repeat: no-repeat;
}

span.corners-top {
	background-image: none;
	background-position: 0 0;
	margin: 0 -5px;
}

span.corners-top span {
	background-image: none;
	background-position: 100% 0;
}

span.corners-bottom {
	background-image: none;
	background-position: 0 100%;
	margin: 0 -5px;
	clear: both;
}

span.corners-bottom span {
	background-image: none;
	background-position: 100% 100%;
}

.headbg span.corners-bottom {
	margin-bottom: -1px;
}

.post span.corners-top, .post span.corners-bottom, .panel span.corners-top, .panel span.corners-bottom, .navbar span.corners-top, .navbar span.corners-bottom {
	margin: 0 -10px;
}

.rules span.corners-top {
	margin: 0 -10px 5px -10px;
}

.rules span.corners-bottom {
	margin: 5px -10px 0 -10px;
}

/* Horizontal lists
----------------------------------------*/
ul.linklist {
	display: block;
	margin: 0;
}

ul.linklist li {
	display: block;
	list-style-type: none;
	float: left;
	width: auto;
	margin-right: 5px;
	font-size: 1.1em;
	line-height: 2.2em;
}

ul.linklist li.rightside, p.rightside {
	float: right;
	margin-right: 0;
	margin-left: 5px;
	text-align: right;
}

ul.navlinks {
	padding-bottom: 1px;
	margin-bottom: 1px;
	border-bottom: 1px solid #FFFFFF;
	font-weight: bold;
}

ul.leftside {
	float: left;
	margin-left: 0;
	margin-right: 5px;
	text-align: left;
}

ul.rightside {
	float: right;
	margin-left: 5px;
	margin-right: -5px;
	text-align: right;
}

/* Table styles
----------------------------------------*/
table.table1 {
	/* See tweaks.css */
}

#ucp-main table.table1 {
	padding: 2px;
}

table.table1 thead th {
	font-weight: normal;
	text-transform: uppercase;
	color: #FFFFFF;
	line-height: 1.3em;
	font-size: 1em;
	padding: 0 0 4px 3px;
}

table.table1 thead th span {
	padding-left: 7px;
}

table.table1 tbody tr {
	border: 1px solid #cfcfcf;
}

table.table1 tbody tr:hover, table.table1 tbody tr.hover {
	background-color: #f6f6f6;
	color: #000;
}

table.table1 td {
	color: #6a6a6a;
	font-size: 1.1em;
}

table.table1 tbody td {
	padding: 5px;
	border-top: 1px solid #FAFAFA;
}

table.table1 tbody th {
	padding: 5px;
	border-bottom: 1px solid #000000;
	text-align: left;
	color: #333333;
	background-color: #FFFFFF;
}

/* Specific column styles */
table.table1 .name		{ text-align: left; }
table.table1 .posts		{ text-align: center !important; width: 7%; }
table.table1 .joined	{ text-align: left; width: 15%; }
table.table1 .active	{ text-align: left; width: 15%; }
table.table1 .mark		{ text-align: center; width: 7%; }
table.table1 .info		{ text-align: left; width: 30%; }
table.table1 .info div	{ width: 100%; white-space: normal; overflow: hidden; }
table.table1 .autocol	{ line-height: 2em; white-space: nowrap; }
table.table1 thead .autocol { padding-left: 1em; }

table.table1 span.rank-img {
	float: right;
	width: auto;
}

table.info td {
	padding: 3px;
}

table.info tbody th {
	padding: 3px;
	text-align: right;
	vertical-align: top;
	color: #000000;
	font-weight: normal;
}

.forumbg table.table1 {
	margin: 0 -2px -1px -1px;
}

/* Misc layout styles
---------------------------------------- */
/* column[1-2] styles are containers for two column layouts 
   Also see tweaks.css */
.column1 {
	float: left;
	clear: left;
	width: 49%;
}

.column2 {
	float: right;
	clear: right;
	width: 49%;
}

/* General classes for placing floating blocks */
.left-box {
	float: left;
	width: auto;
	text-align: left;
}

.right-box {
	float: right;
	width: auto;
	text-align: right;
}

dl.details {
	/*font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;*/
	font-size: 1.1em;
}

dl.details dt {
	float: left;
	clear: left;
	width: 30%;
	text-align: right;
	color: #000000;
	display: block;
}

dl.details dd {
	margin-left: 0;
	padding-left: 5px;
	margin-bottom: 5px;
	color: #828282;
	float: left;
	width: 65%;
}

/* Pagination
---------------------------------------- */
.pagination {
	height: 1%; /* IE tweak (holly hack) */
	width: auto;
	text-align: right;
	margin-top: 5px;
	float: right;
}

.pagination span.page-sep {
	display: none;
}

li.pagination {
	margin-top: 0;
}

.pagination strong, .pagination b {
	font-weight: normal;
}

.pagination span strong {
	padding: 0 2px;
	margin: 0 2px;
	font-weight: normal;
	color: #FFFFFF;
	background-color: #bfbfbf;
	border: 1px solid #bfbfbf;
	font-size: 0.9em;
}

.pagination span a, .pagination span a:link, .pagination span a:visited, .pagination span a:active {
	font-weight: normal;
	text-decoration: none;
	color: #747474;
	margin: 0 2px;
	padding: 0 2px;
	background-color: #eeeeee;
	border: 1px solid #bababa;
	font-size: 0.9em;
	line-height: 1.5em;
}

.pagination span a:hover {
	border-color: #d2d2d2;
	background-color: #d2d2d2;
	color: #FFF;
	text-decoration: none;
}

.pagination img {
	vertical-align: middle;
}

/* Pagination in viewforum for multipage topics */
.row .pagination {
	display: block;
	float: right;
	width: auto;
	margin-top: 0;
	padding: 1px 0 1px 15px;
	font-size: 0.9em;
	background: none 0 50% no-repeat;
}

.row .pagination span a, li.pagination span a {
	background-color: #FFFFFF;
}

.row .pagination span a:hover, li.pagination span a:hover {
	background-color: #d2d2d2;
}

/* Miscellaneous styles
---------------------------------------- */
#forum-permissions {
	float: right;
	width: auto;
	padding-left: 5px;
	margin-left: 5px;
	margin-top: 10px;
	text-align: right;
}

.copyright {
	padding: 5px;
	text-align: center;
	color: #555555;
}

.small {
	font-size: 0.9em !important;
}

.titlespace {
	margin-bottom: 15px;
}

.headerspace {
	margin-top: 20px;
}

.error {
	color: #bcbcbc;
	font-weight: bold;
	font-size: 1em;
}

.reported {
	background-color: #f7f7f7;
}

li.reported:hover {
	background-color: #ececec;
}

div.rules {
	background-color: #ececec;
	color: #bcbcbc;
	padding: 0 10px;
	margin: 10px 0;
	font-size: 1.1em;
}

div.rules ul, div.rules ol {
	margin-left: 20px;
}

p.rules {
	background-color: #ececec;
	background-image: none;
	padding: 5px;
}

p.rules img {
	vertical-align: middle;
	padding-top: 5px;
}

p.rules a {
	vertical-align: middle;
	clear: both;
}

#top {
	position: absolute;
	top: -20px;
}

.clear {
	display: block;
	clear: both;
	font-size: 1px;
	line-height: 1px;
	background: transparent;
} 
 /* END @include common.css */ 

/* BEGIN @include links.css */ 
 /* proSilver Link Styles
---------------------------------------- */

/* Links adjustment to correctly display an order of rtl/ltr mixed content */
a {
	direction: ltr;
	unicode-bidi: embed;
}

a:link	{ color: #898989; text-decoration: none; }
a:visited	{ color: #898989; text-decoration: none; }
a:hover	{ color: #d3d3d3; text-decoration: underline; }
a:active	{ color: #d2d2d2; text-decoration: none; }

/* Coloured usernames */
.username-coloured {
	font-weight: bold;
	display: inline !important;
	padding: 0 !important;
}

/* Links on gradient backgrounds */
#search-box a:link, .navbg a:link, .forumbg .header a:link, .forabg .header a:link, th a:link {
	color: #FFFFFF;
	text-decoration: none;
}

#search-box a:visited, .navbg a:visited, .forumbg .header a:visited, .forabg .header a:visited, th a:visited {
	color: #FFFFFF;
	text-decoration: none;
}

#search-box a:hover, .navbg a:hover, .forumbg .header a:hover, .forabg .header a:hover, th a:hover {
	color: #ffffff;
	text-decoration: underline;
}

#search-box a:active, .navbg a:active, .forumbg .header a:active, .forabg .header a:active, th a:active {
	color: #ffffff;
	text-decoration: none;
}

/* Links for forum/topic lists */
a.forumtitle {
	font-family: "Trebuchet MS", Helvetica, Arial, Sans-serif;
	font-size: 1.2em;
	font-weight: bold;
	color: #898989;
	text-decoration: none;
}

/* a.forumtitle:visited { color: #898989; } */

a.forumtitle:hover {
	color: #bcbcbc;
	text-decoration: underline;
}

a.forumtitle:active {
	color: #898989;
}

a.topictitle {
	font-family: "Trebuchet MS", Helvetica, Arial, Sans-serif;
	font-size: 1.2em;
	font-weight: bold;
	color: #898989;
	text-decoration: none;
}

/* a.topictitle:visited { color: #d2d2d2; } */

a.topictitle:hover {
	color: #bcbcbc;
	text-decoration: underline;
}

a.topictitle:active {
	color: #898989;
}

/* Post body links */
.postlink {
	text-decoration: none;
	color: #d2d2d2;
	border-bottom: 1px solid #d2d2d2;
	padding-bottom: 0;
}

.postlink:visited {
	color: #bdbdbd;
	border-bottom-style: dotted;
	border-bottom-color: #666666;
}

.postlink:active {
	color: #d2d2d2;
}

.postlink:hover {
	background-color: #f6f6f6;
	text-decoration: none;
	color: #404040;
}

.signature a, .signature a:visited, .signature a:active, .signature a:hover {
	border: none;
	text-decoration: underline;
	background-color: transparent;
}

/* Profile links */
.postprofile a:link, .postprofile a:active, .postprofile a:visited, .postprofile dt.author a {
	font-weight: bold;
	color: #898989;
	text-decoration: none;
}

.postprofile a:hover, .postprofile dt.author a:hover {
	text-decoration: underline;
	color: #d3d3d3;
}


/* Profile searchresults */	
.search .postprofile a {
	color: #898989;
	text-decoration: none; 
	font-weight: normal;
}

.search .postprofile a:hover {
	color: #d3d3d3;
	text-decoration: underline; 
}

/* Back to top of page */
.back2top {
	clear: both;
	height: 11px;
	text-align: right;
}

a.top {
	background: none no-repeat top left;
	text-decoration: none;
	width: {IMG_ICON_BACK_TOP_WIDTH}px;
	height: {IMG_ICON_BACK_TOP_HEIGHT}px;
	display: block;
	float: right;
	overflow: hidden;
	letter-spacing: 1000px;
	text-indent: 11px;
}

a.top2 {
	background: none no-repeat 0 50%;
	text-decoration: none;
	padding-left: 15px;
}

/* Arrow links  */
a.up		{ background: none no-repeat left center; }
a.down		{ background: none no-repeat right center; }
a.left		{ background: none no-repeat 3px 60%; }
a.right		{ background: none no-repeat 95% 60%; }

a.up, a.up:link, a.up:active, a.up:visited {
	padding-left: 10px;
	text-decoration: none;
	border-bottom-width: 0;
}

a.up:hover {
	background-position: left top;
	background-color: transparent;
}

a.down, a.down:link, a.down:active, a.down:visited {
	padding-right: 10px;
}

a.down:hover {
	background-position: right bottom;
	text-decoration: none;
}

a.left, a.left:active, a.left:visited {
	padding-left: 12px;
}

a.left:hover {
	color: #d2d2d2;
	text-decoration: none;
	background-position: 0 60%;
}

a.right, a.right:active, a.right:visited {
	padding-right: 12px;
}

a.right:hover {
	color: #d2d2d2;
	text-decoration: none;
	background-position: 100% 60%;
}

/* invisible skip link, used for accessibility  */
.skiplink {
	position: absolute;
	left: -999px;
	width: 990px;
}

/* Feed icon in forumlist_body.html */
a.feed-icon-forum {
	float: right;
	margin: 3px;
} 
 /* END @include links.css */ 

/* BEGIN @include content.css */ 
 /* proSilver Content Styles
---------------------------------------- */

ul.topiclist {
	display: block;
	list-style-type: none;
	margin: 0;
}

ul.forums {
	background: #f9f9f9 none repeat-x 0 0;
}

ul.topiclist li {
	display: block;
	list-style-type: none;
	color: #777777;
	margin: 0;
}

ul.topiclist dl {
	position: relative;
}

ul.topiclist li.row dl {
	padding: 2px 0;
}

ul.topiclist dt {
	display: block;
	float: left;
	width: 50%;
	font-size: 1.1em;
	padding-left: 5px;
	padding-right: 5px;
}

ul.topiclist dd {
	display: block;
	float: left;
	border-left: 1px solid #FFFFFF;
	padding: 4px 0;
}

ul.topiclist dfn {
	/* Labels for post/view counts */
	position: absolute;
	left: -999px;
	width: 990px;
}

ul.topiclist li.row dt a.subforum {
	background-image: none;
	background-position: 0 50%;
	background-repeat: no-repeat;
	position: relative;
	white-space: nowrap;
	padding: 0 0 0 12px;
}

.forum-image {
	float: left;
	padding-top: 5px;
	margin-right: 5px;
}

li.row {
	border-top: 1px solid #FFFFFF;
	border-bottom: 1px solid #8f8f8f;
}

li.row strong {
	font-weight: normal;
	color: #000000;
}

li.row:hover {
	background-color: #f6f6f6;
}

li.row:hover dd {
	border-left-color: #CCCCCC;
}

li.header dt, li.header dd {
	line-height: 1em;
	border-left-width: 0;
	margin: 2px 0 4px 0;
	color: #FFFFFF;
	padding-top: 2px;
	padding-bottom: 2px;
	font-size: 1em;
	font-family: Arial, Helvetica, sans-serif;
	text-transform: uppercase;
}

li.header dt {
	font-weight: bold;
}

li.header dd {
	margin-left: 1px;
}

li.header dl.icon {
	min-height: 0;
}

li.header dl.icon dt {
	/* Tweak for headers alignment when folder icon used */
	padding-left: 0;
	padding-right: 50px;
}

/* Forum list column styles */
dl.icon {
	min-height: 35px;
	background-position: 10px 50%;		/* Position of folder icon */
	background-repeat: no-repeat;
}

dl.icon dt {
	padding-left: 45px;					/* Space for folder icon */
	background-repeat: no-repeat;
	background-position: 5px 95%;		/* Position of topic icon */
}

dd.posts, dd.topics, dd.views {
	width: 8%;
	text-align: center;
	line-height: 2.2em;
	font-size: 1.2em;
}

/* List in forum description */
dl.icon dt ol,
dl.icon dt ul {
	list-style-position: inside;
	margin-left: 1em;
}

dl.icon dt li {
	display: list-item;
	list-style-type: inherit;
}

dd.lastpost {
	width: 25%;
	font-size: 1.1em;
}

dd.redirect {
	font-size: 1.1em;
	line-height: 2.5em;
}

dd.moderation {
	font-size: 1.1em;
}

dd.lastpost span, ul.topiclist dd.searchby span, ul.topiclist dd.info span, ul.topiclist dd.time span, dd.redirect span, dd.moderation span {
	display: block;
	padding-left: 5px;
}

dd.time {
	width: auto;
	line-height: 200%;
	font-size: 1.1em;
}

dd.extra {
	width: 12%;
	line-height: 200%;
	text-align: center;
	font-size: 1.1em;
}

dd.mark {
	float: right !important;
	width: 9%;
	text-align: center;
	line-height: 200%;
	font-size: 1.2em;
}

dd.info {
	width: 30%;
}

dd.option {
	width: 15%;
	line-height: 200%;
	text-align: center;
	font-size: 1.1em;
}

dd.searchby {
	width: 47%;
	font-size: 1.1em;
	line-height: 1em;
}

ul.topiclist dd.searchextra {
	margin-left: 5px;
	padding: 0.2em 0;
	font-size: 1.1em;
	color: #333333;
	border-left: none;
	clear: both;
	width: 98%;
	overflow: hidden;
}

/* Container for post/reply buttons and pagination */
.topic-actions {
	margin-bottom: 3px;
	font-size: 1.1em;
	height: 28px;
	min-height: 28px;
}
div[class].topic-actions {
	height: auto;
}

/* Post body styles
----------------------------------------*/
.postbody {
	padding: 0;
	line-height: 1.48em;
	color: #333333;
	width: 76%;
	float: left;
	clear: both;
}

.postbody .ignore {
	font-size: 1.1em;
}

.postbody h3.first {
	/* The first post on the page uses this */
	font-size: 1.7em;
}

.postbody h3 {
	/* Postbody requires a different h3 format - so change it here */
	font-size: 1.5em;
	padding: 2px 0 0 0;
	margin: 0 0 0.3em 0 !important;
	text-transform: none;
	border: none;
	font-family: "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
	line-height: 125%;
}

.postbody h3 img {
	/* Also see tweaks.css */
	vertical-align: bottom;
}

.postbody .content {
	font-size: 1.3em;
}

.search .postbody {
	width: 68%
}

/* Topic review panel
----------------------------------------*/
#review {
	margin-top: 2em;
}

#topicreview {
	padding-right: 5px;
	overflow: auto;
	height: 300px;
}

#topicreview .postbody {
	width: auto;
	float: none;
	margin: 0;
	height: auto;
}

#topicreview .post {
	height: auto;
}

#topicreview h2 {
	border-bottom-width: 0;
}

.post-ignore .postbody {
	display: none;
}

/* MCP Post details
----------------------------------------*/
#post_details
{
	/* This will only work in IE7+, plus the others */
	overflow: auto;
	max-height: 300px;
}

#expand
{
	clear: both;
}

/* Content container styles
----------------------------------------*/
.content {
	min-height: 3em;
	overflow: hidden;
	line-height: 1.4em;
	font-family: "Lucida Grande", "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
	font-size: 1em;
	color: #333333;
	padding-bottom: 1px;
}

.content h2, .panel h2 {
	font-weight: normal;
	color: #989898;
	border-bottom: 1px solid #CCCCCC;
	font-size: 1.6em;
	margin-top: 0.5em;
	margin-bottom: 0.5em;
	padding-bottom: 0.5em;
}

.panel h3 {
	margin: 0.5em 0;
}

.panel p {
	font-size: 1.2em;
	margin-bottom: 1em;
	line-height: 1.4em;
}

.content p {
	font-family: "Lucida Grande", "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
	font-size: 1.2em;
	margin-bottom: 1em;
	line-height: 1.4em;
}

dl.faq {
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	font-size: 1.1em;
	margin-top: 1em;
	margin-bottom: 2em;
	line-height: 1.4em;
}

dl.faq dt {
	font-weight: bold;
	color: #333333;
}

.content dl.faq {
	font-size: 1.2em;
	margin-bottom: 0.5em;
}

.content li {
	list-style-type: inherit;
}

.content ul, .content ol {
	margin-bottom: 1em;
	margin-left: 3em;
}

.posthilit {
	background-color: #f3f3f3;
	color: #BCBCBC;
	padding: 0 2px 1px 2px;
}

.announce, .unreadpost {
	/* Highlight the announcements & unread posts box */
	border-left-color: #BCBCBC;
	border-right-color: #BCBCBC;
}

/* Post author */
p.author {
	margin: 0 15em 0.6em 0;
	padding: 0 0 5px 0;
	font-family: Verdana, Helvetica, Arial, sans-serif;
	font-size: 1em;
	line-height: 1.2em;
}

/* Post signature */
.signature {
	margin-top: 1.5em;
	padding-top: 0.2em;
	font-size: 1.1em;
	border-top: 1px solid #CCCCCC;
	clear: left;
	line-height: 140%;
	overflow: hidden;
	width: 100%;
}

dd .signature {
	margin: 0;
	padding: 0;
	clear: none;
	border: none;
}

.signature li {
	list-style-type: inherit;
}

.signature ul, .signature ol {
	margin-bottom: 1em;
	margin-left: 3em;
}

/* Post noticies */
.notice {
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	width: auto;
	margin-top: 1.5em;
	padding-top: 0.2em;
	font-size: 1em;
	border-top: 1px dashed #CCCCCC;
	clear: left;
	line-height: 130%;
}

/* Jump to post link for now */
ul.searchresults {
	list-style: none;
	text-align: right;
	clear: both;
}

/* BB Code styles
----------------------------------------*/
/* Quote block */
blockquote {
	background: #ebebeb none 6px 8px no-repeat;
	border: 1px solid #dbdbdb;
	font-size: 0.95em;
	margin: 0.5em 1px 0 25px;
	overflow: hidden;
	padding: 5px;
}

blockquote blockquote {
	/* Nested quotes */
	background-color: #bababa;
	font-size: 1em;
	margin: 0.5em 1px 0 15px;	
}

blockquote blockquote blockquote {
	/* Nested quotes */
	background-color: #e4e4e4;
}

blockquote cite {
	/* Username/source of quoter */
	font-style: normal;
	font-weight: bold;
	margin-left: 20px;
	display: block;
	font-size: 0.9em;
}

blockquote cite cite {
	font-size: 1em;
}

blockquote.uncited {
	padding-top: 25px;
}

/* Code block */
dl.codebox {
	padding: 3px;
	background-color: #FFFFFF;
	border: 1px solid #d8d8d8;
	font-size: 1em;
}

dl.codebox dt {
	text-transform: uppercase;
	border-bottom: 1px solid #CCCCCC;
	margin-bottom: 3px;
	font-size: 0.8em;
	font-weight: bold;
	display: block;
}

blockquote dl.codebox {
	margin-left: 0;
}

dl.codebox code {
	/* Also see tweaks.css */
	overflow: auto;
	display: block;
	height: auto;
	max-height: 200px;
	white-space: normal;
	padding-top: 5px;
	font: 0.9em Monaco, "Andale Mono","Courier New", Courier, mono;
	line-height: 1.3em;
	color: #8b8b8b;
	margin: 2px 0;
}

.syntaxbg		{ color: #FFFFFF; }
.syntaxcomment	{ color: #000000; }
.syntaxdefault	{ color: #bcbcbc; }
.syntaxhtml		{ color: #000000; }
.syntaxkeyword	{ color: #585858; }
.syntaxstring	{ color: #a7a7a7; }

/* Attachments
----------------------------------------*/
.attachbox {
	float: left;
	width: auto; 
	margin: 5px 5px 5px 0;
	padding: 6px;
	background-color: #FFFFFF;
	border: 1px dashed #d8d8d8;
	clear: left;
}

.pm-message .attachbox {
	background-color: #f3f3f3;
}

.attachbox dt {
	font-family: Arial, Helvetica, sans-serif;
	text-transform: uppercase;
}

.attachbox dd {
	margin-top: 4px;
	padding-top: 4px;
	clear: left;
	border-top: 1px solid #d8d8d8;
}

.attachbox dd dd {
	border: none;
}

.attachbox p {
	line-height: 110%;
	color: #666666;
	font-weight: normal;
	clear: left;
}

.attachbox p.stats
{
	line-height: 110%;
	color: #666666;
	font-weight: normal;
	clear: left;
}

.attach-image {
	margin: 3px 0;
	width: 100%;
	max-height: 350px;
	overflow: auto;
}

.attach-image img {
	border: 1px solid #999999;
/*	cursor: move; */
	cursor: default;
}

/* Inline image thumbnails */
div.inline-attachment dl.thumbnail, div.inline-attachment dl.file {
	display: block;
	margin-bottom: 4px;
}

div.inline-attachment p {
	font-size: 100%;
}

dl.file {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	display: block;
}

dl.file dt {
	text-transform: none;
	margin: 0;
	padding: 0;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

dl.file dd {
	color: #666666;
	margin: 0;
	padding: 0;	
}

dl.thumbnail img {
	padding: 3px;
	border: 1px solid #666666;
	background-color: #FFF;
}

dl.thumbnail dd {
	color: #666666;
	font-style: italic;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

.attachbox dl.thumbnail dd {
	font-size: 100%;
}

dl.thumbnail dt a:hover {
	background-color: #EEEEEE;
}

dl.thumbnail dt a:hover img {
	border: 1px solid #d2d2d2;
}

/* Post poll styles
----------------------------------------*/
fieldset.polls {
	font-family: "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
}

fieldset.polls dl {
	margin-top: 5px;
	border-top: 1px solid #e2e2e2;
	padding: 5px 0 0 0;
	line-height: 120%;
	color: #666666;
}

fieldset.polls dl.voted {
	font-weight: bold;
	color: #000000;
}

fieldset.polls dt {
	text-align: left;
	float: left;
	display: block;
	width: 30%;
	border-right: none;
	padding: 0;
	margin: 0;
	font-size: 1.1em;
}

fieldset.polls dd {
	float: left;
	width: 10%;
	border-left: none;
	padding: 0 5px;
	margin-left: 0;
	font-size: 1.1em;
}

fieldset.polls dd.resultbar {
	width: 50%;
}

fieldset.polls dd input {
	margin: 2px 0;
}

fieldset.polls dd div {
	text-align: right;
	font-family: Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
	padding: 0 2px;
	overflow: visible;
	min-width: 2%;
}

.pollbar1 {
	background-color: #aaaaaa;
	border-bottom: 1px solid #747474;
	border-right: 1px solid #747474;
}

.pollbar2 {
	background-color: #bebebe;
	border-bottom: 1px solid #8c8c8c;
	border-right: 1px solid #8c8c8c;
}

.pollbar3 {
	background-color: #D1D1D1;
	border-bottom: 1px solid #aaaaaa;
	border-right: 1px solid #aaaaaa;
}

.pollbar4 {
	background-color: #e4e4e4;
	border-bottom: 1px solid #bebebe;
	border-right: 1px solid #bebebe;
}

.pollbar5 {
	background-color: #f8f8f8;
	border-bottom: 1px solid #D1D1D1;
	border-right: 1px solid #D1D1D1;
}

/* Poster profile block
----------------------------------------*/
.postprofile {
	/* Also see tweaks.css */
	margin: 5px 0 10px 0;
	min-height: 80px;
	color: #666666;
	border-left: 1px solid #FFFFFF;
	width: 22%;
	float: right;
	display: inline;
}
.pm .postprofile {
	border-left: 1px solid #DDDDDD;
}

.postprofile dd, .postprofile dt {
	line-height: 1.2em;
	margin-left: 8px;
}

.postprofile strong {
	font-weight: normal;
	color: #000000;
}

.avatar {
	border: none;
	margin-bottom: 3px;
}

.online {
	background-image: none;
	background-position: 100% 0;
	background-repeat: no-repeat;
}

/* Poster profile used by search*/
.search .postprofile {
	width: 30%;
}

/* pm list in compose message if mass pm is enabled */
dl.pmlist dt {
	width: 60% !important;
}

dl.pmlist dt textarea {
	width: 95%;
}

dl.pmlist dd {
	margin-left: 61% !important;
	margin-bottom: 2px;
} 
 /* END @include content.css */ 

/* BEGIN @include buttons.css */ 
 /* proSilver Button Styles
---------------------------------------- */

/* Rollover buttons
   Based on: http://wellstyled.com/css-nopreload-rollovers.html
----------------------------------------*/
.buttons {
	float: left;
	width: auto;
	height: auto;
}

/* Rollover state */
.buttons div {
	float: left;
	margin: 0 5px 0 0;
	background-position: 0 100%;
}

/* Rolloff state */
.buttons div a {
	display: block;
	width: 100%;
	height: 100%;
	background-position: 0 0;
	position: relative;
	overflow: hidden;
}

/* Hide <a> text and hide off-state image when rolling over (prevents flicker in IE) */
/*.buttons div span		{ display: none; }*/
/*.buttons div a:hover	{ background-image: none; }*/
.buttons div span			{ position: absolute; width: 100%; height: 100%; cursor: pointer;}
.buttons div a:hover span	{ background-position: 0 100%; }

/* Big button images */
.reply-icon span	{ background: transparent none 0 0 no-repeat; }
.post-icon span		{ background: transparent none 0 0 no-repeat; }
.locked-icon span	{ background: transparent none 0 0 no-repeat; }
.pmreply-icon span	{ background: none 0 0 no-repeat; }
.newpm-icon span 	{ background: none 0 0 no-repeat; }
.forwardpm-icon span 	{ background: none 0 0 no-repeat; }

/* Set big button dimensions */
.buttons div.reply-icon		{ width: {IMG_BUTTON_TOPIC_REPLY_WIDTH}px; height: {IMG_BUTTON_TOPIC_REPLY_HEIGHT}px; }
.buttons div.post-icon		{ width: {IMG_BUTTON_TOPIC_NEW_WIDTH}px; height: {IMG_BUTTON_TOPIC_NEW_HEIGHT}px; }
.buttons div.locked-icon	{ width: {IMG_BUTTON_TOPIC_LOCKED_WIDTH}px; height: {IMG_BUTTON_TOPIC_LOCKED_HEIGHT}px; }
.buttons div.pmreply-icon	{ width: {IMG_BUTTON_PM_REPLY_WIDTH}px; height: {IMG_BUTTON_PM_REPLY_HEIGHT}px; }
.buttons div.newpm-icon		{ width: {IMG_BUTTON_PM_NEW_WIDTH}px; height: {IMG_BUTTON_PM_NEW_HEIGHT}px; }
.buttons div.forwardpm-icon	{ width: {IMG_BUTTON_PM_FORWARD_WIDTH}px; height: {IMG_BUTTON_PM_FORWARD_HEIGHT}px; }

/* Sub-header (navigation bar)
--------------------------------------------- */
a.print, a.sendemail, a.fontsize {
	display: block;
	overflow: hidden;
	height: 18px;
	text-indent: -5000px;
	text-align: left;
	background-repeat: no-repeat;
}

a.print {
	background-image: none;
	width: 22px;
}

a.sendemail {
	background-image: none;
	width: 22px;
}

a.fontsize {
	background-image: none;
	background-position: 0 -1px;
	width: 29px;
}

a.fontsize:hover {
	background-position: 0 -20px;
	text-decoration: none;
}

/* Icon images
---------------------------------------- */
.sitehome, .icon-faq, .icon-members, .icon-home, .icon-ucp, .icon-register, .icon-logout,
.icon-bookmark, .icon-bump, .icon-subscribe, .icon-unsubscribe, .icon-pages, .icon-search {
	background-position: 0 50%;
	background-repeat: no-repeat;
	background-image: none;
	padding: 1px 0 0 17px;
}

/* Poster profile icons
----------------------------------------*/
ul.profile-icons {
	padding-top: 10px;
	list-style: none;
}

/* Rollover state */
ul.profile-icons li {
	float: left;
	margin: 0 6px 3px 0;
	background-position: 0 100%;
}

/* Rolloff state */
ul.profile-icons li a {
	display: block;
	width: 100%;
	height: 100%;
	background-position: 0 0;
}

/* Hide <a> text and hide off-state image when rolling over (prevents flicker in IE) */
ul.profile-icons li span { display:none; }
ul.profile-icons li a:hover { background: none; }

/* Positioning of moderator icons */
.postbody ul.profile-icons {
	float: right;
	width: auto;
	padding: 0;
}

.postbody ul.profile-icons li {
	margin: 0 3px;
}

/* Profile & navigation icons */
.email-icon, .email-icon a		{ background: none top left no-repeat; }
.aim-icon, .aim-icon a			{ background: none top left no-repeat; }
.yahoo-icon, .yahoo-icon a		{ background: none top left no-repeat; }
.web-icon, .web-icon a			{ background: none top left no-repeat; }
.msnm-icon, .msnm-icon a			{ background: none top left no-repeat; }
.icq-icon, .icq-icon a			{ background: none top left no-repeat; }
.jabber-icon, .jabber-icon a		{ background: none top left no-repeat; }
.pm-icon, .pm-icon a				{ background: none top left no-repeat; }
.quote-icon, .quote-icon a		{ background: none top left no-repeat; }

/* Moderator icons */
.report-icon, .report-icon a		{ background: none top left no-repeat; }
.warn-icon, .warn-icon a			{ background: none top left no-repeat; }
.edit-icon, .edit-icon a			{ background: none top left no-repeat; }
.delete-icon, .delete-icon a		{ background: none top left no-repeat; }
.info-icon, .info-icon a			{ background: none top left no-repeat; }

/* Set profile icon dimensions */
ul.profile-icons li.email-icon		{ width: {IMG_ICON_CONTACT_EMAIL_WIDTH}px; height: {IMG_ICON_CONTACT_EMAIL_HEIGHT}px; }
ul.profile-icons li.aim-icon	{ width: {IMG_ICON_CONTACT_AIM_WIDTH}px; height: {IMG_ICON_CONTACT_AIM_HEIGHT}px; }
ul.profile-icons li.yahoo-icon	{ width: {IMG_ICON_CONTACT_YAHOO_WIDTH}px; height: {IMG_ICON_CONTACT_YAHOO_HEIGHT}px; }
ul.profile-icons li.web-icon	{ width: {IMG_ICON_CONTACT_WWW_WIDTH}px; height: {IMG_ICON_CONTACT_WWW_HEIGHT}px; }
ul.profile-icons li.msnm-icon	{ width: {IMG_ICON_CONTACT_MSNM_WIDTH}px; height: {IMG_ICON_CONTACT_MSNM_HEIGHT}px; }
ul.profile-icons li.icq-icon	{ width: {IMG_ICON_CONTACT_ICQ_WIDTH}px; height: {IMG_ICON_CONTACT_ICQ_HEIGHT}px; }
ul.profile-icons li.jabber-icon	{ width: {IMG_ICON_CONTACT_JABBER_WIDTH}px; height: {IMG_ICON_CONTACT_JABBER_HEIGHT}px; }
ul.profile-icons li.pm-icon		{ width: {IMG_ICON_CONTACT_PM_WIDTH}px; height: {IMG_ICON_CONTACT_PM_HEIGHT}px; }
ul.profile-icons li.quote-icon	{ width: {IMG_ICON_POST_QUOTE_WIDTH}px; height: {IMG_ICON_POST_QUOTE_HEIGHT}px; }
ul.profile-icons li.report-icon	{ width: {IMG_ICON_POST_REPORT_WIDTH}px; height: {IMG_ICON_POST_REPORT_HEIGHT}px; }
ul.profile-icons li.edit-icon	{ width: {IMG_ICON_POST_EDIT_WIDTH}px; height: {IMG_ICON_POST_EDIT_HEIGHT}px; }
ul.profile-icons li.delete-icon	{ width: {IMG_ICON_POST_DELETE_WIDTH}px; height: {IMG_ICON_POST_DELETE_HEIGHT}px; }
ul.profile-icons li.info-icon	{ width: {IMG_ICON_POST_INFO_WIDTH}px; height: {IMG_ICON_POST_INFO_HEIGHT}px; }
ul.profile-icons li.warn-icon	{ width: {IMG_ICON_USER_WARN_WIDTH}px; height: {IMG_ICON_USER_WARN_HEIGHT}px; }

/* Fix profile icon default margins */
ul.profile-icons li.edit-icon	{ margin: 0 0 0 3px; }
ul.profile-icons li.quote-icon	{ margin: 0 0 0 10px; }
ul.profile-icons li.info-icon, ul.profile-icons li.report-icon	{ margin: 0 3px 0 0; } 
 /* END @include buttons.css */ 

/* BEGIN @include cp.css */ 
 /* proSilver Control Panel Styles
---------------------------------------- */


/* Main CP box
----------------------------------------*/
#cp-menu {
	float:left;
	width: 19%;
	margin-top: 1em;
	margin-bottom: 5px;
}

#cp-main {
	float: left;
	width: 81%;
}

#cp-main .content {
	padding: 0;
}

#cp-main h3, #cp-main hr, #cp-menu hr {
	border-color: #bfbfbf;
}

#cp-main .panel p {
	font-size: 1.1em;
}

#cp-main .panel ol {
	margin-left: 2em;
	font-size: 1.1em;
}

#cp-main .panel li.row {
	border-bottom: 1px solid #cbcbcb;
	border-top: 1px solid #F9F9F9;
}

ul.cplist {
	margin-bottom: 5px;
	border-top: 1px solid #cbcbcb;
}

#cp-main .panel li.header dd, #cp-main .panel li.header dt {
	color: #000000;
	margin-bottom: 2px;
}

#cp-main table.table1 {
	margin-bottom: 1em;
}

#cp-main table.table1 thead th {
	color: #333333;
	font-weight: bold;
	border-bottom: 1px solid #333333;
	padding: 5px;
}

#cp-main table.table1 tbody th {
	font-style: italic;
	background-color: transparent !important;
	border-bottom: none;
}

#cp-main .pagination {
	float: right;
	width: auto;
	padding-top: 1px;
}

#cp-main .postbody p {
	font-size: 1.1em;
}

#cp-main .pm-message {
	border: 1px solid #e2e2e2;
	margin: 10px 0;
	background-color: #FFFFFF;
	width: auto;
	float: none;
}

.pm-message h2 {
	padding-bottom: 5px;
}

#cp-main .postbody h3, #cp-main .box2 h3 {
	margin-top: 0;
}

#cp-main .buttons {
	margin-left: 0;
}

#cp-main ul.linklist {
	margin: 0;
}

/* MCP Specific tweaks */
.mcp-main .postbody {
	width: 100%;
}

/* CP tabbed menu
----------------------------------------*/
#tabs {
	line-height: normal;
	margin: 20px 0 -1px 7px;
	min-width: 570px;
}

#tabs ul {
	margin:0;
	padding: 0;
	list-style: none;
}

#tabs li {
	display: inline;
	margin: 0;
	padding: 0;
	font-size: 1em;
	font-weight: bold;
}

#tabs a {
	float: left;
	background: none no-repeat 0% -35px;
	margin: 0 1px 0 0;
	padding: 0 0 0 5px;
	text-decoration: none;
	position: relative;
	cursor: pointer;
}

#tabs a span {
	float: left;
	display: block;
	background: none no-repeat 100% -35px;
	padding: 6px 10px 6px 5px;
	color: #828282;
	white-space: nowrap;
}

#tabs a:hover span {
	color: #bcbcbc;
}

#tabs .activetab a {
	background-position: 0 0;
	border-bottom: 1px solid #ebebeb;
}

#tabs .activetab a span {
	background-position: 100% 0;
	padding-bottom: 7px;
	color: #333333;
}

#tabs a:hover {
	background-position: 0 -70px;
}

#tabs a:hover span {
	background-position:100% -70px;
}

#tabs .activetab a:hover {
	background-position: 0 0;
}

#tabs .activetab a:hover span {
	color: #000000;
	background-position: 100% 0;
}

/* Mini tabbed menu used in MCP
----------------------------------------*/
#minitabs {
	line-height: normal;
	margin: -20px 7px 0 0;
}

#minitabs ul {
	margin:0;
	padding: 0;
	list-style: none;
}

#minitabs li {
	display: block;
	float: right;
	padding: 0 10px 4px 10px;
	font-size: 1em;
	font-weight: bold;
	background-color: #f2f2f2;
	margin-left: 2px;
}

#minitabs a {
}

#minitabs a:hover {
	text-decoration: none;
}

#minitabs li.activetab {
	background-color: #F9F9F9;
}

#minitabs li.activetab a, #minitabs li.activetab a:hover {
	color: #333333;
}

/* UCP navigation menu
----------------------------------------*/
/* Container for sub-navigation list */
#navigation {
	width: 100%;
	padding-top: 36px;
}

#navigation ul {
	list-style:none;
}

/* Default list state */
#navigation li {
	margin: 1px 0;
	padding: 0;
	font-weight: bold;
	display: inline;
}

/* Link styles for the sub-section links */
#navigation a {
	display: block;
	padding: 5px;
	margin: 1px 0;
	text-decoration: none;
	font-weight: bold;
	color: #333;
	background: #cfcfcf none repeat-y 100% 0;
}

#navigation a:hover {
	text-decoration: none;
	background-color: #c6c6c6;
	color: #bcbcbc;
	background-image: none;
}

#navigation #active-subsection a {
	display: block;
	color: #d3d3d3;
	background-color: #F9F9F9;
	background-image: none;
}

#navigation #active-subsection a:hover {
	color: #d3d3d3;
}

/* Preferences pane layout
----------------------------------------*/
#cp-main h2 {
	border-bottom: none;
	padding: 0;
	margin-left: 10px;
	color: #333333;
}

#cp-main .panel {
	background-color: #F9F9F9;
}

#cp-main .pm {
	background-color: #FFFFFF;
}

#cp-main span.corners-top, #cp-menu span.corners-top {
	background-image: none;
}

#cp-main span.corners-top span, #cp-menu span.corners-top span {
	background-image: none;
}

#cp-main span.corners-bottom, #cp-menu span.corners-bottom {
	background-image: none;
}

#cp-main span.corners-bottom span, #cp-menu span.corners-bottom span {
	background-image: none;
}

/* Topicreview */
#cp-main .panel #topicreview span.corners-top, #cp-menu .panel #topicreview span.corners-top {
	background-image: none;
}

#cp-main .panel #topicreview span.corners-top span, #cp-menu .panel #topicreview span.corners-top span {
	background-image: none;
}

#cp-main .panel #topicreview span.corners-bottom, #cp-menu .panel #topicreview span.corners-bottom {
	background-image: none;
}

#cp-main .panel #topicreview span.corners-bottom span, #cp-menu .panel #topicreview span.corners-bottom span {
	background-image: none;
}

/* Friends list */
.cp-mini {
	background-color: #f9f9f9;
	padding: 0 5px;
	margin: 10px 15px 10px 5px;
}

.cp-mini span.corners-top, .cp-mini span.corners-bottom {
	margin: 0 -5px;
}

dl.mini dt {
	font-weight: bold;
	color: #676767;
}

dl.mini dd {
	padding-top: 4px;
}

.friend-online {
	font-weight: bold;
}

.friend-offline {
	font-style: italic;
}

/* PM Styles
----------------------------------------*/
#pm-menu {
	line-height: 2.5em;
}

/* PM panel adjustments */
.pm-panel-header {
	margin: 0; 
	padding-bottom: 10px; 
	border-bottom: 1px dashed #A4B3BF;
}

.reply-all {
	display: block; 
	padding-top: 4px; 
	clear: both;
	float: left;
}

.pm-panel-message {
	padding-top: 10px;
}

.pm-return-to {
	padding-top: 23px;
}

#cp-main .pm-message-nav {
	margin: 0; 
	padding: 2px 10px 5px 10px; 
	border-bottom: 1px dashed #A4B3BF;
}

/* PM Message history */
.current {
	color: #999999;
}

/* Defined rules list for PM options */
ol.def-rules {
	padding-left: 0;
}

ol.def-rules li {
	line-height: 180%;
	padding: 1px;
}

/* PM marking colours */
.pmlist li.bg1 {
	border: solid 3px transparent;
	border-width: 0 3px;
}

.pmlist li.bg2 {
	border: solid 3px transparent;
	border-width: 0 3px;
}

.pmlist li.pm_message_reported_colour, .pm_message_reported_colour {
	border-left-color: #bcbcbc;
	border-right-color: #bcbcbc;
}

.pmlist li.pm_marked_colour, .pm_marked_colour {
	border: solid 3px #ffffff;
	border-width: 0 3px;
}

.pmlist li.pm_replied_colour, .pm_replied_colour {
	border: solid 3px #c2c2c2;
	border-width: 0 3px;	
}

.pmlist li.pm_friend_colour, .pm_friend_colour {
	border: solid 3px #bdbdbd;
	border-width: 0 3px;
}

.pmlist li.pm_foe_colour, .pm_foe_colour {
	border: solid 3px #000000;
	border-width: 0 3px;
}

.pm-legend {
	border-left-width: 10px;
	border-left-style: solid;
	border-right-width: 0;
	margin-bottom: 3px;
	padding-left: 3px;
}

/* Avatar gallery */
#gallery label {
	position: relative;
	float: left;
	margin: 10px;
	padding: 5px;
	width: auto;
	background: #FFFFFF;
	border: 1px solid #CCC;
	text-align: center;
}

#gallery label:hover {
	background-color: #EEE;
} 
 /* END @include cp.css */ 

/* BEGIN @include forms.css */ 
 /* proSilver Form Styles
---------------------------------------- */

/* General form styles
----------------------------------------*/
fieldset {
	border-width: 0;
	font-family: Verdana, Helvetica, Arial, sans-serif;
	font-size: 1.1em;
}

input {
	font-weight: normal;
	cursor: pointer;
	vertical-align: middle;
	padding: 0 3px;
	font-size: 1em;
	font-family: Verdana, Helvetica, Arial, sans-serif;
}

select {
	font-family: Verdana, Helvetica, Arial, sans-serif;
	font-weight: normal;
	cursor: pointer;
	vertical-align: middle;
	border: 1px solid #666666;
	padding: 1px;
	background-color: #FAFAFA;
}

option {
	padding-right: 1em;
}

option.disabled-option {
	color: graytext;
}

textarea {
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	width: 60%;
	padding: 2px;
	font-size: 1em;
	line-height: 1.4em;
}

label {
	cursor: default;
	padding-right: 5px;
	color: #676767;
}

label input {
	vertical-align: middle;
}

label img {
	vertical-align: middle;
}

/* Definition list layout for forms
---------------------------------------- */
fieldset dl {
	padding: 4px 0;
}

fieldset dt {
	float: left;	
	width: 40%;
	text-align: left;
	display: block;
}

fieldset dd {
	margin-left: 41%;
	vertical-align: top;
	margin-bottom: 3px;
}

/* Specific layout 1 */
fieldset.fields1 dt {
	width: 15em;
	border-right-width: 0;
}

fieldset.fields1 dd {
	margin-left: 15em;
	border-left-width: 0;
}

fieldset.fields1 {
	background-color: transparent;
}

fieldset.fields1 div {
	margin-bottom: 3px;
}

/* Set it back to 0px for the reCaptcha divs: PHPBB3-9587 */
fieldset.fields1 #recaptcha_widget_div div {
	margin-bottom: 0;
}

/* Specific layout 2 */
fieldset.fields2 dt {
	width: 15em;
	border-right-width: 0;
}

fieldset.fields2 dd {
	margin-left: 16em;
	border-left-width: 0;
}

/* Form elements */
dt label {
	font-weight: bold;
	text-align: left;
}

dd label {
	white-space: nowrap;
	color: #333;
}

dd input, dd textarea {
	margin-right: 3px;
}

dd select {
	width: auto;
}

dd textarea {
	width: 85%;
}

/* Hover effects */
fieldset dl:hover dt label {
	color: #000000;
}

fieldset.fields2 dl:hover dt label {
	color: inherit;
}

#timezone {
	width: 95%;
}

* html #timezone {
	width: 50%;
}

/* Quick-login on index page */
fieldset.quick-login {
	margin-top: 5px;
}

fieldset.quick-login input {
	width: auto;
}

fieldset.quick-login input.inputbox {
	width: 15%;
	vertical-align: middle;
	margin-right: 5px;
	background-color: #f3f3f3;
}

fieldset.quick-login label {
	white-space: nowrap;
	padding-right: 2px;
}

/* Display options on viewtopic/viewforum pages  */
fieldset.display-options {
	text-align: center;
	margin: 3px 0 5px 0;
}

fieldset.display-options label {
	white-space: nowrap;
	padding-right: 2px;
}

fieldset.display-options a {
	margin-top: 3px;
}

/* Display actions for ucp and mcp pages */
fieldset.display-actions {
	text-align: right;
	line-height: 2em;
	white-space: nowrap;
	padding-right: 1em;
}

fieldset.display-actions label {
	white-space: nowrap;
	padding-right: 2px;
}

fieldset.sort-options {
	line-height: 2em;
}

/* MCP forum selection*/
fieldset.forum-selection {
	margin: 5px 0 3px 0;
	float: right;
}

fieldset.forum-selection2 {
	margin: 13px 0 3px 0;
	float: right;
}

/* Jumpbox */
fieldset.jumpbox {
	text-align: right;
	margin-top: 15px;
	height: 2.5em;
}

fieldset.quickmod {
	width: 50%;
	float: right;
	text-align: right;
	height: 2.5em;
}

/* Submit button fieldset */
fieldset.submit-buttons {
	text-align: center;
	vertical-align: middle;
	margin: 5px 0;
}

fieldset.submit-buttons input {
	vertical-align: middle;
	padding-top: 3px;
	padding-bottom: 3px;
}

/* Posting page styles
----------------------------------------*/

/* Buttons used in the editor */
#format-buttons {
	margin: 15px 0 2px 0;
}

#format-buttons input, #format-buttons select {
	vertical-align: middle;
}

/* Main message box */
#message-box {
	width: 80%;
}

#message-box textarea {
	font-family: "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
	width: 100%;
	font-size: 1.2em;
	color: #333333;
}

/* Emoticons panel */
#smiley-box {
	width: 18%;
	float: right;
}

#smiley-box img {
	margin: 3px;
}

/* Input field styles
---------------------------------------- */
.inputbox {
	background-color: #FFFFFF;
	border: 1px solid #c0c0c0;
	color: #333333;
	padding: 2px;
	cursor: text;
}

.inputbox:hover {
	border: 1px solid #eaeaea;
}

.inputbox:focus {
	border: 1px solid #eaeaea;
	color: #4b4b4b;
}

input.inputbox	{ width: 85%; }
input.medium	{ width: 50%; }
input.narrow	{ width: 25%; }
input.tiny		{ width: 125px; }

textarea.inputbox {
	width: 85%;
}

.autowidth {
	width: auto !important;
}

/* Form button styles
---------------------------------------- */
input.button1, input.button2 {
	font-size: 1em;
}

a.button1, input.button1, input.button3, a.button2, input.button2 {
	width: auto !important;
	padding-top: 1px;
	padding-bottom: 1px;
	font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif;
	color: #000;
	background: #FAFAFA none repeat-x top left;
}

a.button1, input.button1 {
	font-weight: bold;
	border: 1px solid #666666;
}

input.button3 {
	padding: 0;
	margin: 0;
	line-height: 5px;
	height: 12px;
	background-image: none;
	font-variant: small-caps;
}

/* Alternative button */
a.button2, input.button2, input.button3 {
	border: 1px solid #666666;
}

/* <a> button in the style of the form buttons */
a.button1, a.button1:link, a.button1:visited, a.button1:active, a.button2, a.button2:link, a.button2:visited, a.button2:active {
	text-decoration: none;
	color: #000000;
	padding: 2px 8px;
	line-height: 250%;
	vertical-align: text-bottom;
	background-position: 0 1px;
}

/* Hover states */
a.button1:hover, input.button1:hover, a.button2:hover, input.button2:hover, input.button3:hover {
	border: 1px solid #BCBCBC;
	background-position: 0 100%;
	color: #BCBCBC;
}

input.disabled {
	font-weight: normal;
	color: #666666;
}

/* Topic and forum Search */
.search-box {
	margin-top: 3px;
	margin-left: 5px;
	float: left;
}

.search-box input {
}

input.search {
	background-image: none;
	background-repeat: no-repeat;
	background-position: left 1px;
	padding-left: 17px;
}

.full { width: 95%; }
.medium { width: 50%;}
.narrow { width: 25%;}
.tiny { width: 10%;} 
 /* END @include forms.css */ 

/* BEGIN @include tweaks.css */ 
 /* proSilver Style Sheet Tweaks

These style definitions are mainly IE specific 
tweaks required due to its poor CSS support.
-------------------------------------------------*/

* html table, * html select, * html input { font-size: 100%; }
* html hr { margin: 0; }
* html span.corners-top, * html span.corners-bottom { background-image: url("{T_THEME_PATH}/images/corners_left.gif"); }
* html span.corners-top span, * html span.corners-bottom span { background-image: url("{T_THEME_PATH}/images/corners_right.gif"); }

table.table1 {
	width: 99%;		/* IE < 6 browsers */
	/* Tantek hack */
	voice-family: inherit;
	width: 100%;
}
html>body table.table1 { width: 100%; }	/* Reset 100% for opera */

* html ul.topiclist li { position: relative; }
* html .postbody h3 img { vertical-align: middle; }

/* Form styles */
html>body dd label input { vertical-align: text-bottom; }	/* Align checkboxes/radio buttons nicely */

* html input.button1, * html input.button2 {
	padding-bottom: 0;
	margin-bottom: 1px;
}

/* Misc layout styles */
* html .column1, * html .column2 { width: 45%; }

/* Nice method for clearing floated blocks without having to insert any extra markup (like spacer above)
   From http://www.positioniseverything.net/easyclearing.html 
#tabs:after, #minitabs:after, .post:after, .navbar:after, fieldset dl:after, ul.topiclist dl:after, ul.linklist:after, dl.polls:after {
	content: "."; 
	display: block; 
	height: 0; 
	clear: both; 
	visibility: hidden;
}*/

.clearfix, #tabs, #minitabs, fieldset dl, ul.topiclist dl, dl.polls {
	height: 1%;
	overflow: hidden;
}

/* viewtopic fix */
* html .post {
	height: 25%;
	overflow: hidden;
}

/* navbar fix */
* html .clearfix, * html .navbar, ul.linklist {
	height: 4%;
	overflow: hidden;
}

/* Simple fix so forum and topic lists always have a min-height set, even in IE6
	From http://www.dustindiaz.com/min-height-fast-hack */
dl.icon {
	min-height: 35px;
	height: auto !important;
	height: 35px;
}

* html li.row dl.icon dt {
	height: 35px;
	overflow: visible;
}

* html #search-box {
	width: 25%;
}

/* Correctly clear floating for details on profile view */
*:first-child+html dl.details dd {
	margin-left: 30%;
	float: none;
}

* html dl.details dd {
	margin-left: 30%;
	float: none;
}

* html .forumbg table.table1 {
	margin: 0 -2px 0px -1px;
} 
 /* END @include tweaks.css */ 

/* BEGIN @include colours.css */ 
 /*  	
--------------------------------------------------------------
Colours and backgrounds for common.css
-------------------------------------------------------------- */

html, body {
	color: #536482;
	background-color: #FFFFFF;
}

h1 {
	color: #FFFFFF;
}

h2 {
	color: #28313F;
}

h3 {
	border-bottom-color: #CCCCCC;
	color: #115098;
}

hr {
	border-color: #FFFFFF;
	border-top-color: #CCCCCC;
}

hr.dashed {
	border-top-color: #CCCCCC;
}

/* Search box
--------------------------------------------- */

#search-box {
	color: #FFFFFF;
}

#search-box #keywords {
	background-color: #FFF;
}

#search-box input {
	border-color: #0075B0;
}

/* Round cornered boxes and backgrounds
---------------------------------------- */
.headerbar {
	background-color: #12A3EB;
	background-image: url("{T_THEME_PATH}/images/bg_header.gif");
	color: #FFFFFF;
}

.navbar {
	background-color: #cadceb;
}

.forabg {
	background-color: #0076b1;
	background-image: url("{T_THEME_PATH}/images/bg_list.gif");
}

.forumbg {
	background-color: #12A3EB;
	background-image: url("{T_THEME_PATH}/images/bg_header.gif");
}

.panel {
	background-color: #ECF1F3;
	color: #28313F;
}

.post:target .content {
	color: #000000;
}

.post:target h3 a {
	color: #000000;
}

.bg1	{ background-color: #ECF3F7; }
.bg2	{ background-color: #e1ebf2;  }
.bg3	{ background-color: #cadceb; }

.ucprowbg {
	background-color: #DCDEE2;
}

.fieldsbg {
	background-color: #E7E8EA;
}

span.corners-top {
	background-image: url("{T_THEME_PATH}/images/corners_left.png");
}

span.corners-top span {
	background-image: url("{T_THEME_PATH}/images/corners_right.png");
}

span.corners-bottom {
	background-image: url("{T_THEME_PATH}/images/corners_left.png");
}

span.corners-bottom span {
	background-image: url("{T_THEME_PATH}/images/corners_right.png");
}

/* Horizontal lists
----------------------------------------*/

ul.navlinks {
	border-bottom-color: #FFFFFF;
}

/* Table styles
----------------------------------------*/
table.table1 thead th {
	color: #FFFFFF;
}

table.table1 tbody tr {
	border-color: #BFC1CF;
}

table.table1 tbody tr:hover, table.table1 tbody tr.hover {
	background-color: #CFE1F6;
	color: #000;
}

table.table1 td {
	color: #536482;
}

table.table1 tbody td {
	border-top-color: #FAFAFA;
}

table.table1 tbody th {
	border-bottom-color: #000000;
	color: #333333;
	background-color: #FFFFFF;
}

table.info tbody th {
	color: #000000;
}

/* Misc layout styles
---------------------------------------- */
dl.details dt {
	color: #000000;
}

dl.details dd {
	color: #536482;
}

.sep {
	color: #1198D9;
}

/* Pagination
---------------------------------------- */

.pagination span strong {
	color: #FFFFFF;
	background-color: #4692BF;
	border-color: #4692BF;
}

.pagination span a, .pagination span a:link, .pagination span a:visited, .pagination span a:active {
	color: #5C758C;
	background-color: #ECEDEE;
	border-color: #B4BAC0;
}

.pagination span a:hover {
	border-color: #368AD2;
	background-color: #368AD2;
	color: #FFF;
}

/* Pagination in viewforum for multipage topics */
.row .pagination {
	background-image: url("{T_THEME_PATH}/images/icon_pages.gif");
}

.row .pagination span a, li.pagination span a {
	background-color: #FFFFFF;
}

.row .pagination span a:hover, li.pagination span a:hover {
	background-color: #368AD2;
}

/* Miscellaneous styles
---------------------------------------- */

.copyright {
	color: #555555;
}

.error {
	color: #BC2A4D;
}

.reported {
	background-color: #F7ECEF;
}

li.reported:hover {
	background-color: #ECD5D8 !important;
}
.sticky, .announce {
	/* you can add a background for stickies and announcements*/
}

div.rules {
	background-color: #ECD5D8;
	color: #BC2A4D;
}

p.rules {
	background-color: #ECD5D8;
	background-image: none;
}

/*  	
--------------------------------------------------------------
Colours and backgrounds for links.css
-------------------------------------------------------------- */

a:link	{ color: #105289; }
a:visited	{ color: #105289; }
a:hover	{ color: #D31141; }
a:active	{ color: #368AD2; }

/* Links on gradient backgrounds */
#search-box a:link, .navbg a:link, .forumbg .header a:link, .forabg .header a:link, th a:link {
	color: #FFFFFF;
}

#search-box a:visited, .navbg a:visited, .forumbg .header a:visited, .forabg .header a:visited, th a:visited {
	color: #FFFFFF;
}

#search-box a:hover, .navbg a:hover, .forumbg .header a:hover, .forabg .header a:hover, th a:hover {
	color: #A8D8FF;
}

#search-box a:active, .navbg a:active, .forumbg .header a:active, .forabg .header a:active, th a:active {
	color: #C8E6FF;
}

/* Links for forum/topic lists */
a.forumtitle {
	color: #105289;
}

/* a.forumtitle:visited { color: #105289; } */

a.forumtitle:hover {
	color: #BC2A4D;
}

a.forumtitle:active {
	color: #105289;
}

a.topictitle {
	color: #105289;
}

/* a.topictitle:visited { color: #368AD2; } */

a.topictitle:hover {
	color: #BC2A4D;
}

a.topictitle:active {
	color: #105289;
}

/* Post body links */
.postlink {
	color: #368AD2;
	border-bottom-color: #368AD2;
}

.postlink:visited {
	color: #5D8FBD;
	border-bottom-color: #666666;
}

.postlink:active {
	color: #368AD2;
}

.postlink:hover {
	background-color: #D0E4F6;
	color: #0D4473;
}

.signature a, .signature a:visited, .signature a:active, .signature a:hover {
	background-color: transparent;
}

/* Profile links */
.postprofile a:link, .postprofile a:active, .postprofile a:visited, .postprofile dt.author a {
	color: #105289;
}

.postprofile a:hover, .postprofile dt.author a:hover {
	color: #D31141;
}

/* Profile searchresults */	
.search .postprofile a {
	color: #105289;
}

.search .postprofile a:hover {
	color: #D31141;
}

/* Back to top of page */
a.top {
	background-image: url("{IMG_ICON_BACK_TOP_SRC}");
}

a.top2 {
	background-image: url("{IMG_ICON_BACK_TOP_SRC}");
}

/* Arrow links  */
a.up		{ background-image: url("{T_THEME_PATH}/images/arrow_up.gif") }
a.down		{ background-image: url("{T_THEME_PATH}/images/arrow_down.gif") }
a.left		{ background-image: url("{T_THEME_PATH}/images/arrow_left.gif") }
a.right		{ background-image: url("{T_THEME_PATH}/images/arrow_right.gif") }

a.up:hover {
	background-color: transparent;
}

a.left:hover {
	color: #368AD2;
}

a.right:hover {
	color: #368AD2;
}


/*  	
--------------------------------------------------------------
Colours and backgrounds for content.css
-------------------------------------------------------------- */

ul.forums {
	background-color: #eef5f9;
	background-image: url("{T_THEME_PATH}/images/gradient.gif");
}

ul.topiclist li {
	color: #4C5D77;
}

ul.topiclist dd {
	border-left-color: #FFFFFF;
}

.rtl ul.topiclist dd {
	border-right-color: #fff;
	border-left-color: transparent;
}

ul.topiclist li.row dt a.subforum.read {
	background-image: url("{IMG_SUBFORUM_READ_SRC}");
}

ul.topiclist li.row dt a.subforum.unread {
	background-image: url("{IMG_SUBFORUM_UNREAD_SRC}");
}

li.row {
	border-top-color:  #FFFFFF;
	border-bottom-color: #00608F;
}

li.row strong {
	color: #000000;
}

li.row:hover {
	background-color: #F6F4D0;
}

li.row:hover dd {
	border-left-color: #CCCCCC;
}

.rtl li.row:hover dd {
	border-right-color: #CCCCCC;
	border-left-color: transparent;
}

li.header dt, li.header dd {
	color: #FFFFFF;
}

/* Forum list column styles */
ul.topiclist dd.searchextra {
	color: #333333;
}

/* Post body styles
----------------------------------------*/
.postbody {
	color: #333333;
}

/* Content container styles
----------------------------------------*/
.content {
	color: #333333;
}

.content h2, .panel h2 {
	color: #115098;
	border-bottom-color:  #CCCCCC;
}

dl.faq dt {
	color: #333333;
}

.posthilit {
	background-color: #F3BFCC;
	color: #BC2A4D;
}

/* Post signature */
.signature {
	border-top-color: #CCCCCC;
}

/* Post noticies */
.notice {
	border-top-color:  #CCCCCC;
}

/* BB Code styles
----------------------------------------*/
/* Quote block */
blockquote {
	background-color: #EBEADD;
	background-image: url("{T_THEME_PATH}/images/quote.gif");
	border-color:#DBDBCE;
}

.rtl blockquote {
	background-image: url("{T_THEME_PATH}/images/quote_rtl.gif");
}

blockquote blockquote {
	/* Nested quotes */
	background-color:#EFEED9;
}

blockquote blockquote blockquote {
	/* Nested quotes */
	background-color: #EBEADD;
}

/* Code block */
dl.codebox {
	background-color: #FFFFFF;
	border-color: #C9D2D8;
}

dl.codebox dt {
	border-bottom-color:  #CCCCCC;
}

dl.codebox code {
	color: #2E8B57;
}

.syntaxbg		{ color: #FFFFFF; }
.syntaxcomment	{ color: #FF8000; }
.syntaxdefault	{ color: #0000BB; }
.syntaxhtml		{ color: #000000; }
.syntaxkeyword	{ color: #007700; }
.syntaxstring	{ color: #DD0000; }

/* Attachments
----------------------------------------*/
.attachbox {
	background-color: #FFFFFF;
	border-color:  #C9D2D8;
}

.pm-message .attachbox {
	background-color: #F2F3F3;
}

.attachbox dd {
	border-top-color: #C9D2D8;
}

.attachbox p {
	color: #666666;
}

.attachbox p.stats {
	color: #666666;
}

.attach-image img {
	border-color: #999999;
}

/* Inline image thumbnails */

dl.file dd {
	color: #666666;
}

dl.thumbnail img {
	border-color: #666666;
	background-color: #FFFFFF;
}

dl.thumbnail dd {
	color: #666666;
}

dl.thumbnail dt a:hover {
	background-color: #EEEEEE;
}

dl.thumbnail dt a:hover img {
	border-color: #368AD2;
}

/* Post poll styles
----------------------------------------*/

fieldset.polls dl {
	border-top-color: #DCDEE2;
	color: #666666;
}

fieldset.polls dl.voted {
	color: #000000;
}

fieldset.polls dd div {
	color: #FFFFFF;
}

.rtl .pollbar1, .rtl .pollbar2, .rtl .pollbar3, .rtl .pollbar4, .rtl .pollbar5 {
	border-right-color: transparent;
}

.pollbar1 {
	background-color: #AA2346;
	border-bottom-color: #74162C;
	border-right-color: #74162C;
}

.rtl .pollbar1 {
	border-left-color: #74162C;
}

.pollbar2 {
	background-color: #BE1E4A;
	border-bottom-color: #8C1C38;
	border-right-color: #8C1C38;
}

.rtl .pollbar2 {
	border-left-color: #8C1C38;
}

.pollbar3 {
	background-color: #D11A4E;
	border-bottom-color: #AA2346;
	border-right-color: #AA2346;
}

.rtl .pollbar3 {
	border-left-color: #AA2346;
}

.pollbar4 {
	background-color: #E41653;
	border-bottom-color: #BE1E4A;
	border-right-color: #BE1E4A;
}

.rtl .pollbar4 {
	border-left-color: #BE1E4A;
}

.pollbar5 {
	background-color: #F81157;
	border-bottom-color: #D11A4E;
	border-right-color: #D11A4E;
}

.rtl .pollbar5 {
	border-left-color: #D11A4E;
}

/* Poster profile block
----------------------------------------*/
.postprofile {
	color: #666666;
	border-left-color: #FFFFFF;
}

.rtl .postprofile {
	border-right-color: #FFFFFF;
	border-left-color: transparent;
}

.pm .postprofile {
	border-left-color: #DDDDDD;
}

.rtl .pm .postprofile {
	border-right-color: #DDDDDD;
	border-left-color: transparent;
}

.postprofile strong {
	color: #000000;
}

.online {
	background-image: url("{T_IMAGESET_LANG_PATH}/icon_user_online.gif");
}

/*  	
--------------------------------------------------------------
Colours and backgrounds for buttons.css
-------------------------------------------------------------- */

/* Big button images */
.reply-icon span	{ background-image: url("{IMG_BUTTON_TOPIC_REPLY_SRC}"); }
.post-icon span		{ background-image: url("{IMG_BUTTON_TOPIC_NEW_SRC}"); }
.locked-icon span	{ background-image: url("{IMG_BUTTON_TOPIC_LOCKED_SRC}"); }
.pmreply-icon span	{ background-image: url("{IMG_BUTTON_PM_REPLY_SRC}") ;}
.newpm-icon span 	{ background-image: url("{IMG_BUTTON_PM_NEW_SRC}") ;}
.forwardpm-icon span	{ background-image: url("{IMG_BUTTON_PM_FORWARD_SRC}") ;}

a.print {
	background-image: url("{T_THEME_PATH}/images/icon_print.gif");
}

a.sendemail {
	background-image: url("{T_THEME_PATH}/images/icon_sendemail.gif");
}

a.fontsize {
	background-image: url("{T_THEME_PATH}/images/icon_fontsize.gif");
}

/* Icon images
---------------------------------------- */
.sitehome						{ background-image: url("{T_THEME_PATH}/images/icon_home.gif"); }
.icon-faq						{ background-image: url("{T_THEME_PATH}/images/icon_faq.gif"); }
.icon-members					{ background-image: url("{T_THEME_PATH}/images/icon_members.gif"); }
.icon-home						{ background-image: url("{T_THEME_PATH}/images/icon_home.gif"); }
.icon-ucp						{ background-image: url("{T_THEME_PATH}/images/icon_ucp.gif"); }
.icon-register					{ background-image: url("{T_THEME_PATH}/images/icon_register.gif"); }
.icon-logout					{ background-image: url("{T_THEME_PATH}/images/icon_logout.gif"); }
.icon-bookmark					{ background-image: url("{T_THEME_PATH}/images/icon_bookmark.gif"); }
.icon-bump						{ background-image: url("{T_THEME_PATH}/images/icon_bump.gif"); }
.icon-subscribe					{ background-image: url("{T_THEME_PATH}/images/icon_subscribe.gif"); }
.icon-unsubscribe				{ background-image: url("{T_THEME_PATH}/images/icon_unsubscribe.gif"); }
.icon-pages						{ background-image: url("{T_THEME_PATH}/images/icon_pages.gif"); }
.icon-search					{ background-image: url("{T_THEME_PATH}/images/icon_search.gif"); }

/* Profile & navigation icons */
.email-icon, .email-icon a		{ background-image: url("{IMG_ICON_CONTACT_EMAIL_SRC}"); }
.aim-icon, .aim-icon a			{ background-image: url("{IMG_ICON_CONTACT_AIM_SRC}"); }
.yahoo-icon, .yahoo-icon a		{ background-image: url("{IMG_ICON_CONTACT_YAHOO_SRC}"); }
.web-icon, .web-icon a			{ background-image: url("{IMG_ICON_CONTACT_WWW_SRC}"); }
.msnm-icon, .msnm-icon a			{ background-image: url("{IMG_ICON_CONTACT_MSNM_SRC}"); }
.icq-icon, .icq-icon a			{ background-image: url("{IMG_ICON_CONTACT_ICQ_SRC}"); }
.jabber-icon, .jabber-icon a		{ background-image: url("{IMG_ICON_CONTACT_JABBER_SRC}"); }
.pm-icon, .pm-icon a				{ background-image: url("{IMG_ICON_CONTACT_PM_SRC}"); }
.quote-icon, .quote-icon a		{ background-image: url("{IMG_ICON_POST_QUOTE_SRC}"); }

/* Moderator icons */
.report-icon, .report-icon a		{ background-image: url("{IMG_ICON_POST_REPORT_SRC}"); }
.edit-icon, .edit-icon a			{ background-image: url("{IMG_ICON_POST_EDIT_SRC}"); }
.delete-icon, .delete-icon a		{ background-image: url("{IMG_ICON_POST_DELETE_SRC}"); }
.info-icon, .info-icon a			{ background-image: url("{IMG_ICON_POST_INFO_SRC}"); }
.warn-icon, .warn-icon a			{ background-image: url("{IMG_ICON_USER_WARN_SRC}"); } /* Need updated warn icon */

/*  	
--------------------------------------------------------------
Colours and backgrounds for cp.css
-------------------------------------------------------------- */

/* Main CP box
----------------------------------------*/

#cp-main h3, #cp-main hr, #cp-menu hr {
	border-color: #A4B3BF;
}

#cp-main .panel li.row {
	border-bottom-color: #B5C1CB;
	border-top-color: #F9F9F9;
}

ul.cplist {
	border-top-color: #B5C1CB;
}

#cp-main .panel li.header dd, #cp-main .panel li.header dt {
	color: #000000;
}

#cp-main table.table1 thead th {
	color: #333333;
	border-bottom-color: #333333;
}

#cp-main .pm-message {
	border-color: #DBDEE2;
	background-color: #FFFFFF;
}

/* CP tabbed menu
----------------------------------------*/
#tabs a {
	background-image: url("{T_THEME_PATH}/images/bg_tabs1.gif");
}

#tabs a span {
	background-image: url("{T_THEME_PATH}/images/bg_tabs2.gif");
	color: #536482;
}

#tabs a:hover span {
	color: #BC2A4D;
}

#tabs .activetab a {
	border-bottom-color: #CADCEB;
}

#tabs .activetab a span {
	color: #333333;
}

#tabs .activetab a:hover span {
	color: #000000;
}

/* Mini tabbed menu used in MCP
----------------------------------------*/
#minitabs li {
	background-color: #E1EBF2;
}

#minitabs li.activetab {
	background-color: #F9F9F9;
}

#minitabs li.activetab a, #minitabs li.activetab a:hover {
	color: #333333;
}

/* UCP navigation menu
----------------------------------------*/

/* Link styles for the sub-section links */
#navigation a {
	color: #333;
	background-color: #B2C2CF;
	background-image: url("{T_THEME_PATH}/images/bg_menu.gif");
}

.rtl #navigation a {
	background-image: url("{T_THEME_PATH}/images/bg_menu_rtl.gif");
	background-position: 0 100%;
}

#navigation a:hover {
	background-image: none;
	background-color: #aabac6;
	color: #BC2A4D;
}

#navigation #active-subsection a {
	color: #D31141;
	background-color: #F9F9F9;
	background-image: none;
}

#navigation #active-subsection a:hover {
	color: #D31141;
}

/* Preferences pane layout
----------------------------------------*/
#cp-main h2 {
	color: #333333;
}

#cp-main .panel {
	background-color: #F9F9F9;
}

#cp-main .pm {
	background-color: #FFFFFF;
}

#cp-main span.corners-top, #cp-menu span.corners-top {
	background-image: url("{T_THEME_PATH}/images/corners_left2.gif");
}

#cp-main span.corners-top span, #cp-menu span.corners-top span {
	background-image: url("{T_THEME_PATH}/images/corners_right2.gif");
}

#cp-main span.corners-bottom, #cp-menu span.corners-bottom {
	background-image: url("{T_THEME_PATH}/images/corners_left2.gif");
}

#cp-main span.corners-bottom span, #cp-menu span.corners-bottom span {
	background-image: url("{T_THEME_PATH}/images/corners_right2.gif");
}

/* Topicreview */
#cp-main .panel #topicreview span.corners-top, #cp-menu .panel #topicreview span.corners-top {
	background-image: url("{T_THEME_PATH}/images/corners_left.gif");
}

#cp-main .panel #topicreview span.corners-top span, #cp-menu .panel #topicreview span.corners-top span {
	background-image: url("{T_THEME_PATH}/images/corners_right.gif");
}

#cp-main .panel #topicreview span.corners-bottom, #cp-menu .panel #topicreview span.corners-bottom {
	background-image: url("{T_THEME_PATH}/images/corners_left.gif");
}

#cp-main .panel #topicreview span.corners-bottom span, #cp-menu .panel #topicreview span.corners-bottom span {
	background-image: url("{T_THEME_PATH}/images/corners_right.gif");
}

/* Friends list */
.cp-mini {
	background-color: #eef5f9;
}

dl.mini dt {
	color: #425067;
}

/* PM Styles
----------------------------------------*/
/* PM Message history */
.current {
	color: #000000 !important;
}

/* PM panel adjustments */
.pm-panel-header,
#cp-main .pm-message-nav {
	border-bottom-color: #A4B3BF;
}

/* PM marking colours */
.pmlist li.pm_message_reported_colour, .pm_message_reported_colour {
	border-left-color: #BC2A4D;
	border-right-color: #BC2A4D;
}

.pmlist li.pm_marked_colour, .pm_marked_colour {
	border-color: #FF6600;
}

.pmlist li.pm_replied_colour, .pm_replied_colour {
	border-color: #A9B8C2;
}

.pmlist li.pm_friend_colour, .pm_friend_colour {
	border-color: #5D8FBD;
}

.pmlist li.pm_foe_colour, .pm_foe_colour {
	border-color: #000000;
}

/* Avatar gallery */
#gallery label {
	background-color: #FFFFFF;
	border-color: #CCC;
}

#gallery label:hover {
	background-color: #EEE;
}

/*  	
--------------------------------------------------------------
Colours and backgrounds for forms.css
-------------------------------------------------------------- */

/* General form styles
----------------------------------------*/
select {
	border-color: #666666;
	background-color: #FAFAFA;
	color: #000;
}

label {
	color: #425067;
}

option.disabled-option {
	color: graytext;
}

/* Definition list layout for forms
---------------------------------------- */
dd label {
	color: #333;
}

/* Hover effects */
fieldset dl:hover dt label {
	color: #000000;
}

fieldset.fields2 dl:hover dt label {
	color: inherit;
}

/* Quick-login on index page */
fieldset.quick-login input.inputbox {
	background-color: #F2F3F3;
}

/* Posting page styles
----------------------------------------*/

#message-box textarea {
	color: #333333;
}

/* Input field styles
---------------------------------------- */
.inputbox {
	background-color: #FFFFFF; 
	border-color: #B4BAC0;
	color: #333333;
}

.inputbox:hover {
	border-color: #11A3EA;
}

.inputbox:focus {
	border-color: #11A3EA;
	color: #0F4987;
}

/* Form button styles
---------------------------------------- */

a.button1, input.button1, input.button3, a.button2, input.button2 {
	color: #000;
	background-color: #FAFAFA;
	background-image: url("{T_THEME_PATH}/images/bg_button.gif");
}

a.button1, input.button1 {
	border-color: #666666;
}

input.button3 {
	background-image: none;
}

/* Alternative button */
a.button2, input.button2, input.button3 {
	border-color: #666666;
}

/* <a> button in the style of the form buttons */
a.button1, a.button1:link, a.button1:visited, a.button1:active, a.button2, a.button2:link, a.button2:visited, a.button2:active {
	color: #000000;
}

/* Hover states */
a.button1:hover, input.button1:hover, a.button2:hover, input.button2:hover, input.button3:hover {
	border-color: #BC2A4D;
	color: #BC2A4D;
}

input.search {
	background-image: url("{T_THEME_PATH}/images/icon_textbox_search.gif");
}

input.disabled {
	color: #666666;
} 
 /* END @include colours.css */ 

');

INSERT INTO "phpbb_styles_theme" VALUES ('5', 'phpBB iPhone', '&copy; 2010 Callum Macrae', 'mobile', '1', '1297501070', '/*iWebKit css 5.04 by Christopher Plieger*/
body {
	position: relative;
	margin: 0;
	-webkit-text-size-adjust: none;
	min-height: 416px;
	font-family: helvetica,sans-serif;
	-webkit-background-size:0.438em 100%; 
	background: -webkit-gradient(linear,left top,right top,from(#c5ccd4), color-stop(71%, #c5ccd4), color-stop(72%, #cbd2d8), to(#cbd2d8));
	-webkit-touch-callout: none;
}
.center {
	margin: auto;
	display: block;
	text-align: center!important;
}
img {
	border: 0;
}
a:hover .arrow {
	background-position: 0 -13px!important;
}
@media screen and (max-width: 320px)
{
#topbar {
	height: 44px;
}
#title {
	line-height: 44px;
	height: 44px;
	font-size: 16pt;
}
#tributton a:first-child, #duobutton a:first-child {
	width: 101px;
}
#tributton a:last-child, #duobutton a:last-child {
	width: 101px;
}
#tributton a {
	width: 106px;
}
#duobutton .links {
	width: 195px;
}
#tributton .links {
	width: 302px;
}
#doublead {
	width: 300px!important;
}
#duoselectionbuttons {
	width: 191px;
	height: 30px;
	top: 7px;
}
#triselectionbuttons {
	width: 290px;
	height: 30px;
	top: 7px;
}
#triselectionbuttons a:first-child, #duoselectionbuttons a:first-child {
	width: 99px;
	height: 28px;
	line-height: 28px;
}
#triselectionbuttons a {
	width: 98px;
	height: 28px;
	line-height: 28px;
}
#triselectionbuttons a:last-child, #duoselectionbuttons a:last-child {
	width: 99px;
	height: 28px;
	line-height: 28px;
}
.searchbox form {
	width: 272px;
}
.searchbox input[type="text"] {
	width: 275px;
}
.menu .name {
	max-width: 77%;
}.checkbox .name {
	max-width: 190px;
}.radiobutton .name {
	max-width: 190px;
}
#leftnav a, #rightnav a, #leftbutton a, #rightbutton a, #blueleftbutton a, #bluerightbutton a {
	line-height: 30px;
	height: 30px;
}
#leftnav img, #rightnav img {
	margin-top: 4px;
}
#leftnav, #leftbutton, #blueleftbutton {
	top: 7px;
}
#rightnav, #rightbutton, #bluerightbutton {
	top: 7px;
}
.textbox textarea {
	width: 280px;
}
.bigfield input{
	width:295px
}
}
@media screen and (min-width: 321px)
{
#topbar {
	height: 32px;
}
#title {
	line-height: 32px;
	height: 32px;
	font-size: 13pt;
}
.menu .name {
	max-width: 85%;
}.checkbox .name {
	max-width: 75%;
}.radiobutton .name {
	max-width: 75%;
}
#leftnav a, #rightnav a, #leftbutton a, #rightbutton a, #blueleftbutton a, #bluerightbutton a {
	line-height: 24px;
	height: 24px;
}
#leftnav img, #rightnav img {
	margin-top: 4px;
	height: 70%;
}
#leftnav, #leftbutton, #blueleftbutton {
	top: 4px;
}
#rightnav, #rightbutton, #bluerightbutton {
	top: 4px;
}
.textbox textarea {
	width: 440px;
}
#tributton a:first-child, #duobutton a:first-child {
	width: 152px;
}
#tributton a:last-child, #duobutton a:last-child {
	width: 152px;
}
#tributton a {
	width: 154px;
}
#tributton .links {
	width: 452px;
}
#duobutton .links {
	width: 298px;
}
#doublead {
	width: 350px!important;
}
#duoselectionbuttons {
	width: 293px;
	height: 24px;
	top: 4px;
}
#triselectionbuttons {
	width: 450px;
	height: 24px;
	top: 4px;
}
#triselectionbuttons a:first-child, #duoselectionbuttons a:first-child {
	width: 150px;
	height: 22px;
	line-height: 22px;
}
#triselectionbuttons a {
	width: 156px;
	height: 22px;
	line-height: 22px;
}
#triselectionbuttons a:last-child, #duoselectionbuttons a:last-child {
	width: 150px;
	height: 22px;
	line-height: 22px;
}
.searchbox form {
	width: 432px;
}
.searchbox input[type="text"] {
	width: 435px;
}
.bigfield input{
	width:455px
}
}
#topbar.black {
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#858585), color-stop(3%, #636363), color-stop(50%, #202020), color-stop(51%, black), color-stop(97%, black), to(#262626));
}
#topbar.transparent {
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba(133,133,133,0.7)), color-stop(3%, rgba(99,99,99,0.7)), color-stop(50%, rgba(32,32,32,0.7)), color-stop(51%, rgba(0,0,0,0.7)), color-stop(97%, rgba(0,0,0,0.7)), to(rgba(38,38,38,0.7)));
}
#topbar {
	position: relative;
	left: 0;
	top: 0;
	width: auto;
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#cdd5df), color-stop(3%, #b0bccd), color-stop(50%, #889bb3), color-stop(51%, #8195af), color-stop(97%, #6d84a2), to(#2d3642));
	margin-bottom: 13px;
}
#title {
	position: absolute;
	font-weight: bold;
	top: 0;
	left: 0;
	right: 0;
	padding: 0 10px;
	text-align: center;
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
	color: #FFF;
	text-shadow: rgba(0,0,0,0.6) 0 -1px 0;
}
#content {
	width: 100%;
	position: relative;
	min-height: 250px;
	margin-top: 10px;
	height: auto;
	z-index: 0;
	overflow: hidden;
}
#footer {
	text-align: center;
	position: relative;
	margin: 20px 10px 0;
	height: auto;
	width: auto;
	bottom: 10px;
}
#footer a, #footer {
	text-decoration: none;
	font-size: 9pt;
	color: #4C4C4C;
	text-shadow: #FFF 0 1px 0;
}
.pageitem {
	-webkit-border-radius: 8px;
	background-color: #fff;
	border: #878787 solid 1px;
	font-size: 12pt;
	overflow: hidden;
	padding: 0;
	position: relative;
	display: block;
	height: auto;
	width: auto;
	margin: 3px 9px 17px;
	list-style: none;
}
.textbox {
	padding: 5px 9px;
	position: relative;
	overflow: hidden;
	border-top: 1px solid #878787;
}
#tributton, #duobutton {
	height: 44px;
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#cdd4d9), color-stop(3%, #c0c9cf), color-stop(97%, #abb7bf),to(#81929f));
	margin: -13px 0 13px 0;
	text-align: center;
}
#tributton .links, #duobutton .links {
	height: 30px;
	-webkit-border-image: url("§1	images/tributton.png") 0 4 0 4;
	border-width: 0 4px 0 4px;
	margin: 0 auto 0px auto;
	position: relative;
	top: 7px;
}
#tributton a:first-child, #duobutton a:first-child {
	border-right: 1px solid #6d7e91;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
	margin-left: -4px;
}
#tributton a, #duobutton a {
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	height: 27px;
	display: inline-block;
	line-height: 27px;
	margin-top: 1px;
	font: bold 13px;
	text-decoration: none;
	color: #3f5c84;
	text-shadow: #FFF 0 1px 0;
}
#duobutton a:last-child {
	border: 0;
}
#tributton a:last-child {
	border-left: 1px solid #6d7e91;
}
#tributton a:last-child, #duobutton a:last-child {
	-webkit-border-top-right-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	margin-right: -4px;
}
#tributton a:hover, #tributton a#pressed, #duobutton a:hover, #duobutton a#pressed {
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#7b8b9f), color-stop(3%, #8c9baf), to(#647792));
	color: white;
	text-shadow: black 0 -1px 0;
}
#triselectionbuttons, #duoselectionbuttons {
	-webkit-border-image: url(''images/navbutton.png'') 0 5 0 5;
	border-width: 0 5px 0 5px;
	position: relative;
	margin: auto;
}
#duoselectionbuttons a:first-child {
	border: 0;
}
#triselectionbuttons a:first-child {
	border-right: solid 1px #556984;
}
#triselectionbuttons a:first-child, #duoselectionbuttons a:first-child {
	margin-left: -4px;
	-webkit-border-top-left-radius: 6px;
	-webkit-border-bottom-left-radius: 6px;
}
#triselectionbuttons a, #duoselectionbuttons a {
	display: inline-block;
	text-align: center;
	color: white;
	text-decoration: none;
	margin-top: 1px;
	text-shadow: black 0 -1px 0;
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#909baa), color-stop(3%, #a5b4c6), color-stop(50%, #798eaa), color-stop(51%, #6b83a1), color-stop(97%, #6e85a3), to(#526379));
}
#triselectionbuttons a:last-child, #duoselectionbuttons a:last-child {
	border-left: solid 1px #556984;
	margin-right: -4px;
	-webkit-border-top-right-radius: 6px;
	-webkit-border-bottom-right-radius: 6px;
}
#triselectionbuttons a:hover, #triselectionbuttons a#pressed, #duoselectionbuttons a:hover, #duoselectionbuttons a#pressed {
	background: none;
}
#doublead {
	height: 83px!important;
	position: relative;
	margin: 0 auto 13px auto;
}
#doublead a:first-child {
	left: 0!important;
}
#doublead a:last-child {
	right: 0!important;
}
#doublead a {
	width: 147px!important;
	height: 83px!important;
	position: absolute;
	-webkit-border-radius: 8px;
	display: block;
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#7c7c7c), color-stop(3%, #858585), color-stop(97%, #a4a4a4),to(#c2c2c2));
}
li#doublead {
	margin-top: 25px;
	margin-bottom: 10px!important;
	background: none;
}
li#doublead:hover {
	background: none;
}
.searchbox {
	height: 44px;
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f1f3f4), color-stop(3%, #e0e4e7), color-stop(50%, #c7cfd4), color-stop(51%, #bec7cd), color-stop(97%, #b4bec6), to(#8999a5));
	margin: -13px 0 13px 0;
	width: 100%;
}
.searchbox form {
	height: 24px;
	-webkit-border-image: url(''images/searchfield.png'') 4 14 1 24;
	border-width: 4px 14px 1px 24px;
	display: block;
	position: relative;
	top: 8px;
	margin: auto;
}
fieldset {
	border: 0;
	margin: 0;
	padding: 0;
}
.searchbox input[type="text"] {
	border: 0;
	-webkit-appearance: none;
	height: 18px;
	float: left;
	font-size: 13px;
	padding: 0;
	position: relative;
	top: 2px;
	left: 2px;
}
.textbox img {
	max-width: 100%;
}
.textbox p {
	margin-top: 2px;
}
.textbox p {
	margin-top: 2px;
	color: #000;
	margin-bottom: 2px;
	text-align: justify;
}
.textbox img {
	max-width: 100%;
}
.textbox ul {
	margin: 3px 0 3px 0;
	list-style: circle!important;
}
.textbox li {
	margin: 0!important;
}
.pageitem li:first-child, .pageitem li.form:first-child {
	border-top: 0;
}
.menu, .checkbox, .radiobutton, .select, li.button, li.bigfield, li.smallfield {
	position: relative;
	list-style-type: none;
	display: block;
	height: 43px;
	overflow: hidden;
	border-top: 1px solid #878787;
	width: auto;
}
.pageitem li:first-child:hover, .pageitem li:first-child a, .radiobutton:first-child input, .select:first-child select, li.button:first-child input, .bigfield:first-child input {
	-webkit-border-top-left-radius: 8px;
	-webkit-border-top-right-radius: 8px;
}
.pageitem li:last-child:hover, .pageitem li:last-child a, .radiobutton:last-child input, .select:last-child select, li.button:last-child input, .bigfield:last-child input {
	-webkit-border-bottom-left-radius: 8px;
	-webkit-border-bottom-right-radius: 8px;
}
.menu:hover, .store:hover, .list #content li a:hover, .list .withimage:hover {
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#058cf5), to(#015fe6));
}
.menu a:hover .name, .store:hover .starcomment, .store:hover .name, .store:hover .comment, .list .withimage a:hover .comment {
	color: #fff;
}
.menu a:hover .comment {
	color: #CCF;
}
.menu a {
	display: block;
	height: 43px;
	width: auto;
	text-decoration: none;
}
.menu a img {
	width: auto;
	height: 32px;
	margin: 5px 0 0 5px;
	float: left;
}
.menu .name, .checkbox .name, .radiobutton .name {
	margin: 11px 0 0 7px;
	width: auto;
	color: #000;
	font-weight: bold;
	font-size: 17px;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	float: left;
}
.menu .comment {
	margin: 11px 30px 0 0;
	width: auto;
	font-size: 17px;
	text-overflow: ellipsis;
	overflow: hidden;
	max-width: 75%;
	white-space: nowrap;
	float: right;
	color: #324f85;
}
.menu .arrow, .store .arrow, .list .arrow {
	position: absolute;
	width: 8px!important;
	height: 13px!important;
	right: 10px;
	top: 15px;
	margin: 0!important;
	background: url("images/arrow.png") 0 0 no-repeat;
}
.graytitle {
	position: relative;
	font-weight: bold;
	font-size: 17px;
	right: 20px;
	left: 9px;
	color: #4C4C4C;
	text-shadow: #FFF 0 1px 0;
	padding: 1px 0 3px 8px;
}
.header {
	display: block;
	font-weight: bold;
	color: rgb(73,102,145);
	font-size: 12pt;
	margin-bottom: 6px;
	line-height: 14pt;
	padding-left: 30px;
}

.list .title {
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#a5b1ba), color-stop(3%, #909faa), color-stop(97%, #b5bfc6), to(#989ea4));
	height: 22px!important;
	width: 100%;
	color: #fff;
	font-weight: bold;
	font-size: 16px;
	text-shadow: gray 0 1px 0;
	line-height: 22px;
	padding-left: 20px;
	border-bottom: none!important;
}
.list ul {
	background-color: #fff;
	width: 100%;
	overflow: hidden;
	padding: 0;
	margin: 0;
}
.list #content li {
	height: 40px;
	border-bottom: 1px solid #e1e1e1;
	list-style: none;
}
.list {
	background-color: #fff;
	background-image: none!important;
}
.list #footer {
	margin-top: 24px!important;
}
.list #content li a {
	padding: 9px 0 0 20px;
	font-size: large;
	font-weight: bold;
	position: relative;
	display: block;
	color: #000;
	text-decoration: none;
	height: 32px;
}
.list #content li a .name {
	text-overflow: ellipsis;
	overflow: hidden;
	max-width: 93%;
	white-space: nowrap;
	display: block;
}
.list #content li a:hover {
	color: #fff;
}
.list #content {
	margin-top: -13px!important;
}
.list ul img {
	width: 90px;
	height: 90px;
	position: absolute;
	left: 0;
	top: 0;
}
.list .withimage {
	height: 90px!important;
}
.list .withimage .name {
	margin: 13px 0 0 90px;
	text-overflow: ellipsis;
	overflow: hidden;
	max-width: 63%!important;
	white-space: nowrap;
}
.list .withimage .comment {
	margin: 10px auto auto 90px !important;
	max-width: 63%!important;
}
.list .withimage a, .list .withimage:hover a {
	height: 81px!important;
}
#leftnav, #leftbutton, #blueleftbutton {
	position: absolute;
	font-size: 12px;
	left: 9px;
	font-weight: bold;
}
#leftnav, #leftbutton, #rightnav, #rightbutton, #blueleftbutton, #bluerightbutton {
	z-index: 5000;
}
#leftnav a, #rightnav a, #leftbutton a, #rightbutton a, #blueleftbutton a, #bluerightbutton a {
	display: block;
	color: #fff;
	text-shadow: rgba(0,0,0,0.6) 0 -1px 0;
	text-decoration: none;
}
.black #leftnav a:first-child, .transparent #leftnav a:first-child {
	-webkit-border-image: url("images/navleftblack.png") 0 5 0 13;
}
.black #leftnav a, .transparent #leftnav a {
	-webkit-border-image: url("images/navlinkleftblack.png") 0 5 0 13;
}
.black #rightnav a:first-child, .transparent #rightnav a:first-child {
	-webkit-border-image: url("images/navrightblack.png") 0 13 0 5;
}
.black #rightnav a, .transparent #rightnav a {
	-webkit-border-image: url("images/navlinkrightblack.png") 0 13 0 5;
}
.black #leftbutton a, .black #rightbutton a, .transparent #leftbutton a, .transparent #rightbutton a {
	-webkit-border-image: url("images/navbuttonblack.png") 0 5 0 5;
}
#leftnav a:first-child {
	z-index: 2;
	-webkit-border-image: url("images/navleft.png") 0 5 0 13;
	border-width: 0 5px 0 13px;
	-webkit-border-top-left-radius: 16px;
	-webkit-border-bottom-left-radius: 16px;
	-webkit-border-top-right-radius: 6px;
	-webkit-border-bottom-right-radius: 6px;
	width: auto;
}
#leftnav a {
	-webkit-border-image: url("images/navlinkleft.png") 0 5 0 13;
	z-index: 3;
	margin-left: -4px;
	border-width: 0 5px 0 13px;
	padding-right: 4px;
	-webkit-border-top-left-radius: 16px;
	-webkit-border-bottom-left-radius: 16px;
	-webkit-border-top-right-radius: 6px;
	-webkit-border-bottom-right-radius: 6px;
	float: left;
}
#rightnav, #rightbutton, #bluerightbutton {
	position: absolute;
	font-size: 12px;
	right: 9px;
	font-weight: bold;
}
#rightnav a {
	-webkit-border-image: url("images/navlinkright.png") 0 13 0 5;
	z-index: 3;
	margin-right: -4px;
	border-width: 0 13px 0 5px;
	padding-left: 4px;
	-webkit-border-top-left-radius: 6px;
	-webkit-border-bottom-left-radius: 6px;
	float: right;
	-webkit-border-top-right-radius: 16px;
	-webkit-border-bottom-right-radius: 16px;
}
#rightnav a:first-child {
	z-index: 2;
	-webkit-border-top-left-radius: 6px;
	-webkit-border-bottom-left-radius: 6px;
	-webkit-border-image: url("images/navright.png") 0 13 0 5;
	border-width: 0 13px 0 5px;
	-webkit-border-top-right-radius: 16px;
	-webkit-border-bottom-right-radius: 16px;
}
#leftbutton a, #rightbutton a {
	-webkit-border-image: url("images/navbutton.png") 0 5 0 5;
	border-width: 0 5px;
	-webkit-border-radius: 6px;
}
#blueleftbutton a, #bluerightbutton a {
	-webkit-border-image: url("images/navbuttonblue.png") 0 5 0 5;
	border-width: 0 5px;
	-webkit-border-radius: 6px;
}
input[type="checkbox"] {
	width: 94px;
	height: 27px;
	background: url(''images/checkbox.png'');
	-webkit-appearance: none;
	border: 0;
	float: right;
	margin: 8px 4px 0 0;
}
input[type="checkbox"]:checked {
	background-position: 0 27px;
}
input[type="radio"] {
	-webkit-appearance: none;
	border: 0;
	width: 100%;
	height: 100%;
	z-index: 2;
	position: absolute;
	left: 0;
	margin: 0;
	-webkit-border-radius: 0;
}
input[type="radio"]:checked {
	background: url(''images/radiobutton.png'') no-repeat;
	background-position: right center;
}
.radiobutton .name {
	z-index: 1;
}
select {
	-webkit-appearance: none;
	height: 100%;
	width: 100%;
	border: 0;
}
.select select {
	-webkit-border-radius: 0;
	color: #000;
	font-weight: bold;
	font-size: 17px;
}
.select option {
	max-width: 90%;
}
.select .arrow {
	background: url(''images/arrow.png'');
	width: 8px;
	height: 13px;
	display: block;
	-webkit-transform: rotate(90deg);
	position: absolute;
	right: 10px;
	top: 18px;
}
.button input {
	width: 100%;
	height: 100%;
	-webkit-appearance: none;
	border: 0;
	-webkit-border-radius: 0;
	font-weight: bold;
	font-size: 17px;
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
	background: none;
}
.textbox textarea {
	padding: 0;
	margin-top: 5px;
	font-size: medium;
}
.bigfield input {
	-webkit-appearance: none;
	border: 0;
	height: 100%;
	padding: 0;
	-webkit-border-radius: 0;
	background: transparent;
	font-weight: bold;
	font-size: 17px;
	padding-left: 5px;
}
.bigfield textarea {
	border: 0;
}
.smallfield .name {
	width: 48%;
	position: absolute;
	left: 0;
	font-size: 17px;
	text-overflow: ellipsis;
	white-space: nowrap;
	font-weight: bold;
	line-height: 44px;
	font-size: 17px;
	padding-left: 5px;
	overflow: hidden;
}
.smallfield input {
	width: 50%;
	position: absolute;
	right: 0;
	height: 44px;
	-webkit-appearance: none;
	border: none;
	padding: 0;
	background: transparent;
	-webkit-border-radius: 0;
	font-weight: bold;
	font-size: 17px;
}
.smallfield:first-child input {
	-webkit-border-top-right-radius: 8px;
}
.smallfield:last-child input {
	-webkit-border-bottom-right-radius: 8px;
}
');

-- ----------------------------
-- Records of phpbb_topics
-- ----------------------------
INSERT INTO "phpbb_topics" VALUES ('1', '2', '0', '0', '1', '0', 'Welcome to phpBB3', '2', '1297685283', '0', '3', '1', '1', '0', '0', '1', 'catroweb', 'AA0000', '2', '2', 'catroweb', 'AA0000', 'Re: Welcome to phpBB3', '1297838793', '1297838793', '0', '0', '0', '', '0', '0', '1', '0', '0');

-- ----------------------------
-- Records of phpbb_topics_posted
-- ----------------------------
INSERT INTO "phpbb_topics_posted" VALUES ('2', '1', '1');

-- ----------------------------
-- Records of phpbb_users
-- ----------------------------
INSERT INTO "phpbb_users" VALUES ('1', '2', '1', '00000000003khra3nk
i1cjyo000000
i1cjyo000000', '0', '', '1297685283', 'Anonymous', 'anonymous', '', '0', '0', '', '0', '', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'd M Y H:i', '1', '0', '', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '4db8fa7cbde751c4', '1', '0', '0');
INSERT INTO "phpbb_users" VALUES ('2', '3', '5', 'zik0zjzik0zjzik0xs
i1cjyo000000
zik0zjzhb2tc', '0', '127.0.0.1', '1297685283', 'catroweb', 'catroweb', '$H$976Rx3sts/5F0uwEOePiid7zIhHulS0', '0', '0', 'webmaster@catroid.org', '61496799921', '', '1297848269', '0', '1297838793', '../../index.php?module=catroid&class=login', '', '0', '0', '0', '0', '0', '0', '2', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '1', 'AA0000', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '1', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'a569790c1b878853', '1', '0', '0');
INSERT INTO "phpbb_users" VALUES ('3', '2', '6', '', '0', '', '1297685298', 'AdsBot [Google]', 'adsbot [google]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '8d729010378fcb7e', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('4', '2', '6', '', '0', '', '1297685298', 'Alexa [Bot]', 'alexa [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '6e4e8ca13aeac1a0', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('5', '2', '6', '', '0', '', '1297685298', 'Alta Vista [Bot]', 'alta vista [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'b4c9a064df56969e', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('6', '2', '6', '', '0', '', '1297685298', 'Ask Jeeves [Bot]', 'ask jeeves [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'd379a6090df5111f', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('7', '2', '6', '', '0', '', '1297685298', 'Baidu [Spider]', 'baidu [spider]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'c41334eef7a4babb', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('8', '2', '6', '', '0', '', '1297685298', 'Bing [Bot]', 'bing [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '8dc92377cd78a37a', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('9', '2', '6', '', '0', '', '1297685298', 'Exabot [Bot]', 'exabot [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'dbbd3ffe33f3a5b1', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('10', '2', '6', '', '0', '', '1297685298', 'FAST Enterprise [Crawler]', 'fast enterprise [crawler]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '8f59ee393c03be57', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('11', '2', '6', '', '0', '', '1297685298', 'FAST WebCrawler [Crawler]', 'fast webcrawler [crawler]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'cbd65d84f8f54f06', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('12', '2', '6', '', '0', '', '1297685298', 'Francis [Bot]', 'francis [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '59945133381cc82f', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('13', '2', '6', '', '0', '', '1297685298', 'Gigabot [Bot]', 'gigabot [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'a46a93e45223ebe9', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('14', '2', '6', '', '0', '', '1297685298', 'Google Adsense [Bot]', 'google adsense [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '7b1593a874c4cdef', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('15', '2', '6', '', '0', '', '1297685298', 'Google Desktop', 'google desktop', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '922d7aff32386757', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('16', '2', '6', '', '0', '', '1297685298', 'Google Feedfetcher', 'google feedfetcher', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'b890604e666c88b3', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('17', '2', '6', '', '0', '', '1297685298', 'Google [Bot]', 'google [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'a5c86f30678b946b', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('18', '2', '6', '', '0', '', '1297685298', 'Heise IT-Markt [Crawler]', 'heise it-markt [crawler]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '05d25c9e3c5cc61e', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('19', '2', '6', '', '0', '', '1297685298', 'Heritrix [Crawler]', 'heritrix [crawler]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '486366265774f4bc', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('20', '2', '6', '', '0', '', '1297685298', 'IBM Research [Bot]', 'ibm research [bot]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '290d7c192bcbb753', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('21', '2', '6', '', '0', '', '1297685298', 'ICCrawler - ICjobs', 'iccrawler - icjobs', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '5e8846d4635b38d5', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('22', '2', '6', '', '0', '', '1297685298', 'ichiro [Crawler]', 'ichiro [crawler]', '', '1297685298', '0', '', '0', '', '0', '1297685298', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '923f36e0d2a78867', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('23', '2', '6', '', '0', '', '1297685299', 'Majestic-12 [Bot]', 'majestic-12 [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'ed4466416463ff7f', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('24', '2', '6', '', '0', '', '1297685299', 'Metager [Bot]', 'metager [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '620de9d333139943', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('25', '2', '6', '', '0', '', '1297685299', 'MSN NewsBlogs', 'msn newsblogs', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '23cbbe9d8995c030', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('26', '2', '6', '', '0', '', '1297685299', 'MSN [Bot]', 'msn [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '74c4df483e4131bb', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('27', '2', '6', '', '0', '', '1297685299', 'MSNbot Media', 'msnbot media', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '32fea746709472df', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('28', '2', '6', '', '0', '', '1297685299', 'NG-Search [Bot]', 'ng-search [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'b2af70006eea5e58', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('29', '2', '6', '', '0', '', '1297685299', 'Nutch [Bot]', 'nutch [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'b203c60592674875', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('30', '2', '6', '', '0', '', '1297685299', 'Nutch/CVS [Bot]', 'nutch/cvs [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'be1255cd86d42f2d', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('31', '2', '6', '', '0', '', '1297685299', 'OmniExplorer [Bot]', 'omniexplorer [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '8e851f761c0c907d', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('32', '2', '6', '', '0', '', '1297685299', 'Online link [Validator]', 'online link [validator]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'be578c20ce7c5263', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('33', '2', '6', '', '0', '', '1297685299', 'psbot [Picsearch]', 'psbot [picsearch]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '8dc2f003feec7ee7', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('34', '2', '6', '', '0', '', '1297685299', 'Seekport [Bot]', 'seekport [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'c834b932f2a74fb2', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('35', '2', '6', '', '0', '', '1297685299', 'Sensis [Crawler]', 'sensis [crawler]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'ca0ffe640eb863c9', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('36', '2', '6', '', '0', '', '1297685299', 'SEO Crawler', 'seo crawler', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'e995b526d5c451bf', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('37', '2', '6', '', '0', '', '1297685299', 'Seoma [Crawler]', 'seoma [crawler]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '6ecb8609863772d2', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('38', '2', '6', '', '0', '', '1297685299', 'SEOSearch [Crawler]', 'seosearch [crawler]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'd651fc53c8e65bfe', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('39', '2', '6', '', '0', '', '1297685299', 'Snappy [Bot]', 'snappy [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'fc1801b078cd85ec', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('40', '2', '6', '', '0', '', '1297685299', 'Steeler [Crawler]', 'steeler [crawler]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '98b486a943a40e38', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('41', '2', '6', '', '0', '', '1297685299', 'Synoo [Bot]', 'synoo [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'bc78adfe431795f8', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('42', '2', '6', '', '0', '', '1297685299', 'Telekom [Bot]', 'telekom [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '5bc2018d3a925e05', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('43', '2', '6', '', '0', '', '1297685299', 'TurnitinBot [Bot]', 'turnitinbot [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '5b5aa1d51b481262', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('44', '2', '6', '', '0', '', '1297685299', 'Voyager [Bot]', 'voyager [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '6b5a61f4190f1aec', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('45', '2', '6', '', '0', '', '1297685299', 'W3 [Sitesearch]', 'w3 [sitesearch]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'fed243d5488ae8ec', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('46', '2', '6', '', '0', '', '1297685299', 'W3C [Linkcheck]', 'w3c [linkcheck]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'd41ecc2e01e5e47f', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('47', '2', '6', '', '0', '', '1297685299', 'W3C [Validator]', 'w3c [validator]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'a50c24608c384ba2', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('48', '2', '6', '', '0', '', '1297685299', 'WiseNut [Bot]', 'wisenut [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '3dc11ab9b0f8ff66', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('49', '2', '6', '', '0', '', '1297685299', 'YaCy [Bot]', 'yacy [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '906a9de9ffe6f750', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('50', '2', '6', '', '0', '', '1297685299', 'Yahoo MMCrawler [Bot]', 'yahoo mmcrawler [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'e43cd509d6a84eb8', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('51', '2', '6', '', '0', '', '1297685299', 'Yahoo Slurp [Bot]', 'yahoo slurp [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '6dd269dbecf4e2ac', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('52', '2', '6', '', '0', '', '1297685299', 'Yahoo [Bot]', 'yahoo [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '77e72c8ce27a7e8d', '0', '0', '0');
INSERT INTO "phpbb_users" VALUES ('53', '2', '6', '', '0', '', '1297685299', 'YahooSeeker [Bot]', 'yahooseeker [bot]', '', '1297685299', '0', '', '0', '', '0', '1297685299', '0', '', '', '0', '0', '0', '0', '0', '0', '0', 'en', '0.00', '0', 'D M d, Y g:i a', '1', '0', '9E8DA7', '0', '0', '0', '0', '-3', '0', '0', 't', 'd', '0', 't', 'a', '0', '1', '0', '1', '1', '1', '0', '230271', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '5ce30a0ebc225327', '0', '0', '0');

-- ----------------------------
-- Records of phpbb_user_group
-- ----------------------------
INSERT INTO "phpbb_user_group" VALUES ('1', '1', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('2', '2', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('4', '2', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('5', '2', '1', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '3', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '4', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '5', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '6', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '7', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '8', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '9', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '10', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '11', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '12', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '13', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '14', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '15', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '16', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '17', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '18', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '19', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '20', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '21', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '22', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '23', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '24', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '25', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '26', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '27', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '28', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '29', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '30', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '31', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '32', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '33', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '34', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '35', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '36', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '37', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '38', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '39', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '40', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '41', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '42', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '43', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '44', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '45', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '46', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '47', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '48', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '49', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '50', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '51', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '52', '0', '0');
INSERT INTO "phpbb_user_group" VALUES ('6', '53', '0', '0');

select setval('phpbb_acl_options_seq', (select max(auth_option_id) from phpbb_acl_options) + 1); 
select setval('phpbb_acl_roles_seq', (select max(role_id) from phpbb_acl_roles) + 1); 
select setval('phpbb_attachments_seq', (select max(attach_id) from phpbb_attachments) + 1); 
select setval('phpbb_banlist_seq', (select max(ban_id) from phpbb_banlist) + 1); 
select setval('phpbb_bots_seq', (select max(bot_id) from phpbb_bots) + 1); 
select setval('phpbb_disallow_seq', (select max(disallow_id) from phpbb_disallow) + 1); 
select setval('phpbb_drafts_seq', (select max(draft_id) from phpbb_drafts) + 1); 
select setval('phpbb_extensions_seq', (select max(extension_id) from phpbb_extensions) + 1); 
select setval('phpbb_extension_groups_seq', (select max(group_id) from phpbb_extension_groups) + 1); 
select setval('phpbb_forums_seq', (select max(forum_id) from phpbb_forums) + 1); 
select setval('phpbb_groups_seq', (select max(group_id) from phpbb_groups) + 1); 
select setval('phpbb_icons_seq', (select max(icons_id) from phpbb_icons) + 1);
select setval('phpbb_lang_seq', (select max(lang_id) from phpbb_lang) + 1);
select setval('phpbb_log_seq', (select max(log_id) from phpbb_log) + 1);
select setval('phpbb_modules_seq', (select max(module_id) from phpbb_modules) + 1);
select setval('phpbb_posts_seq', (select max(post_id) from phpbb_posts) + 1);
select setval('phpbb_privmsgs_folder_seq', (select max(folder_id) from phpbb_privmsgs_folder) + 1);
select setval('phpbb_privmsgs_rules_seq', (select max(rule_id) from phpbb_privmsgs_rules) + 1);
select setval('phpbb_privmsgs_seq', (select max(msg_id) from phpbb_privmsgs) + 1);
select setval('phpbb_profile_fields_seq', (select max(field_id) from phpbb_profile_fields) + 1);
select setval('phpbb_ranks_seq', (select max(rank_id) from phpbb_ranks) + 1);
select setval('phpbb_reports_reasons_seq', (select max(reason_id) from phpbb_reports_reasons) + 1);
select setval('phpbb_reports_seq', (select max(report_id) from phpbb_reports) + 1);
select setval('phpbb_search_wordlist_seq', (select max(word_id) from phpbb_search_wordlist) + 1);
select setval('phpbb_sitelist_seq', (select max(site_id) from phpbb_sitelist) + 1);
select setval('phpbb_smilies_seq', (select max(smiley_id) from phpbb_smilies) + 1);
select setval('phpbb_styles_imageset_data_seq', (select max(image_id) from phpbb_styles_imageset_data) + 1);
select setval('phpbb_styles_imageset_seq', (select max(imageset_id) from phpbb_styles_imageset) + 1);
select setval('phpbb_styles_seq', (select max(style_id) from phpbb_styles) + 1);
select setval('phpbb_styles_template_seq', (select max(template_id) from phpbb_styles_template) + 1);
select setval('phpbb_styles_theme_seq', (select max(theme_id) from phpbb_styles_theme) + 1);
select setval('phpbb_topics_seq', (select max(topic_id) from phpbb_topics) + 1);
select setval('phpbb_users_seq', (select max(user_id) from phpbb_users) + 1);
select setval('phpbb_warnings_seq', (select max(warning_id) from phpbb_warnings) + 1);
select setval('phpbb_words_seq', (select max(word_id) from phpbb_words) + 1);
