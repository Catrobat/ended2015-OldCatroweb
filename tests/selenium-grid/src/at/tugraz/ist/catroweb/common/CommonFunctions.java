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
import java.util.Iterator;
import java.util.Map;

import org.json.simple.JSONValue;

public class CommonFunctions {
  public static String getAdminPath(String webSite) {
    return "http://" + Config.ADMIN_AREA_USER + ":" + Config.DB_PASS + "@" + webSite.replace("http://", "") + "admin";
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
      json = "{"+temp[1];
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
}
