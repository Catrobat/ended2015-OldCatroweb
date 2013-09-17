/*
Navicat PGSQL Data Transfer

Source Server         : local
Source Server Version : 80407
Source Host           : localhost:5432
Source Database       : catroweb
Source Schema         : public

Target Server Type    : PGSQL
Target Server Version : 80407
File Encoding         : 65001

Date: 2011-03-17 15:36:52
*/


-- ----------------------------
-- Table structure for "public"."flagged_projects"
-- ----------------------------
-- DROP TABLE IF EXISTS "public"."flagged_projects" CASCADE;
-- DROP SEQUENCE IF EXISTS flagged_projects_id_seq CASCADE;
CREATE SEQUENCE flagged_projects_id_seq;
CREATE TABLE "public"."flagged_projects" (
"id" int4 DEFAULT nextval('flagged_projects_id_seq'::regclass) NOT NULL,
"project_id" int4 NOT NULL,
"user_ip" varchar(255) DEFAULT NULL,
"reason" text DEFAULT NULL,
"time" timestamptz DEFAULT now(),
"mail_sent" bool DEFAULT false,
"resolved" bool DEFAULT false,
PRIMARY KEY ("id"),
CONSTRAINT "projectId" FOREIGN KEY ("project_id") REFERENCES "public"."projects" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (OIDS=FALSE)
;;
