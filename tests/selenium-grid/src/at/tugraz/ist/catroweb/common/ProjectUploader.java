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
import java.nio.charset.Charset;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.sql.*;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;
import org.postgresql.Driver;

public class ProjectUploader {
  protected List<HashMap<String, String>> uploadedProjects;
  protected String webSite;

  public ProjectUploader(String webSite) {
    this.uploadedProjects = Collections.synchronizedList(new ArrayList<HashMap<String, String>>());
    this.webSite = webSite;
  }

  public synchronized void cleanup() {
    for(HashMap<String, String> item : this.uploadedProjects) {
      deleteProject(item.get("projectId"));
    }
    this.uploadedProjects.clear();
  }

  public void upload() {
    upload(new HashMap<String, String>());
  }

  public synchronized String upload(HashMap<String, String> payload) {
    HashMap<String, String> verifiedPayload = verifyPayload(payload);
    Charset utf8 = Charset.forName("UTF-8");
    HttpClient httpclient = new DefaultHttpClient();
    try {
      MultipartEntity reqEntity = new MultipartEntity();
      
      reqEntity.addPart("projectTitle", new StringBody(verifiedPayload.get("projectTitle"), utf8));
      reqEntity.addPart("projectDescription", new StringBody(verifiedPayload.get("projectDescription"), utf8));
      reqEntity.addPart("upload", new FileBody(new File(verifiedPayload.get("upload"))));
      reqEntity.addPart("fileChecksum", new StringBody(verifiedPayload.get("fileChecksum"), utf8));
      reqEntity.addPart("userEmail", new StringBody(verifiedPayload.get("userEmail"), utf8));
      reqEntity.addPart("userLanguage", new StringBody(verifiedPayload.get("userLanguage"), utf8));
      reqEntity.addPart("token", new StringBody(verifiedPayload.get("token"), utf8));

      HttpPost httppost = new HttpPost(this.webSite + Config.TESTS_BASE_PATH.substring(1) + "api/upload/upload.json");
      httppost.setEntity(reqEntity);
      HttpResponse response = httpclient.execute(httppost);
      HttpEntity resEntity = response.getEntity();

      if(resEntity != null) {
        String answer = EntityUtils.toString(resEntity);
        if(CommonFunctions.getValueFromJSONobject(answer, "statusCode").equals("200")) {
          String projectId = CommonFunctions.getValueFromJSONobject(answer, "projectId");
          verifiedPayload.put("projectId", projectId);
          this.uploadedProjects.add(verifiedPayload);
        }
        return answer;
      }
    } catch(Exception e) {
      System.out.println("Unknown Exception - upload failed! " + e.getMessage());
      return "";
    } finally {
      try {
        httpclient.getConnectionManager().shutdown();
      } catch(Exception ignore) {
      }
    }
    return "";
  }

  public String getProjectId(String key) {
    for(HashMap<String, String> item : this.uploadedProjects) {
      for(String value : item.values()) {
        if(value.equals(key))
          return item.get("projectId");
      }
    }
    return "";
  }

  public synchronized void remove(String key) {
    String projectId = getProjectId(key);
    if(projectId.equals("")) {
      projectId = key;
    }

    this.uploadedProjects.remove(getProject(projectId));
    deleteProject(projectId);
  }

  private HashMap<String, String> verifyPayload(HashMap<String, String> payload) {
    HashMap<String, String> data = new HashMap<String, String>();

    String projectTitle = Config.DEFAULT_UPLOAD_TITLE;
    if(payload.containsKey("projectTitle")) {
      projectTitle = payload.get("projectTitle");
    }
    data.put("projectTitle", projectTitle);

    String projectDescription = Config.DEFAULT_UPLOAD_DESCRIPTION;
    if(payload.containsKey("projectDescription")) {
      projectDescription = payload.get("projectDescription");
    }
    data.put("projectDescription", projectDescription);

    String upload = Config.DEFAULT_UPLOAD_FILE;
    if(payload.containsKey("upload")) {
      upload = payload.get("upload");
    }
    data.put("upload", upload);

    String fileChecksum = Config.DEFAULT_UPLOAD_CHECKSUM;
    if(payload.containsKey("fileChecksum")) {
      fileChecksum = payload.get("fileChecksum");
    }
    data.put("fileChecksum", fileChecksum);

    String userEmail = Config.DEFAULT_UPLOAD_EMAIL;
    if(payload.containsKey("userEmail")) {
      userEmail = payload.get("userEmail");
    }
    data.put("userEmail", userEmail);

    String userLanguage = Config.DEFAULT_UPLOAD_LANGUAGE;
    if(payload.containsKey("userLanguage")) {
      userLanguage = payload.get("userLanguage");
    }
    data.put("userLanguage", userLanguage);

    String token = Config.DEFAULT_UPLOAD_TOKEN;
    if(payload.containsKey("token")) {
      token = payload.get("token");
    }
    data.put("token", token);

    return data;
  }

  private void deleteProject(String projectId) {
    if(!projectId.equals("")) {
      try {
        Driver driver = new Driver();
        DriverManager.registerDriver(driver);

        Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
        Statement statement = connection.createStatement();
        statement.executeUpdate("DELETE FROM projects WHERE id='" + projectId + "';");
        statement.close();
        connection.close();
        DriverManager.deregisterDriver(driver);
      } catch(SQLException e) {
        System.out.println("ProjectUploader: deleteProject: SQL Exception couldn't execute sql query!");
      }

      (new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_DIRECTORY + projectId + Config.PROJECTS_EXTENTION)).delete();
      (new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_QR_DIRECTORY + projectId + Config.PROJECTS_QR_EXTENTION)).delete();
      (new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_THUMBNAIL_DIRECTORY + projectId + Config.PROJECTS_THUMBNAIL_EXTENTION_ORIG)).delete();
      (new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_THUMBNAIL_DIRECTORY + projectId + Config.PROJECTS_THUMBNAIL_EXTENTION_SMALL)).delete();
      (new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_THUMBNAIL_DIRECTORY + projectId + Config.PROJECTS_THUMBNAIL_EXTENTION_LARGE)).delete();
      CommonFunctions.deleteDir(new File(Config.FILESYSTEM_BASE_PATH + Config.PROJECTS_UNZIPPED_DIRECTORY + projectId + Config.FILESYSTEM_SEPARATOR));
    } else {
      System.out.println("ProjectUploader: deleteProject: invalid project id:'" + projectId + "' - couldn't delete!");
    }
  }

  private HashMap<String, String> getProject(String key) {
    for(HashMap<String, String> item : this.uploadedProjects) {
      if(item.get("projectId").equals(key))
        return item;
    }
    return new HashMap<String, String>();
  }
}
