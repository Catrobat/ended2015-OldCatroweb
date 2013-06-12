/**
  *Catroid: An on-device visual programming system for Android devices
  *Copyright (C) 2010-2013 The Catrobat Team
  *(<http://developer.catrobat.org/credits>)
  *
  *This program is free software: you can redistribute it and/or modify
  *it under the terms of the GNU Affero General Public License as
  *published by the Free Software Foundation, either version 3 of the
  *License, or (at your option) any later version.
  *
  *An additional term exception under section 7 of the GNU Affero
  *General Public License, version 3, is available at
  *http://developer.catrobat.org/license_additional_term
  *
  *This program is distributed in the hope that it will be useful,
  *but WITHOUT ANY WARRANTY; without even the implied warranty of
  *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  *GNU Affero General Public License for more details.
  *
  *You should have received a copy of the GNU Affero General Public License
  *along with this program. If not, see <http://www.gnu.org/licenses/>.
  */

package at.tugraz.ist.catroweb.common;

public class Config {
  public static final boolean REPORTER_LOG_TO_STD_OUT = true;
  public static final int TIMEOUT_WAIT = 60;

  public static final String TESTS_BASE_PATH = "/";
//  public static final String TESTS_BASE_PATH = "/catroweb/";

  public static final String DB_USER = "website";
  public static final String DB_PASS = "cat.roid.web";
  public static final String DB_HOST = "jdbc:postgresql://localhost/";
  public static final String DB_NAME = "catroweb";
  public static final String ADMIN_AREA_USER = "admin";

  public static final String FILESYSTEM_SEPARATOR = System.getProperty("file.separator");
  public static String FILESYSTEM_BASE_PATH = System.getProperty("user.dir") + FILESYSTEM_SEPARATOR;
  public static String FILESYSTEM_TEMP_FOLDER = System.getProperty("java.io.tmpdir") + FILESYSTEM_SEPARATOR;

  public static final String SELENIUM_GRID_TESTDATA = "tests" + FILESYSTEM_SEPARATOR + "selenium-grid" + FILESYSTEM_SEPARATOR + "testdata" + FILESYSTEM_SEPARATOR;
  public static final String SELENIUM_GRID_TARGET = "tests" + FILESYSTEM_SEPARATOR + "selenium-grid" + FILESYSTEM_SEPARATOR + "target" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "projects" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_UNZIPPED_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "catroid" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_QR_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "qrcodes" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_QR_EXTENTION = "_qr.png";
  public static final String PROJECTS_THUMBNAIL_DIRECTORY = "resources" + FILESYSTEM_SEPARATOR + "thumbnails" + FILESYSTEM_SEPARATOR;
  public static final String PROJECTS_THUMBNAIL_EXTENTION_ORIG = "_original.png";
  public static final String PROJECTS_THUMBNAIL_EXTENTION_SMALL = "_small.png";
  public static final String PROJECTS_THUMBNAIL_EXTENTION_LARGE = "_large.png";

  public static final int PROJECT_PAGE_LOAD_MAX_PROJECTS = 9;
  public static final int PROJECT_PAGE_SHOW_MAX_PAGES = 3;
  public static final String PROJECTS_EXTENTION = ".catrobat";
  public static final int PROJECT_SHORT_DESCRIPTION_MAX_LENGTH = 178;
  
  public static final String DEFAULT_UPLOAD_TITLE = "Testproject";
  public static final String DEFAULT_UPLOAD_DESCRIPTION = "This is my testproject...";
  public static String DEFAULT_UPLOAD_FILE = FILESYSTEM_BASE_PATH + SELENIUM_GRID_TESTDATA + "test-0.7.3beta.catrobat";
  public static final String DEFAULT_UPLOAD_CHECKSUM = "649ff13ee9c1750c3276f15e509f5489";
  public static final String DEFAULT_UPLOAD_EMAIL = "webmaster@catroid.org";
  public static final String DEFAULT_UPLOAD_LANGUAGE = "en";
  public static final String DEFAULT_UPLOAD_TOKEN = "31df676f845b4ce9908f7a716a7bfa50";
  
  public static final String SITE_DEFAULT_LANGUAGE = "en";

  public static void setSeleniumGridTestdata(String basedir) {
    if(FILESYSTEM_BASE_PATH.matches(".*tests" + FILESYSTEM_SEPARATOR + "selenium-grid" + FILESYSTEM_SEPARATOR + "$")) {
      FILESYSTEM_BASE_PATH = FILESYSTEM_BASE_PATH.substring(0, FILESYSTEM_BASE_PATH.indexOf("tests"+FILESYSTEM_SEPARATOR +  "selenium-grid"));
      DEFAULT_UPLOAD_FILE = FILESYSTEM_BASE_PATH + SELENIUM_GRID_TESTDATA + "test-0.7.3beta.catrobat";
    }
  }
}
