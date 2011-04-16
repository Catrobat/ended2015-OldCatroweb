-- Column: upload_imei
-- Column: upload_email
-- Column: upload_language
ALTER TABLE projects ADD COLUMN upload_imei character varying(32) DEFAULT ''; -- (+) projects.upload_imei
ALTER TABLE projects ADD COLUMN upload_email character varying(255) DEFAULT ''; -- (+) projects.upload_email
ALTER TABLE projects ADD COLUMN upload_language character varying(4) DEFAULT ''; -- (+) projects.upload_language
