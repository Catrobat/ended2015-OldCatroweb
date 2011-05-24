/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
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

package at.tugraz.ist.catroweb.common;

public class Config {
  public static final boolean REPORTER_LOG_TO_STD_OUT = true;
  public static final boolean TESTS_SLOW_MODE = false;
  public static final int TESTS_SLOW_SPEED = 1000;
  public static final String WAIT_FOR_PAGE_TO_LOAD = "10000";
  public static final String TIMEOUT = "120000";
  public static final String TIMEOUT_AJAX = "5000";
  public static final long TIMEOUT_THREAD = 2000;

  public static final String TESTS_BASE_PATH = "/";
  // public static final String TESTS_BASE_PATH = "/catroweb/";

  public static final String DB_USER = "website";
  public static final String DB_PASS = "cat.roid.web";
  public static final String DB_HOST = "jdbc:postgresql://localhost/";
  public static final String DB_NAME = "catroweb";
  public static final String ADMIN_AREA_USER = "admin";

  public static final String FILESYSTEM_SEPARATOR = System.getProperty("file.separator");
  public static final String FILESYSTEM_BASE_PATH = System.getProperty("user.dir") + FILESYSTEM_SEPARATOR;

  public static final String SELENIUM_GRID_TESTDATA = "tests" + FILESYSTEM_SEPARATOR + "selenium-grid" + FILESYSTEM_SEPARATOR + "testdata"
      + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "projects" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_UNZIPPED_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "catroid" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_QR_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "qrcodes" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_QR_EXTENTION = "_qr.png";
  public static final String PROJECTS_THUMBNAIL_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "thumbnails" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_THUMBNAIL_EXTENTION_ORIG = "_original.jpg";
  public static final String PROJECTS_THUMBNAIL_EXTENTION_SMALL = "_small.jpg";
  public static final String PROJECTS_THUMBNAIL_EXTENTION_LARGE = "_large.jpg";

  public static final int PROJECT_PAGE_LOAD_MAX_PROJECTS = 5;
  public static final int PROJECT_PAGE_SHOW_MAX_PAGES = 5;
  public static final String PROJECTS_EXTENTION = ".zip";

  public static final String DEFAULT_UPLOAD_TITLE = "Testproject";
  public static final String DEFAULT_UPLOAD_DESCRIPTION = "This is my testproject...";
  public static final String DEFAULT_UPLOAD_FILE = FILESYSTEM_BASE_PATH + SELENIUM_GRID_TESTDATA + "test.zip";
  public static final String DEFAULT_UPLOAD_CHECKSUM = "72ed87fbd5119885009522f08b7ee79f";
  public static final String DEFAULT_UPLOAD_IMEI = "b1946ac92492d2347c6235b4d2611184";
  public static final String DEFAULT_UPLOAD_EMAIL = "webmaster@catroid.org";
  public static final String DEFAULT_UPLOAD_LANGUAGE = "en";
  public static final String DEFAULT_UPLOAD_TOKEN = "31df676f845b4ce9908f7a716a7bfa50";
}
