-- Column: remix0f
ALTER TABLE projects ADD COLUMN remix_count integer DEFAULT 0; -- (+) projects.remix_count
ALTER TABLE projects ADD COLUMN approved bool DEFAULT false; -- (+) projects.approved
