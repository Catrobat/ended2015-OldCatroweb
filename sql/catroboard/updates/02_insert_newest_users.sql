INSERT INTO phpbb_config VALUES ('newest_user_id','',1);
INSERT INTO phpbb_config VALUES ('newest_username','',1);

/*
	Table: 'phpbb_login_attempts'
*/
CREATE TABLE phpbb_login_attempts (
	attempt_ip varchar(40) DEFAULT '' NOT NULL,
	attempt_browser varchar(150) DEFAULT '' NOT NULL,
	attempt_forwarded_for varchar(255) DEFAULT '' NOT NULL,
	attempt_time INT4 DEFAULT '0' NOT NULL CHECK (attempt_time >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	username varchar(255) DEFAULT '0' NOT NULL,
	username_clean varchar_ci DEFAULT '0' NOT NULL
);

CREATE INDEX phpbb_login_attempts_att_ip ON phpbb_login_attempts (attempt_ip, attempt_time);
CREATE INDEX phpbb_login_attempts_att_for ON phpbb_login_attempts (attempt_forwarded_for, attempt_time);
CREATE INDEX phpbb_login_attempts_att_time ON phpbb_login_attempts (attempt_time);
CREATE INDEX phpbb_login_attempts_user_id ON phpbb_login_attempts (user_id);
