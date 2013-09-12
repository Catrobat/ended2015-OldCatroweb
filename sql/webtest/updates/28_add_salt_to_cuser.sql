ALTER TABLE cusers ADD COLUMN salt character varying(8); -- (+) cusers.salt
ALTER TABLE cusers ALTER COLUMN auth_token TYPE character varying(32) USING (auth_token::character varying(32));

UPDATE cusers SET password='8f63ec658d2b3abaad603f98e30c49d6766ea305', salt='YPvfsLOX' WHERE id=1;
INSERT INTO cusers VALUES ('2', 'catroid', 'bd7ea35ef1a27ee42914d55f50a0ee37345fb1c1', 'catroid@catroid.org', null, null, 'AT', null, '2011-03-15 09:31:54.983+01', '127.0.0.1', 'catroid', 'active', null, null, 'dbfbd45e6a9f05e3250d027448faf78', null, null, 'AdfkYaDS');
