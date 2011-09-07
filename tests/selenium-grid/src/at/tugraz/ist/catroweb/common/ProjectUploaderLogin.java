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

import java.nio.charset.Charset;
import java.util.HashMap;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

public class ProjectUploaderLogin {

  protected String webSite;

  public ProjectUploaderLogin(String webSite) {
    this.webSite = webSite;
  }


  public synchronized String loginOrRegister(HashMap<String, String> dataset) {

    Charset utf8 = Charset.forName("UTF-8");
    HttpClient httpclient = new DefaultHttpClient();
    String answer = "";
    try {
      MultipartEntity reqEntity = new MultipartEntity();

      HttpPost httppost = new HttpPost(this.webSite + Config.TESTS_BASE_PATH.substring(1) + "api/loginOrRegister/loginOrRegister.json");

      reqEntity.addPart("registrationUsername", new StringBody(dataset.get("username"), utf8));
      reqEntity.addPart("registrationPassword", new StringBody(dataset.get("password"), utf8));
      reqEntity.addPart("registrationEmail", new StringBody(dataset.get("email"), utf8));
      reqEntity.addPart("registrationCountry", new StringBody(dataset.get("country"), utf8));
      
      httppost.setEntity(reqEntity);
      HttpResponse response = httpclient.execute(httppost);
      HttpEntity resEntity = response.getEntity();
      
      answer = EntityUtils.toString(resEntity);
      
      if(resEntity != null) {
        return answer;
      }        
            
    } catch(Exception e) {
      System.out.println(CommonFunctions.getValueFromJSONobject(answer, "answer"));
      return "";
    } finally {
      try {
        httpclient.getConnectionManager().shutdown();
      } catch(Exception ignore) {
      }
    }
    return "";
  }

}
