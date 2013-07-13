-- ----------------------------
-- Table structure for "public"."projects_tags"
-- ----------------------------

CREATE TABLE "public"."projects_tags" (
	"project_id" int4,
	"tag_id" int4,
	PRIMARY KEY ("project_id","tag_id"),
	CONSTRAINT "projectID" FOREIGN KEY ("project_id") REFERENCES "public"."projects" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT "tagID" FOREIGN KEY ("tag_id") REFERENCES "public"."tags" ("id") ON DELETE CASCADE ON UPDATE CASCADE
	)
	WITH (OIDS = FALSE)
;;


-- --------------------------------------------
-- Inserting sample values
-- --------------------------------------------
INSERT INTO "public"."projects_tags" VALUES ('1','1');
INSERT INTO "public"."projects_tags" VALUES ('1','2');
INSERT INTO "public"."projects_tags" VALUES ('1','3');
INSERT INTO "public"."projects_tags" VALUES ('1','5');
INSERT INTO "public"."projects_tags" VALUES ('2','1');
INSERT INTO "public"."projects_tags" VALUES ('2','2');

