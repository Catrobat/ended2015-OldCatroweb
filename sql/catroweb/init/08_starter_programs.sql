--DROP TABLE IF EXISTS "public"."starter_projects" CASCADE;
--DROP SEQUENCE IF EXISTS starter_projects_id_seq CASCADE;

CREATE SEQUENCE starter_projects_id_seq;
CREATE TABLE "public"."starter_projects" (
"id" int4 DEFAULT nextval('starter_projects_id_seq'::regclass) NOT NULL,
"project_id" int4 DEFAULT 0,
"group" int4 DEFAULT 0,
"update_time" timestamptz DEFAULT now() NOT NULL,
"visible" bool DEFAULT true,
CONSTRAINT "starter_projects_pkey" PRIMARY KEY ("id"),
CONSTRAINT "sprojectId" FOREIGN KEY ("project_id") REFERENCES "public"."projects" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (OIDS=FALSE)
;;

INSERT INTO starter_projects VALUES (1, 1, 1, '2013-03-04 05:06:07.89+01', true);
INSERT INTO starter_projects VALUES (2, 1, 2, '2013-03-04 05:06:07.89+01', true);
INSERT INTO starter_projects VALUES (3, 1, 3, '2013-03-04 05:06:07.89+01', true);
