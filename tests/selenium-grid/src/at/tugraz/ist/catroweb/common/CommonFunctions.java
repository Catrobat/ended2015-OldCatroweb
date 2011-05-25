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

import java.io.File;
import java.math.BigDecimal;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import org.json.simple.JSONValue;

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
      System.out.println("********************************************************************");
      System.out.println("CommonFunctions: getValueFromJSONobject: Invalid json object!");
      System.out.println(json);
      System.out.println("********************************************************************");

      String[] temp = json.split("[{]", 2);
      json = "{" + temp[1];
    }

    Map<?, ?> array = (Map<?, ?>) JSONValue.parse(json);
    Iterator<?> iter = array.entrySet().iterator();
    while(iter.hasNext()) {
      Map.Entry<?, ?> entry = (Map.Entry<?, ?>) iter.next();
      if(entry.getKey().equals(key)) {
        return entry.getValue().toString();
      }
    }
    return "";
  }
  
  public static double getFileSizeRounded(String filepath) {
    double filesize = 0.0;
    try {
      File file = new File(filepath);    
      if (file.exists()) {
        BigDecimal bd = new BigDecimal(((double) file.length()/(1024*1024)));
        bd = bd.setScale(1,BigDecimal.ROUND_DOWN);
        filesize = bd.doubleValue();
      }
    }
    catch (Exception e) {
      System.out.println("Error: CommonFunctions.getFileSizeRounded("+filepath+")"+e.getMessage());
    }
    return filesize;
  }
  
  public static HashMap<String, String> getVersionInfo(String id) {
    HashMap<String, String> data = new HashMap<String, String>();      
    try {
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      ResultSet result = statement.executeQuery("SELECT version_name, version_code FROM projects WHERE id='" + id + "';");
      if (result.next()) { 
        data.put("version_name", result.getString("version_name"));
        data.put("version_code", result.getString("version_code"));
        result.close();        
        statement.close();
        connection.close();
      }      
    } catch(SQLException e) {
      System.out.println("CommonData: getRandomProject: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
    return data;   
  }
  
  public static int getProjectsCount(boolean visible_projects_only) {
    int count = 0;      
    try {
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      String query = "SELECT COUNT(*) FROM projects ";
      if (visible_projects_only)
        query += "WHERE visible='true';";
      else
        query += ";";
      
      ResultSet result = statement.executeQuery(query);
      if (result.next()) { 
        count = result.getInt(1);
        result.close();        
        statement.close();
        connection.close();
      }      
    } catch(SQLException e) {
      System.out.println("CommonData: getRandomProject: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
    return count;   
  }

  public static String getUnapprovedWordId(String word) {
    String id = "";
    try {
      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      ResultSet result = statement.executeQuery("SELECT id FROM wordlist WHERE word='" + word + "' LIMIT 1");
      if(result.next()) {
        id = result.getString(1);
        result.close();
        statement.close();
        connection.close();
      }
    } catch(SQLException e) {
      System.out.println("CommonFunctions: getUnapprovedWordId: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
    return id;
  }
}
