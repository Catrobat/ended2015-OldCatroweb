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

public class ProjectUploader {
  protected List<HashMap<String, String>> uploadedProjects;

  public ProjectUploader() {
    this.uploadedProjects = Collections.synchronizedList(new ArrayList<HashMap<String, String>>());
  }

  public void cleanup() {
    for(HashMap<String, String> item : this.uploadedProjects) {
      deleteProject(item.get("projectId"));
    }
    this.uploadedProjects.clear();
  }

  public void upload() {
    upload(new HashMap<String, String>());
  }

  public void upload(HashMap<String, String> payload) {
    HttpClient httpclient = new DefaultHttpClient();
    HashMap<String, String> verifiedPayload = verifyPayload(payload);
    try {
      MultipartEntity reqEntity = new MultipartEntity();
      reqEntity.addPart("projectTitle", new StringBody(verifiedPayload.get("projectTitle")));
      reqEntity.addPart("projectDescription", new StringBody(verifiedPayload.get("projectDescription")));
      reqEntity.addPart("upload", new FileBody(new File(verifiedPayload.get("upload"))));
      reqEntity.addPart("fileChecksum", new StringBody(verifiedPayload.get("fileChecksum")));
      reqEntity.addPart("deviceIMEI", new StringBody(verifiedPayload.get("deviceIMEI")));
      reqEntity.addPart("userEmail", new StringBody(verifiedPayload.get("userEmail")));
      reqEntity.addPart("userLanguage", new StringBody(verifiedPayload.get("userLanguage")));
      reqEntity.addPart("token", new StringBody(verifiedPayload.get("token")));

      HttpPost httppost = new HttpPost(this.webSite + CommonConfig.TESTS_BASE_PATH.substring(1) + "api/upload/upload.json");
      httppost.setEntity(reqEntity);
      HttpResponse response = httpclient.execute(httppost);
      HttpEntity resEntity = response.getEntity();

      if(resEntity != null) {
        String projectId = CommonFunctions.getValueFromJSONobject(EntityUtils.toString(resEntity), "projectId");
        verifiedPayload.put("projectId", projectId);
        this.uploadedProjects.add(verifiedPayload);
      }
      EntityUtils.consume(resEntity);
    } catch(Exception e) {
      System.out.println("Unknown Exception - upload failed!");
      e.printStackTrace();
    } finally {
      try {
        httpclient.getConnectionManager().shutdown();
      } catch(Exception ignore) {
      }

    }
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

  public void remove(String key) {
    String projectId = getProjectId(key);
    if(projectId.equals("")) {
      projectId = key;
    }

    this.uploadedProjects.remove(getProject(projectId));
    deleteProject(projectId);
  }

  private HashMap<String, String> verifyPayload(HashMap<String, String> payload) {
    HashMap<String, String> data = new HashMap<String, String>();

    String projectTitle = CommonConfig.DEFAULT_UPLOAD_TITLE;
    if(payload.containsKey("projectTitle")) {
      projectTitle = payload.get("projectTitle");
    }
    data.put("projectTitle", projectTitle);

    String projectDescription = CommonConfig.DEFAULT_UPLOAD_DESCRIPTION;
    if(payload.containsKey("projectDescription")) {
      projectDescription = payload.get("projectDescription");
    }
    data.put("projectDescription", projectDescription);

    String upload = CommonConfig.DEFAULT_UPLOAD_FILE;
    if(payload.containsKey("upload")) {
      upload = payload.get("upload");
    }
    data.put("upload", upload);

    String fileChecksum = CommonConfig.DEFAULT_UPLOAD_CHECKSUM;
    if(payload.containsKey("fileChecksum")) {
      fileChecksum = payload.get("fileChecksum");
    }
    data.put("fileChecksum", fileChecksum);

    String deviceIMEI = CommonConfig.DEFAULT_UPLOAD_IMEI;
    if(payload.containsKey("deviceIMEI")) {
      deviceIMEI = payload.get("deviceIMEI");
    }
    data.put("deviceIMEI", deviceIMEI);

    String userEmail = CommonConfig.DEFAULT_UPLOAD_EMAIL;
    if(payload.containsKey("userEmail")) {
      userEmail = payload.get("userEmail");
    }
    data.put("userEmail", userEmail);

    String userLanguage = CommonConfig.DEFAULT_UPLOAD_LANGUAGE;
    if(payload.containsKey("userLanguage")) {
      userLanguage = payload.get("userLanguage");
    }
    data.put("userLanguage", userLanguage);

    String token = CommonConfig.DEFAULT_UPLOAD_TOKEN;
    if(payload.containsKey("token")) {
      token = payload.get("token");
    }
    data.put("token", token);

    return data;
  }

  private void deleteProject(String projectId) {
    try {
      Connection connection = DriverManager.getConnection(CommonConfig.DB_HOST + CommonConfig.DB_NAME, CommonConfig.DB_USER, CommonConfig.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeQuery("DELETE FROM projects WHERE id='" + projectId + "'");
      statement.close();
      connection.close();
    } catch(SQLException e) {
      e.printStackTrace();
    }

    (new File(CommonConfig.FILESYSTEM_BASE_PATH + CommonConfig.PROJECTS_DIRECTORY + projectId + CommonConfig.PROJECTS_EXTENTION)).delete();
    (new File(CommonConfig.FILESYSTEM_BASE_PATH + CommonConfig.PROJECTS_QR_DIRECTORY + projectId + CommonConfig.PROJECTS_QR_EXTENTION)).delete();
    (new File(CommonConfig.FILESYSTEM_BASE_PATH + CommonConfig.PROJECTS_THUMBNAIL_DIRECTORY + projectId + CommonConfig.PROJECTS_THUMBNAIL_EXTENTION_ORIG))
        .delete();
    (new File(CommonConfig.FILESYSTEM_BASE_PATH + CommonConfig.PROJECTS_THUMBNAIL_DIRECTORY + projectId + CommonConfig.PROJECTS_THUMBNAIL_EXTENTION_SMALL))
        .delete();
    (new File(CommonConfig.FILESYSTEM_BASE_PATH + CommonConfig.PROJECTS_THUMBNAIL_DIRECTORY + projectId + CommonConfig.PROJECTS_THUMBNAIL_EXTENTION_LARGE))
        .delete();
    CommonFunctions.deleteDir(new File(CommonConfig.FILESYSTEM_BASE_PATH + CommonConfig.PROJECTS_UNZIPPED_DIRECTORY + projectId
        + CommonConfig.FILESYSTEM_SEPARATOR));
  }

  private HashMap<String, String> getProject(String key) {
    for(HashMap<String, String> item : this.uploadedProjects) {
      if(item.get("projectId").equals(key))
        return item;
    }
    return new HashMap<String, String>();
  }
}
