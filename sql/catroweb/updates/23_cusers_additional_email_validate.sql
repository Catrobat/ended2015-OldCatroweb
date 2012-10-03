-- Column: validated
ALTER TABLE cusers_additional_email ADD COLUMN validated bool DEFAULT false;
ALTER TABLE cusers_additional_email ADD COLUMN validation_hash character varying(32) DEFAULT '';
UPDATE "public"."cusers_additional_email" SET validated=TRUE WHERE user_id=1;
