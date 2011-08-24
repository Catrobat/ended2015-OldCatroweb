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
import java.util.HashMap;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

public class UploaderLoginOrRegister {

  protected String webSite;

  public UploaderLoginOrRegister(String webSite) {
    this.webSite = webSite;
  }


  public synchronized String loginOrRegister(HashMap<String, String> payload) {
    HashMap<String, String> verifiedPayload = verifyPayload(payload);
    Charset utf8 = Charset.forName("UTF-8");
    HttpClient httpclient = new DefaultHttpClient();
    try {
      MultipartEntity reqEntity = new MultipartEntity();

      HttpPost httppost = new HttpPost(this.webSite + Config.TESTS_BASE_PATH.substring(1) + "api/loginOrRegister/loginOrRegister.json");

      reqEntity.addPart("registrationUsername", new StringBody("catrowebs", utf8));
      reqEntity.addPart("registrationPassword", new StringBody("cat.roid.web", utf8));
      reqEntity.addPart("registrationEmail", new StringBody("adminis@catroid.org", utf8));
      reqEntity.addPart("registrationCountry", new StringBody("AT", utf8));
      
      httppost.setEntity(reqEntity);
      HttpResponse response = httpclient.execute(httppost);
      HttpEntity resEntity = response.getEntity();

      if(resEntity != null) {
        System.out.println("try: String answer = EntityUtils.toString(resEntity);");
        String answer = EntityUtils.toString(resEntity);
        if(CommonFunctions.getValueFromJSONobject(answer, "statusCode").equals("200")) {
          System.out.println(CommonFunctions.getValueFromJSONobject(answer, "answer"));
          
        }
        else if(CommonFunctions.getValueFromJSONobject(answer, "statusCode").equals("500")) {
          System.out.println(CommonFunctions.getValueFromJSONobject(answer, "answer"));
        }
        System.out.println(CommonFunctions.getValueFromJSONobject(answer, "answer"));
        System.out.println(CommonFunctions.getValueFromJSONobject(answer, "statusCode"));
        return answer;
      }        
            
      System.out.println("catch(Exception e)");
      
    } catch(Exception e) {
      System.out.println("Unknown Exception - upload failed!");
      return "";
    } finally {
      try {
        httpclient.getConnectionManager().shutdown();
      } catch(Exception ignore) {
      }
    }
    return "";
  }

  private HashMap<String, String> verifyPayload(HashMap<String, String> logindata) {
    HashMap<String, String> data = new HashMap<String, String>();

    String registrationUsername = logindata.get("registrationUsername");
    data.put("registrationUsername", registrationUsername);

    String registrationPassword = logindata.get("registrationPassword");
    data.put("registrationPassword", registrationPassword);

    String registrationEmail = logindata.get("registrationEmail");
    data.put("registrationEmail", registrationEmail);

    String registrationCountry = logindata.get("registrationCountry");
    data.put("registrationCountry", registrationCountry);

    return data;
  }

}
