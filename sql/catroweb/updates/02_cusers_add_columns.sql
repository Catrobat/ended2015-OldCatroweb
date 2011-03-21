-- Column: recovery_time
ALTER TABLE cusers ADD COLUMN recovery_time integer;

-- Column: recovery_hash
ALTER TABLE cusers ADD COLUMN recovery_hash character varying(32);
