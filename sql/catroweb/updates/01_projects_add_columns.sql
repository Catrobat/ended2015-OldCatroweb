--
-- alter table: projects
-- 
ALTER TABLE projects ADD COLUMN view_count integer; ALTER TABLE projects ALTER COLUMN view_count SET DEFAULT 0; -- (+) projects.view_count 
ALTER TABLE projects ADD COLUMN visible boolean; ALTER TABLE projects ALTER COLUMN visible SET DEFAULT true; -- (+) projects.visible
ALTER TABLE projects ADD COLUMN user_id integer; ALTER TABLE projects ALTER COLUMN user_id SET DEFAULT 0; -- (+) projects.user_id

