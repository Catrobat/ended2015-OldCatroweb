-- Column: validated
ALTER TABLE cusers_additional_email ADD COLUMN validated bool DEFAULT false;
UPDATE "public"."cusers_additional_email" SET validated=TRUE WHERE user_id=1;
