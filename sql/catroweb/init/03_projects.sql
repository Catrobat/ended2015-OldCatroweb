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

Date: 2011-03-17 15:35:52
*/


-- ----------------------------
-- Table structure for "public"."projects"
-- ----------------------------
CREATE SEQUENCE projects_id_seq;
CREATE TABLE "public"."projects" (
"id" int4 DEFAULT nextval('projects_id_seq'::regclass) NOT NULL,
"title" varchar(255) DEFAULT NULL,
"description" text DEFAULT NULL,
"source" varchar(255) DEFAULT NULL,
"upload_time" timestamptz DEFAULT now(),
"upload_ip" varchar(255) DEFAULT NULL,
"download_count" int4 DEFAULT 0,
"view_count" int4 DEFAULT 0,
"visible" bool DEFAULT true,
"user_id" int4 DEFAULT 0,
PRIMARY KEY ("id"),
CONSTRAINT "userId" FOREIGN KEY ("user_id") REFERENCES "public"."cusers" ("id") ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (OIDS=FALSE)
;;
