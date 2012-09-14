<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('STATUS_CODE_OK', 200);
define('STATUS_CODE_REGISTRATION_OK', 201);

define('STATUS_CODE_SQL_QUERY_FAILED', 401);
define('STATUS_CODE_SQL_CONNECTION_FAILED', 402);

define('STATUS_CODE_INTERNAL_SERVER_ERROR', 500);
define('STATUS_CODE_UPLOAD_MISSING_DATA', 501);
define('STATUS_CODE_UPLOAD_EXCEEDING_FILESIZE', 502);
define('STATUS_CODE_UPLOAD_MISSING_CHECKSUM', 503);
define('STATUS_CODE_UPLOAD_INVALID_CHECKSUM', 504);
define('STATUS_CODE_UPLOAD_COPY_FAILED', 505);
define('STATUS_CODE_UPLOAD_UNZIP_FAILED', 506);
define('STATUS_CODE_UPLOAD_MISSING_XML', 507);
define('STATUS_CODE_UPLOAD_INVALID_XML', 508);
define('STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE', 509);
define('STATUS_CODE_UPLOAD_DEFAULT_PROJECT_TITLE', 510);
define('STATUS_CODE_UPLOAD_RUDE_PROJECT_TITLE', 511);
define('STATUS_CODE_UPLOAD_RUDE_PROJECT_DESCRIPTION', 512);
define('STATUS_CODE_UPLOAD_RENAME_FAILED', 513);
define('STATUS_CODE_UPLOAD_SAVE_THUMBNAIL_FAILED', 514);
define('STATUS_CODE_UPLOAD_QRCODE_GENERATION_FAILED', 515);

define('STATUS_CODE_AUTHENTICATION_FAILED', 601);
define('STATUS_CODE_AUTHENTICATION_REGISTRATION_FAILED', 602);
define('STATUS_CODE_LOGIN_MISSING_DATA', 651);
define('STATUS_CODE_LOGIN_MISSING_USERNAME', 652);
define('STATUS_CODE_LOGIN_MISSING_PASSWORD', 653);

define('STATUS_CODE_PROFILE_OLD_PASSWORD_MISSING', 701);
define('STATUS_CODE_PROFILE_OLD_PASSWORD_WRONG', 702);
define('STATUS_CODE_PROFILE_NEW_PASSWORD_MISSING', 703);
define('STATUS_CODE_PROFILE_USERNAME_PASSWORD_EQUAL', 704);
define('STATUS_CODE_PROFILE_NEW_PASSWORD_TOO_SHORT', 705);
define('STATUS_CODE_PROFILE_NEW_PASSWORD_TOO_LONG', 706);
define('STATUS_CODE_PROFILE_UPDATE_CATROWEB_EMAIL_FAILED', 708);


define('STATUS_CODE_USER_PASSWORD_MISSING', 751);
define('STATUS_CODE_USER_USERNAME_PASSWORD_EQUAL', 752);
define('STATUS_CODE_USER_PASSWORD_TOO_SHORT', 753);
define('STATUS_CODE_USER_PASSWORD_TOO_LONG', 754);
define('STATUS_CODE_USER_NEW_PASSWORD_BOARD_UPDATE_FAILED', 755);
define('STATUS_CODE_USER_DELETE_EMAIL_FAILED', 756);
define('STATUS_CODE_USER_ADD_EMAIL_EXISTS', 757);
define('STATUS_CODE_USER_UPDATE_CITY_FAILED', 758);
define('STATUS_CODE_USER_UPDATE_COUNTRY_FAILED', 759);
define('STATUS_CODE_USER_UPDATE_GENDER_FAILED', 760);
define('STATUS_CODE_USER_UPDATE_BIRTHDAY_FAILED', 761);


?>
