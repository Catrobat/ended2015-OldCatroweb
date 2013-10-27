--DROP TABLE IF EXISTS "public"."starter_projects" CASCADE;
--DROP SEQUENCE IF EXISTS starter_projects_id_seq CASCADE;

CREATE SEQUENCE starter_projects_id_seq;
CREATE TABLE "public"."starter_projects" (
"id" int4 DEFAULT nextval('starter_projects_id_seq'::regclass) NOT NULL,
"project_id" int4 DEFAULT 0,
"group" int4 DEFAULT 0,
"updated_time" timestamptz DEFAULT now() NOT NULL,
"visible" bool DEFAULT true,
CONSTRAINT "starter_projects_pkey" PRIMARY KEY ("id"),
CONSTRAINT "sprojectId" FOREIGN KEY ("project_id") REFERENCES "public"."projects" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (OIDS=FALSE)
;;

