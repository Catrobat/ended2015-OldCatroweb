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

Date: 2011-03-17 15:35:19
*/


-- ----------------------------
-- Table structure for "public"."countries"
-- ----------------------------
CREATE TABLE "public"."countries" (
"name" varchar(50) NOT NULL,
"code" varchar(5) NOT NULL,
"language" varchar(5) NOT NULL,

PRIMARY KEY ("code"))
WITH (OIDS=FALSE)

;;

-- ----------------------------
-- Records of countries
-- ----------------------------
INSERT INTO "public"."countries" VALUES ('ANDORRA', 'AD', 'en');
INSERT INTO "public"."countries" VALUES ('UNITED ARAB EMIRATES', 'AE', 'en');
INSERT INTO "public"."countries" VALUES ('AFGHANISTAN', 'AF', 'en');
INSERT INTO "public"."countries" VALUES ('ANTIGUA AND BARBUDA', 'AG', 'en');
INSERT INTO "public"."countries" VALUES ('ANGUILLA', 'AI', 'en');
INSERT INTO "public"."countries" VALUES ('ALBANIA', 'AL', 'en');
INSERT INTO "public"."countries" VALUES ('ARMENIA', 'AM', 'en');
INSERT INTO "public"."countries" VALUES ('NETHERLANDS ANTILLES', 'AN', 'en');
INSERT INTO "public"."countries" VALUES ('ANGOLA', 'AO', 'en');
INSERT INTO "public"."countries" VALUES ('ANTARCTICA', 'AQ', 'en');
INSERT INTO "public"."countries" VALUES ('ARGENTINA', 'AR', 'en');
INSERT INTO "public"."countries" VALUES ('AMERICAN SAMOA', 'AS', 'en');
INSERT INTO "public"."countries" VALUES ('AUSTRIA', 'AT', 'en');
INSERT INTO "public"."countries" VALUES ('AUSTRALIA', 'AU', 'en');
INSERT INTO "public"."countries" VALUES ('ARUBA', 'AW', 'en');
INSERT INTO "public"."countries" VALUES ('ALAND ISLANDS', 'AX', 'en');
INSERT INTO "public"."countries" VALUES ('AZERBAIJAN', 'AZ', 'en');
INSERT INTO "public"."countries" VALUES ('BOSNIA AND HERZEGOVINA', 'BA', 'en');
INSERT INTO "public"."countries" VALUES ('BARBADOS', 'BB', 'en');
INSERT INTO "public"."countries" VALUES ('BANGLADESH', 'BD', 'en');
INSERT INTO "public"."countries" VALUES ('BELGIUM', 'BE', 'en');
INSERT INTO "public"."countries" VALUES ('BURKINA FASO', 'BF', 'en');
INSERT INTO "public"."countries" VALUES ('BULGARIA', 'BG', 'en');
INSERT INTO "public"."countries" VALUES ('BAHRAIN', 'BH', 'en');
INSERT INTO "public"."countries" VALUES ('BURUNDI', 'BI', 'en');
INSERT INTO "public"."countries" VALUES ('BENIN', 'BJ', 'en');
INSERT INTO "public"."countries" VALUES ('BERMUDA', 'BM', 'en');
INSERT INTO "public"."countries" VALUES ('BRUNEI DARUSSALAM', 'BN', 'en');
INSERT INTO "public"."countries" VALUES ('BOLIVIA', 'BO', 'en');
INSERT INTO "public"."countries" VALUES ('BRAZIL', 'BR', 'en');
INSERT INTO "public"."countries" VALUES ('BAHAMAS', 'BS', 'en');
INSERT INTO "public"."countries" VALUES ('BHUTAN', 'BT', 'en');
INSERT INTO "public"."countries" VALUES ('BOUVET ISLAND', 'BV', 'en');
INSERT INTO "public"."countries" VALUES ('BOTSWANA', 'BW', 'en');
INSERT INTO "public"."countries" VALUES ('BELARUS', 'BY', 'en');
INSERT INTO "public"."countries" VALUES ('BELIZE', 'BZ', 'en');
INSERT INTO "public"."countries" VALUES ('CANADA', 'CA', 'en');
INSERT INTO "public"."countries" VALUES ('COCOS (KEELING) ISLANDS', 'CC', 'en');
INSERT INTO "public"."countries" VALUES ('CONGO DEMOCRATIC REPUBLIC', 'CD', 'en');
INSERT INTO "public"."countries" VALUES ('CENTRAL AFRICAN REPUBLIC', 'CF', 'en');
INSERT INTO "public"."countries" VALUES ('CONGO', 'CG', 'en');
INSERT INTO "public"."countries" VALUES ('SWITZERLAND', 'CH', 'en');
INSERT INTO "public"."countries" VALUES ('COTE D''IVOIRE', 'CI', 'en');
INSERT INTO "public"."countries" VALUES ('COOK ISLANDS', 'CK', 'en');
INSERT INTO "public"."countries" VALUES ('CHILE', 'CL', 'en');
INSERT INTO "public"."countries" VALUES ('CAMEROON', 'CM', 'en');
INSERT INTO "public"."countries" VALUES ('CHINA', 'CN', 'en');
INSERT INTO "public"."countries" VALUES ('COLOMBIA', 'CO', 'en');
INSERT INTO "public"."countries" VALUES ('COSTA RICA', 'CR', 'en');
INSERT INTO "public"."countries" VALUES ('SERBIA AND MONTENEGRO', 'CS', 'en');
INSERT INTO "public"."countries" VALUES ('CUBA', 'CU', 'en');
INSERT INTO "public"."countries" VALUES ('CAPE VERDE', 'CV', 'en');
INSERT INTO "public"."countries" VALUES ('CHRISTMAS ISLAND', 'CX', 'en');
INSERT INTO "public"."countries" VALUES ('CYPRUS', 'CY', 'en');
INSERT INTO "public"."countries" VALUES ('CZECH REPUBLIC', 'CZ', 'en');
INSERT INTO "public"."countries" VALUES ('GERMANY', 'DE', 'en');
INSERT INTO "public"."countries" VALUES ('DJIBOUTI', 'DJ', 'en');
INSERT INTO "public"."countries" VALUES ('DENMARK', 'DK', 'en');
INSERT INTO "public"."countries" VALUES ('DOMINICA', 'DM', 'en');
INSERT INTO "public"."countries" VALUES ('DOMINICAN REPUBLIC', 'DO', 'en');
INSERT INTO "public"."countries" VALUES ('ALGERIA', 'DZ', 'en');
INSERT INTO "public"."countries" VALUES ('ECUADOR', 'EC', 'en');
INSERT INTO "public"."countries" VALUES ('ESTONIA', 'EE', 'en');
INSERT INTO "public"."countries" VALUES ('EGYPT', 'EG', 'en');
INSERT INTO "public"."countries" VALUES ('WESTERN SAHARA', 'EH', 'en');
INSERT INTO "public"."countries" VALUES ('ERITREA', 'ER', 'en');
INSERT INTO "public"."countries" VALUES ('SPAIN', 'ES', 'en');
INSERT INTO "public"."countries" VALUES ('ETHIOPIA', 'ET', 'en');
INSERT INTO "public"."countries" VALUES ('FINLAND', 'FI', 'en');
INSERT INTO "public"."countries" VALUES ('FIJI', 'FJ', 'en');
INSERT INTO "public"."countries" VALUES ('FALKLAND ISLANDS (MALVINAS)', 'FK', 'en');
INSERT INTO "public"."countries" VALUES ('MICRONESIA', 'FM', 'en');
INSERT INTO "public"."countries" VALUES ('FAROE ISLANDS', 'FO', 'en');
INSERT INTO "public"."countries" VALUES ('FRANCE', 'FR', 'en');
INSERT INTO "public"."countries" VALUES ('GABON', 'GA', 'en');
INSERT INTO "public"."countries" VALUES ('UNITED KINGDOM', 'GB', 'en');
INSERT INTO "public"."countries" VALUES ('GRENADA', 'GD', 'en');
INSERT INTO "public"."countries" VALUES ('GEORGIA', 'GE', 'en');
INSERT INTO "public"."countries" VALUES ('FRENCH GUIANA', 'GF', 'en');
INSERT INTO "public"."countries" VALUES ('GHANA', 'GH', 'en');
INSERT INTO "public"."countries" VALUES ('GIBRALTAR', 'GI', 'en');
INSERT INTO "public"."countries" VALUES ('GREENLAND', 'GL', 'en');
INSERT INTO "public"."countries" VALUES ('GAMBIA', 'GM', 'en');
INSERT INTO "public"."countries" VALUES ('GUINEA', 'GN', 'en');
INSERT INTO "public"."countries" VALUES ('GUADELOUPE', 'GP', 'en');
INSERT INTO "public"."countries" VALUES ('EQUATORIAL GUINEA', 'GQ', 'en');
INSERT INTO "public"."countries" VALUES ('GREECE', 'GR', 'en');
INSERT INTO "public"."countries" VALUES ('SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'GS', 'en');
INSERT INTO "public"."countries" VALUES ('GUATEMALA', 'GT', 'en');
INSERT INTO "public"."countries" VALUES ('GUAM', 'GU', 'en');
INSERT INTO "public"."countries" VALUES ('GUINEA-BISSAU', 'GW', 'en');
INSERT INTO "public"."countries" VALUES ('GUYANA', 'GY', 'en');
INSERT INTO "public"."countries" VALUES ('HONG KONG', 'HK', 'en');
INSERT INTO "public"."countries" VALUES ('HEARD ISLAND AND MCDONALD ISLANDS', 'HM', 'en');
INSERT INTO "public"."countries" VALUES ('HONDURAS', 'HN', 'en');
INSERT INTO "public"."countries" VALUES ('CROATIA', 'HR', 'en');
INSERT INTO "public"."countries" VALUES ('HAITI', 'HT', 'en');
INSERT INTO "public"."countries" VALUES ('HUNGARY', 'HU', 'en');
INSERT INTO "public"."countries" VALUES ('INDONESIA', 'ID', 'en');
INSERT INTO "public"."countries" VALUES ('IRELAND', 'IE', 'en');
INSERT INTO "public"."countries" VALUES ('ISRAEL', 'IL', 'en');
INSERT INTO "public"."countries" VALUES ('INDIA', 'IN', 'en');
INSERT INTO "public"."countries" VALUES ('BRITISH INDIAN OCEAN TERRITORY', 'IO', 'en');
INSERT INTO "public"."countries" VALUES ('IRAQ', 'IQ', 'en');
INSERT INTO "public"."countries" VALUES ('IRAN', 'IR', 'en');
INSERT INTO "public"."countries" VALUES ('ICELAND', 'IS', 'en');
INSERT INTO "public"."countries" VALUES ('ITALY', 'IT', 'en');
INSERT INTO "public"."countries" VALUES ('JAMAICA', 'JM', 'en');
INSERT INTO "public"."countries" VALUES ('JORDAN', 'JO', 'en');
INSERT INTO "public"."countries" VALUES ('JAPAN', 'JP', 'en');
INSERT INTO "public"."countries" VALUES ('KENYA', 'KE', 'en');
INSERT INTO "public"."countries" VALUES ('KYRGYZSTAN', 'KG', 'en');
INSERT INTO "public"."countries" VALUES ('CAMBODIA', 'KH', 'en');
INSERT INTO "public"."countries" VALUES ('KIRIBATI', 'KI', 'en');
INSERT INTO "public"."countries" VALUES ('COMOROS', 'KM', 'en');
INSERT INTO "public"."countries" VALUES ('SAINT KITTS AND NEVIS', 'KN', 'en');
INSERT INTO "public"."countries" VALUES ('KOREA NORTH', 'KP', 'en');
INSERT INTO "public"."countries" VALUES ('KOREA SOUTH', 'KR', 'en');
INSERT INTO "public"."countries" VALUES ('KUWAIT', 'KW', 'en');
INSERT INTO "public"."countries" VALUES ('CAYMAN ISLANDS', 'KY', 'en');
INSERT INTO "public"."countries" VALUES ('KAZAKHSTAN', 'KZ', 'en');
INSERT INTO "public"."countries" VALUES ('LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'LA', 'en');
INSERT INTO "public"."countries" VALUES ('LEBANON', 'LB', 'en');
INSERT INTO "public"."countries" VALUES ('SAINT LUCIA', 'LC', 'en');
INSERT INTO "public"."countries" VALUES ('LIECHTENSTEIN', 'LI', 'en');
INSERT INTO "public"."countries" VALUES ('SRI LANKA', 'LK', 'en');
INSERT INTO "public"."countries" VALUES ('LIBERIA', 'LR', 'en');
INSERT INTO "public"."countries" VALUES ('LESOTHO', 'LS', 'en');
INSERT INTO "public"."countries" VALUES ('LITHUANIA', 'LT', 'en');
INSERT INTO "public"."countries" VALUES ('LUXEMBOURG', 'LU', 'en');
INSERT INTO "public"."countries" VALUES ('LATVIA', 'LV', 'en');
INSERT INTO "public"."countries" VALUES ('LIBYAN ARAB JAMAHIRIYA', 'LY', 'en');
INSERT INTO "public"."countries" VALUES ('MOROCCO', 'MA', 'en');
INSERT INTO "public"."countries" VALUES ('MONACO', 'MC', 'en');
INSERT INTO "public"."countries" VALUES ('MOLDOVA', 'MD', 'en');
INSERT INTO "public"."countries" VALUES ('MADAGASCAR', 'MG', 'en');
INSERT INTO "public"."countries" VALUES ('MARSHALL ISLANDS', 'MH', 'en');
INSERT INTO "public"."countries" VALUES ('MACEDONIA', 'MK', 'en');
INSERT INTO "public"."countries" VALUES ('MALI', 'ML', 'en');
INSERT INTO "public"."countries" VALUES ('MYANMAR', 'MM', 'en');
INSERT INTO "public"."countries" VALUES ('MONGOLIA', 'MN', 'en');
INSERT INTO "public"."countries" VALUES ('MACAO', 'MO', 'en');
INSERT INTO "public"."countries" VALUES ('NORTHERN MARIANA ISLANDS', 'MP', 'en');
INSERT INTO "public"."countries" VALUES ('MARTINIQUE', 'MQ', 'en');
INSERT INTO "public"."countries" VALUES ('MAURITANIA', 'MR', 'en');
INSERT INTO "public"."countries" VALUES ('MONTSERRAT', 'MS', 'en');
INSERT INTO "public"."countries" VALUES ('MALTA', 'MT', 'en');
INSERT INTO "public"."countries" VALUES ('MAURITIUS', 'MU', 'en');
INSERT INTO "public"."countries" VALUES ('MALDIVES', 'MV', 'en');
INSERT INTO "public"."countries" VALUES ('MALAWI', 'MW', 'en');
INSERT INTO "public"."countries" VALUES ('MEXICO', 'MX', 'en');
INSERT INTO "public"."countries" VALUES ('MALAYSIA', 'MY', 'en');
INSERT INTO "public"."countries" VALUES ('MOZAMBIQUE', 'MZ', 'en');
INSERT INTO "public"."countries" VALUES ('NAMIBIA', 'NA', 'en');
INSERT INTO "public"."countries" VALUES ('NEW CALEDONIA', 'NC', 'en');
INSERT INTO "public"."countries" VALUES ('NIGER', 'NE', 'en');
INSERT INTO "public"."countries" VALUES ('NORFOLK ISLAND', 'NF', 'en');
INSERT INTO "public"."countries" VALUES ('NIGERIA', 'NG', 'en');
INSERT INTO "public"."countries" VALUES ('NICARAGUA', 'NI', 'en');
INSERT INTO "public"."countries" VALUES ('NETHERLANDS', 'NL', 'en');
INSERT INTO "public"."countries" VALUES ('NORWAY', 'NO', 'en');
INSERT INTO "public"."countries" VALUES ('NEPAL', 'NP', 'en');
INSERT INTO "public"."countries" VALUES ('NAURU', 'NR', 'en');
INSERT INTO "public"."countries" VALUES ('NIUE', 'NU', 'en');
INSERT INTO "public"."countries" VALUES ('NEW ZEALAND', 'NZ', 'en');
INSERT INTO "public"."countries" VALUES ('OMAN', 'OM', 'en');
INSERT INTO "public"."countries" VALUES ('PANAMA', 'PA', 'en');
INSERT INTO "public"."countries" VALUES ('PERU', 'PE', 'en');
INSERT INTO "public"."countries" VALUES ('FRENCH POLYNESIA', 'PF', 'en');
INSERT INTO "public"."countries" VALUES ('PAPUA NEW GUINEA', 'PG', 'en');
INSERT INTO "public"."countries" VALUES ('PHILIPPINES', 'PH', 'en');
INSERT INTO "public"."countries" VALUES ('PAKISTAN', 'PK', 'en');
INSERT INTO "public"."countries" VALUES ('POLAND', 'PL', 'en');
INSERT INTO "public"."countries" VALUES ('SAINT PIERRE AND MIQUELON', 'PM', 'en');
INSERT INTO "public"."countries" VALUES ('PITCAIRN', 'PN', 'en');
INSERT INTO "public"."countries" VALUES ('PUERTO RICO', 'PR', 'en');
INSERT INTO "public"."countries" VALUES ('PALESTINIAN TERRITORY', 'PS', 'en');
INSERT INTO "public"."countries" VALUES ('PORTUGAL', 'PT', 'en');
INSERT INTO "public"."countries" VALUES ('PALAU', 'PW', 'en');
INSERT INTO "public"."countries" VALUES ('PARAGUAY', 'PY', 'en');
INSERT INTO "public"."countries" VALUES ('QATAR', 'QA', 'en');
INSERT INTO "public"."countries" VALUES ('REUNION', 'RE', 'en');
INSERT INTO "public"."countries" VALUES ('ROMANIA', 'RO', 'en');
INSERT INTO "public"."countries" VALUES ('RUSSIAN FEDERATION', 'RU', 'en');
INSERT INTO "public"."countries" VALUES ('RWANDA', 'RW', 'en');
INSERT INTO "public"."countries" VALUES ('SAUDI ARABIA', 'SA', 'en');
INSERT INTO "public"."countries" VALUES ('SOLOMON ISLANDS', 'SB', 'en');
INSERT INTO "public"."countries" VALUES ('SEYCHELLES', 'SC', 'en');
INSERT INTO "public"."countries" VALUES ('SUDAN', 'SD', 'en');
INSERT INTO "public"."countries" VALUES ('SWEDEN', 'SE', 'en');
INSERT INTO "public"."countries" VALUES ('SINGAPORE', 'SG', 'en');
INSERT INTO "public"."countries" VALUES ('SAINT HELENA', 'SH', 'en');
INSERT INTO "public"."countries" VALUES ('SLOVENIA', 'SI', 'en');
INSERT INTO "public"."countries" VALUES ('SVALBARD AND JAN MAYEN', 'SJ', 'en');
INSERT INTO "public"."countries" VALUES ('SLOVAKIA', 'SK', 'en');
INSERT INTO "public"."countries" VALUES ('SIERRA LEONE', 'SL', 'en');
INSERT INTO "public"."countries" VALUES ('SAN MARINO', 'SM', 'en');
INSERT INTO "public"."countries" VALUES ('SENEGAL', 'SN', 'en');
INSERT INTO "public"."countries" VALUES ('SOMALIA', 'SO', 'en');
INSERT INTO "public"."countries" VALUES ('SURINAME', 'SR', 'en');
INSERT INTO "public"."countries" VALUES ('SAO TOME AND PRINCIPE', 'ST', 'en');
INSERT INTO "public"."countries" VALUES ('EL SALVADOR', 'SV', 'en');
INSERT INTO "public"."countries" VALUES ('SYRIAN ARAB REPUBLIC', 'SY', 'en');
INSERT INTO "public"."countries" VALUES ('SWAZILAND', 'SZ', 'en');
INSERT INTO "public"."countries" VALUES ('TURKS AND CAICOS ISLANDS', 'TC', 'en');
INSERT INTO "public"."countries" VALUES ('CHAD', 'TD', 'en');
INSERT INTO "public"."countries" VALUES ('FRENCH SOUTHERN TERRITORIES', 'TF', 'en');
INSERT INTO "public"."countries" VALUES ('TOGO', 'TG', 'en');
INSERT INTO "public"."countries" VALUES ('THAILAND', 'TH', 'en');
INSERT INTO "public"."countries" VALUES ('TAJIKISTAN', 'TJ', 'en');
INSERT INTO "public"."countries" VALUES ('TOKELAU', 'TK', 'en');
INSERT INTO "public"."countries" VALUES ('TIMOR-LESTE', 'TL', 'en');
INSERT INTO "public"."countries" VALUES ('TURKMENISTAN', 'TM', 'en');
INSERT INTO "public"."countries" VALUES ('TUNISIA', 'TN', 'en');
INSERT INTO "public"."countries" VALUES ('TONGA', 'TO', 'en');
INSERT INTO "public"."countries" VALUES ('TURKEY', 'TR', 'en');
INSERT INTO "public"."countries" VALUES ('TRINIDAD AND TOBAGO', 'TT', 'en');
INSERT INTO "public"."countries" VALUES ('TUVALU', 'TV', 'en');
INSERT INTO "public"."countries" VALUES ('TAIWAN', 'TW', 'en');
INSERT INTO "public"."countries" VALUES ('TANZANIA', 'TZ', 'en');
INSERT INTO "public"."countries" VALUES ('UKRAINE', 'UA', 'en');
INSERT INTO "public"."countries" VALUES ('UGANDA', 'UG', 'en');
INSERT INTO "public"."countries" VALUES ('UNITED STATES MINOR OUTLYING ISLANDS', 'UM', 'en');
INSERT INTO "public"."countries" VALUES ('UNITED STATES', 'US', 'en');
INSERT INTO "public"."countries" VALUES ('URUGUAY', 'UY', 'en');
INSERT INTO "public"."countries" VALUES ('UZBEKISTAN', 'UZ', 'en');
INSERT INTO "public"."countries" VALUES ('HOLY SEE (VATICAN CITY STATE)', 'VA', 'en');
INSERT INTO "public"."countries" VALUES ('SAINT VINCENT AND THE GRENADINES', 'VC', 'en');
INSERT INTO "public"."countries" VALUES ('VENEZUELA', 'VE', 'en');
INSERT INTO "public"."countries" VALUES ('VIRGIN ISLANDS (BRITISH)', 'VG', 'en');
INSERT INTO "public"."countries" VALUES ('VIRGIN ISLANDS (U.S.)', 'VI', 'en');
INSERT INTO "public"."countries" VALUES ('VIET NAM', 'VN', 'en');
INSERT INTO "public"."countries" VALUES ('VANUATU', 'VU', 'en');
INSERT INTO "public"."countries" VALUES ('WALLIS AND FUTUNA', 'WF', 'en');
INSERT INTO "public"."countries" VALUES ('SAMOA', 'WS', 'en');
INSERT INTO "public"."countries" VALUES ('YEMEN', 'YE', 'en');
INSERT INTO "public"."countries" VALUES ('MAYOTTE', 'YT', 'en');
INSERT INTO "public"."countries" VALUES ('SOUTH AFRICA', 'ZA', 'en');
INSERT INTO "public"."countries" VALUES ('ZAMBIA', 'ZM', 'en');
INSERT INTO "public"."countries" VALUES ('ZIMBABWE', 'ZW', 'en');
