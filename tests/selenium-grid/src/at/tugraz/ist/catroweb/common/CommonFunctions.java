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

import java.io.File;
import java.math.BigDecimal;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.HashMap;

import org.json.JSONException;
import org.json.JSONObject;
import org.postgresql.Driver;
import org.testng.Reporter;

public class CommonFunctions {
  public static String getAdminPath(String webSite) {
    return "http://" + Config.ADMIN_AREA_USER + ":" + Config.DB_PASS + "@" + webSite.replace("http://", "") + Config.TESTS_BASE_PATH.substring(1) + "admin";
  }

  public static void deleteDir(File directory) {
    if(directory.isDirectory()) {
      String[] children = directory.list();

      for(String child : children) {
        File item = new File(directory, child);

        if(item.isFile()) {
          item.delete();
        }
        if(item.isDirectory()) {
          deleteDir(item);
        }
      }
      directory.delete();
    }
  }

  public static String getValueFromJSONobject(String json, String key) {
    if(json.indexOf("{") != 0) {
      Reporter.log("********************************************************");
      Reporter.log("CommonFunctions: getValueFromJSONobject: Invalid json object!");
      Reporter.log(json);
      Reporter.log("********************************************************");

      String[] temp = json.split("[{]", 2);
      try {
        json = "{" + temp[1];
      } catch (ArrayIndexOutOfBoundsException e) {
        return "received no json object";
      }
    }

    try {
      JSONObject array = new JSONObject(json);
      
      try {
        String status = array.getString("statusCode");
        if(!status.equals("200")) {
          Reporter.log("********************************************************");
          Reporter.log("CommonFunctions: getValueFromJSONobject: Statuscode is not 200");
          Reporter.log(json);
          Reporter.log("********************************************************");
        } 
      }catch(Exception ignore) {
      }
      
      return array.getString(key);
    } catch(JSONException e) {
      e.printStackTrace();
    } catch(ArrayIndexOutOfBoundsException e) {
      Reporter.log("********************************************************");
      Reporter.log("CommonFunctions: getValueFromJSONobject: Invalid json object!");
      Reporter.log(json);
      Reporter.log("********************************************************");
    }
    return "received invalid json object";
  }

  public static double getFileSizeRounded(String filepath) {
    double filesize = 0.0;
    try {
      File file = new File(filepath);
      if(file.exists()) {
        BigDecimal bd = new BigDecimal(((double) file.length() / (1024 * 1024)));
        bd = bd.setScale(1, BigDecimal.ROUND_UP);
        filesize = bd.doubleValue();
      }
    } catch(Exception e) {
      Reporter.log("Error: CommonFunctions.getFileSizeRounded(" + filepath + ")" + e.getMessage());
    }
    return filesize;
  }

  public static HashMap<String, String> getVersionInfo(String id) {
    HashMap<String, String> data = new HashMap<String, String>();
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      ResultSet result = statement.executeQuery("SELECT version_name, language_code FROM projects WHERE id='" + id + "';");
      if(result.next()) {
        data.put("version_name", result.getString("version_name"));
        data.put("version_code", result.getString("language_code"));
      }
      result.close();
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonData: getRandomProject: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
    return data;
  }

  public static int getProjectsCount(boolean visible_projects_only) {
    int count = 0;
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      String query = "SELECT COUNT(*) FROM projects ";
      if(visible_projects_only)
        query += "WHERE visible='true';";
      else
        query += ";";

      ResultSet result = statement.executeQuery(query);
      if(result.next()) {
        count = result.getInt(1);
      }
      result.close();
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonData: getRandomProject: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
    return count;
  }

  public static void deleteUserFromDatabase(String username) {
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("DELETE FROM cusers WHERE username='" + username + "';");
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonFunctions: removeUserFromBD: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
  }

  public static String getTimeStamp() {
    java.util.Date time = new java.util.Date();
    return(new java.sql.Time(time.getTime()).toString());
  }

  public static String getUnapprovedWordId(String word) {
    String id = "";
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      ResultSet result = statement.executeQuery("SELECT id FROM wordlist WHERE word='" + word + "' LIMIT 1");
      if(result.next()) {
        id = result.getString(1);
      }
      result.close();
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonFunctions: getUnapprovedWordId: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
    return id;
  }

  public static String getAuthenticationToken(String username) {
    String token = "";

    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      ResultSet result = statement.executeQuery("SELECT auth_token FROM cusers WHERE username='" + username + "' LIMIT 1");
      if(result.next()) {
        token = result.getString(1);
      }
      result.close();
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonFunctions: generateAuthenticationToken: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
    return token;
  }

  public static void removeAllBlockedIps() {
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("DELETE FROM blocked_ips;");
      statement.executeUpdate("DELETE FROM blocked_ips_temporary;");
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonFunctions: removeAllBlockedIps: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
  }

  public static void removeAllBlockedUsers() {
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("DELETE FROM blocked_cusers;");
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      Reporter.log("CommonFunctions: removeAllBlockedUsers: SQL Exception couldn't execute sql query!");
      Reporter.log(e.getMessage());
    }
  }
}
