-- Column: auth_token
ALTER TABLE cusers ADD COLUMN auth_token character varying(255) DEFAULT '0'; -- (+) cusers.auth_token
