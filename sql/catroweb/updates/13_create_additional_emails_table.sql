-- Table: blocked_cusers

CREATE SEQUENCE cusers_additional_email_id_seq;
CREATE TABLE cusers_additional_email
(
id int4 DEFAULT nextval('cusers_additional_email_id_seq'::regclass) NOT NULL,
user_id int4 NOT NULL,
email character varying(255) DEFAULT NULL NOT NULL,
email_count int4 NOT NULL,
PRIMARY KEY ("id"),
CONSTRAINT "cusersId" FOREIGN KEY ("user_id") REFERENCES "public"."cusers" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (
  OIDS=FALSE
)
;;