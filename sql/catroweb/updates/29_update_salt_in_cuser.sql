ALTER TABLE cusers DROP COLUMN salt; -- (+) cusers.salt
ALTER TABLE cusers ALTER COLUMN password TYPE character varying(60) USING (password::character varying(60));
ALTER TABLE cusers ALTER COLUMN recovery_hash TYPE character varying(40) USING (recovery_hash::character varying(40));
ALTER TABLE cusers_additional_email ALTER COLUMN validation_hash TYPE character varying(40) USING (validation_hash::character varying(40));

UPDATE cusers SET password='$2a$11$em8/16sTsaBwbbU1LAnPweyp3o7ZNqb6uv1Z7jP9iQtyVtF6aFO6W' WHERE id=1;
UPDATE cusers SET password='$2a$11$p6Ja1wdIr8rd6UKOHi0sUuliMJaKnyNcSwpAg2dR2NHQCkK5Lk6Lq' WHERE id=2;
