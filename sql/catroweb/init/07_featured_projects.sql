--DROP TABLE IF EXISTS "public"."featured_projects" CASCADE;
--DROP SEQUENCE IF EXISTS featured_projects_id_seq CASCADE;

CREATE SEQUENCE featured_projects_id_seq;
CREATE TABLE "public"."featured_projects" (
"id" int4 DEFAULT nextval('featured_projects_id_seq'::regclass) NOT NULL,
"project_id" int4 DEFAULT 0,
"update_time" timestamptz DEFAULT now() NOT NULL,
"visible" bool DEFAULT true,
CONSTRAINT "featured_projects_pkey" PRIMARY KEY ("id"),
CONSTRAINT "fprojectId" FOREIGN KEY ("project_id") REFERENCES "public"."projects" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (OIDS=FALSE)
;;

INSERT INTO featured_projects VALUES (0, 1, '2013-03-04 05:06:07.89+01', true);
