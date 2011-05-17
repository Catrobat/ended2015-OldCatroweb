-- Column: version_name
ALTER TABLE projects ADD COLUMN version_name character varying(8); -- (+) projects.version_name

-- Column: version_code
ALTER TABLE projects ADD COLUMN version_code integer DEFAULT 4; -- (+) projects.version_code