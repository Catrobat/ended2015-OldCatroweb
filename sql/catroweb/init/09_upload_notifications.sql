--DROP TABLE IF EXISTS "public"."upload_notifications" CASCADE;
--DROP SEQUENCE IF EXISTS upload_notifications_id_seq CASCADE;

CREATE SEQUENCE upload_notifications_id_seq;
CREATE TABLE "public"."upload_notifications" (
"id" int4 DEFAULT nextval('upload_notifications_id_seq'::regclass) NOT NULL,
"email" varchar(255) DEFAULT NULL NOT NULL
)
WITH (OIDS=FALSE)
;;

