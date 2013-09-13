
-- ----------------------------
-- Table structure for "public"."tags"
-- ----------------------------

CREATE SEQUENCE tags_id_seq;
CREATE TABLE "public"."tags" (
	"id" int4 DEFAULT nextval('tags_id_seq'::regclass) NOT NULL,
	"tag_name" varchar(255) DEFAULT NULL,

	PRIMARY KEY ("id")
	)
	WITH (OIDS = FALSE)
;;


-- --------------------------------------------
-- Inserting sample values
-- --------------------------------------------
INSERT INTO "public"."tags" VALUES ('1', 'test');
INSERT INTO "public"."tags" VALUES ('2', 'ball');
INSERT INTO "public"."tags" VALUES ('3', 'sample');
INSERT INTO "public"."tags" VALUES ('4', 'native');
INSERT INTO "public"."tags" VALUES ('5', 'airplane');
