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

Date: 2011-03-17 15:36:24
*/


-- ----------------------------
-- Table structure for "public"."unapproved_words_in_projects"
-- ----------------------------
CREATE SEQUENCE unapproved_words_in_projects_id_seq;
CREATE TABLE "public"."unapproved_words_in_projects" (
"id" int4 DEFAULT nextval('unapproved_words_in_projects_id_seq'::regclass) NOT NULL,
"project_id" int4 NOT NULL,
"word_id" int4 NOT NULL,
PRIMARY KEY ("id"),
CONSTRAINT "projectId" FOREIGN KEY ("project_id") REFERENCES "public"."projects" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT "wordId" FOREIGN KEY ("word_id") REFERENCES "public"."wordlist" ("id") ON DELETE CASCADE ON UPDATE CASCADE 
)
WITH (OIDS=FALSE)
;;
