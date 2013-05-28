ALTER TABLE cusers RENAME COLUMN add_email TO additional_email;
ALTER TABLE cusers RENAME COLUMN add_email_validated TO additional_email_validated;
ALTER TABLE cusers RENAME COLUMN add_email_validation_hash TO email_validation_hash;

ALTER TABLE cusers ALTER COLUMN email_validation_hash TYPE character varying(40) USING (email_validation_hash::character varying(40));



ALTER TABLE cusers DROP COLUMN date_of_birth;
ALTER TABLE cusers DROP COLUMN gender;
ALTER TABLE cusers DROP COLUMN city;

DROP TABLE cusers_additional_email;

