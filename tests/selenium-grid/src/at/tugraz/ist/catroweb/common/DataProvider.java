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

import java.util.HashMap;

public class DataProvider {
  public static String getRandomLongString() {
    String str = "";
    int strLen = 200;
    String chars = "abcdefghijklmnopqrstuvwxyz1234567890";
    for(int i = 0; i < strLen; i++) {
      java.util.Random rand = new java.util.Random();
      str += chars.charAt(rand.nextInt(chars.length()));
    }
    return str;
  }

  public static String getLoginUserDefault() {
    return "catroweb";
  }

  public static String getLoginPasswordDefault() {
    return "cat.roid.web";
  }

  public static HashMap<String, String> getUploadPayload(String projectTitle, String projectDescription, String filename, String fileChecksum,
      String deviceIMEI, String userEmail, String userLanguage, String token) {
    HashMap<String, String> data = new HashMap<String, String>();
    if(!projectTitle.isEmpty()) {
      data.put("projectTitle", projectTitle);
    }
    if(!projectDescription.isEmpty()) {
      data.put("projectDescription", projectDescription);
    }
    if(!filename.isEmpty()) {
      data.put("upload", Config.FILESYSTEM_BASE_PATH + Config.SELENIUM_GRID_TESTDATA + Config.FILESYSTEM_SEPARATOR + filename);
    }
    if(!fileChecksum.isEmpty()) {
      data.put("fileChecksum", fileChecksum);
    }
    if(!deviceIMEI.isEmpty()) {
      data.put("deviceIMEI", deviceIMEI);
    }
    if(!userEmail.isEmpty()) {
      data.put("userEmail", userEmail);
    }
    if(!userLanguage.isEmpty()) {
      data.put("userLanguage", userLanguage);
    }
    if(!token.isEmpty()) {
      data.put("token", token);
    }
    return data;
  }
}
