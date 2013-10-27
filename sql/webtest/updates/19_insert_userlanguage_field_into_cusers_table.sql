-- Column: language
ALTER TABLE cusers ADD COLUMN language character varying(8); -- (+) cusers.langage
-- ----------------------------
-- Records of cusers
-- ----------------------------
UPDATE cusers SET language = 'en' WHERE id=0;
