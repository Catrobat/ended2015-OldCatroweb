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

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.HashMap;

import org.postgresql.Driver;

public class CommonData {
  public static String getRandomShortString(int strLen) {
    String str = "";
    String chars = "abcdefghijklmnopqrstuvwxyz";
    for(int i = 0; i < strLen; i++) {
      java.util.Random rand = new java.util.Random();
      str += chars.charAt(rand.nextInt(chars.length()));
    }
    return str;
  }

  public static String getRandomLongString(int strLen) {
    String str = "";
    String chars = "abcdefghijklmnopqrstuvwxyz1234567890";
    for(int i = 0; i < strLen; i++) {
      java.util.Random rand = new java.util.Random();
      str += chars.charAt(rand.nextInt(chars.length()));
    }
    return str;
  }
  
  public static String getRandomChineseString(int strLen) {
	String str = "";
	String chars = "诶比西迪伊艾弗吉艾尺艾杰开艾勒艾马艾娜哦屁吉吾艾儿艾丝提伊吾维豆贝尔维艾克斯吾艾贼德";
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
       String userEmail, String userLanguage, String username, String token) {
    HashMap<String, String> data = new HashMap<String, String>();
    if(!projectTitle.isEmpty()) {
      data.put("projectTitle", projectTitle);
    }
    if(!projectDescription.isEmpty()) {
      data.put("projectDescription", projectDescription);
    }
    if(!filename.isEmpty()) {
      data.put("upload", Config.FILESYSTEM_BASE_PATH + Config.SELENIUM_GRID_TESTDATA + filename);
    }
    if(!fileChecksum.isEmpty()) {
      data.put("fileChecksum", fileChecksum);
    }
    if(!userEmail.isEmpty()) {
      data.put("userEmail", userEmail);
    }
    if(!userLanguage.isEmpty()) {
      data.put("userLanguage", userLanguage);
    }
    if(!username.isEmpty()) {
      data.put("username", username);
    }
    if(!token.isEmpty()) {
      data.put("token", token);
    }
    return data;
  }
  
  public static HashMap<String, String> getUploadPayload(String projectTitle, String projectDescription, String filename, String fileChecksum,
      String userEmail, String userLanguage, String username, String token, String expected) {
    HashMap<String, String> data = new HashMap<String, String>();
    data = getUploadPayload(projectTitle, projectDescription, filename, fileChecksum, userEmail, userLanguage, username, token);
    if(!expected.isEmpty()) {
      data.put("expected", expected);
    }
    return data;
  }

  public static HashMap<String, String> getUploadFtpPayload(String projectTitle, String projectDescription, String catroidFileName, String fileChecksum,
      String userEmail, String userLanguage, String username, String token) {
   HashMap<String, String> data = new HashMap<String, String>();
   if(!projectTitle.isEmpty()) {
     data.put("projectTitle", projectTitle);
   }
   if(!projectDescription.isEmpty()) {
     data.put("projectDescription", projectDescription);
   }
   if(!catroidFileName.isEmpty()) {
     data.put("catroidFileName", catroidFileName);
   }
   if(!fileChecksum.isEmpty()) {
     data.put("fileChecksum", fileChecksum);
   }
   if(!userEmail.isEmpty()) {
     data.put("userEmail", userEmail);
   }
   if(!userLanguage.isEmpty()) {
     data.put("userLanguage", userLanguage);
   }
   if(!username.isEmpty()) {
     data.put("username", username);
   }
   if(!token.isEmpty()) {
     data.put("token", token);
   }
   return data;
 }

  public static HashMap<String, String> getRandomProject() {
    HashMap<String, String> data = new HashMap<String, String>();
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      ResultSet result = statement.executeQuery("SELECT * FROM projects WHERE visible=true ORDER BY random() LIMIT 1");
      if(result.next()) {
        data.put("id", result.getString("id"));
        data.put("title", result.getString("title"));
        data.put("description", result.getString("description"));
      }
      result.close();
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      System.out.println("CommonData: getRandomProject: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
    return data;
  }
}
