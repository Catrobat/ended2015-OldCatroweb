-- Column: additional_email
ALTER TABLE cusers ADD COLUMN add_email varchar(255) DEFAULT NULL; -- (+) cusers.add_email

-- Column: validated
ALTER TABLE cusers ADD COLUMN add_email_validated bool DEFAULT false; -- (+) cusers.add_email_validated
ALTER TABLE cusers ADD COLUMN add_email_validation_hash character varying(32) DEFAULT ''; -- (+) cusers.add_email_validation_hash

UPDATE "public"."cusers" SET add_email='admin@catroid.org', add_email_validated=TRUE WHERE id=1;

