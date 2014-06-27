--DROP TABLE IF EXISTS "public"."intern_tagging" CASCADE;
--DROP SEQUENCE IF EXISTS intern_tagging_id_seq CASCADE;

CREATE SEQUENCE intern_tagging_id_seq;
CREATE TABLE "public"."intern_tagging" (
"id" int4 DEFAULT nextval('intern_tagging_id_seq'::regclass) NOT NULL,
"name" varchar(255) DEFAULT NULL NOT NULL
)
WITH (OIDS=FALSE)
;;

--DROP TABLE IF EXISTS "public"."intern_tagging_reference" CASCADE;
CREATE TABLE "public"."intern_tagging_reference" (
"id_tag" int4 DEFAULT NULL NOT NULL,
"id_project" int4 DEFAULT NULL NOT NULL
)
WITH (OIDS=FALSE)
;;
