-- Table: blocked_cusers

CREATE SEQUENCE cusers_additional_email_id_seq;
CREATE TABLE cusers_additional_email
(
id int4 DEFAULT nextval('cusers_additional_email_id_seq'::regclass) NOT NULL,
cusers_id int4 NOT NULL,
additional_email character varying(255) DEFAULT NULL NOT NULL,
additional_email_count int4 NOT NULL,
PRIMARY KEY ("id"),
CONSTRAINT "cusersID" FOREIGN KEY ("cusers_id") REFERENCES "public"."cusers" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (
  OIDS=FALSE
)
;;