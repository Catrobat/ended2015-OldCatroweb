/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
/*
PGSQL Backup
Source Server Version: 8.3.12
Source Database: catroweb
Date: 30.11.2010 14:57:30
*/


-- ----------------------------
--  Table structure for "public"."projects"
-- ----------------------------
DROP TABLE IF EXISTS "public"."projects";
DROP SEQUENCE IF EXISTS projects_id_seq;
CREATE SEQUENCE projects_id_seq;
CREATE TABLE "public"."projects" (
"id" int4 DEFAULT nextval('projects_id_seq'::regclass) NOT NULL,
"title" varchar(255) DEFAULT NULL,
"description" text DEFAULT NULL,
"source" varchar(255) DEFAULT NULL,
"upload_time" timestamptz DEFAULT now(),
"upload_ip" varchar(255) DEFAULT NULL,
"download_count" int4 DEFAULT 0,
PRIMARY KEY ("id")
)
WITH (OIDS=FALSE)
;;

-- ----------------------------
--  Records 
-- ----------------------------
