ALTER TABLE cusers ADD COLUMN salt character varying(8); -- (+) cusers.salt
ALTER TABLE cusers ALTER COLUMN auth_token TYPE character varying(32) USING (auth_token::character varying(32));

UPDATE cusers SET password='8f63ec658d2b3abaad603f98e30c49d6766ea305', salt='YPvfsLOX' WHERE id=1;
