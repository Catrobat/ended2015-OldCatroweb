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

Date: 2011-03-17 15:35:39
*/


-- ----------------------------
-- Table structure for "public"."cusers"
-- ----------------------------
CREATE SEQUENCE cusers_id_seq;
CREATE TABLE "public"."cusers" (
"id" int4 DEFAULT nextval('cusers_id_seq'::regclass) NOT NULL,
"username" varchar(255) DEFAULT NULL NOT NULL,
"password" varchar(255) DEFAULT NULL NOT NULL,
"email" varchar(255) DEFAULT NULL NOT NULL,
"date_of_birth" timestamp DEFAULT NULL,
"gender" varchar(16) DEFAULT NULL,
"country" varchar(5) DEFAULT NULL NOT NULL,
"province" varchar(255) DEFAULT NULL,
"city" varchar(255) DEFAULT NULL,
"time_registered" timestamptz DEFAULT now() NOT NULL,
"ip_registered" varchar(255) DEFAULT NULL NOT NULL,
"username_clean" varchar(255) DEFAULT NULL NOT NULL,
"status" varchar(255) DEFAULT NULL,
CONSTRAINT "cusers_pkey" PRIMARY KEY ("id"),
CONSTRAINT "username_unique" UNIQUE ("username"),
CONSTRAINT "username_clean_unique" UNIQUE ("username_clean")
)
WITH (OIDS=FALSE)
;;

-- ----------------------------
-- Records of cusers
-- ----------------------------
INSERT INTO "public"."cusers" VALUES ('0', 'anonymous', '', '', null, null, '', null, null, '2011-03-15 09:31:54.983+01', '', 'anonymous', 'active');
INSERT INTO "public"."cusers" VALUES ('1', 'catroweb', '7fdb2542b9d9260a63756eea1d377e6c', 'webmaster@catroid.org', null, null, 'AT', null, null, '2011-03-15 09:31:54.983+01', '127.0.0.1', 'catroweb', 'active');

