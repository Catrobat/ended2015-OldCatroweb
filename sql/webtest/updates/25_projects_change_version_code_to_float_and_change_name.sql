-- MODIFY Column: version_code
ALTER TABLE projects ALTER COLUMN version_code TYPE character varying(255);
ALTER TABLE projects RENAME COLUMN version_code TO language_code;
