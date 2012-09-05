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

?>
