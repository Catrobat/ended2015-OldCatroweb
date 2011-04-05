-- Column: recovery_time
ALTER TABLE cusers ADD COLUMN recovery_time integer; -- (+) cusers.recovery_time

-- Column: recovery_hash
ALTER TABLE cusers ADD COLUMN recovery_hash character varying(32); -- (+) cusers.recovery_hash

