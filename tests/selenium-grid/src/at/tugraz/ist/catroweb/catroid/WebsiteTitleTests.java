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

package at.tugraz.ist.catroweb.catroid;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import java.util.HashMap;

import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.CommonData;

@Test(groups = { "catroid", "WebsiteTitleTests" })
public class WebsiteTitleTests extends BaseTest {

  @Test(dataProvider = "websitePages", groups = { "visibility" }, description = "check html website titles/page")
  public void websiteTitle(String actualPage) throws Throwable {

    String website_title;
    String website_html_title;
   
    try {
      openLocation("catroid/"+actualPage);
      waitForPageToLoad();
      session().isTextPresent("Catroid Website - "); //xpath=html/title
      website_html_title = session().getTitle();
      
      website_title = session().getText("xpath=//div[@class='webMainContentTitle']");
      
      //reg exp to find string in html title
      String search_string = website_html_title;
      log(website_html_title);
      log(website_title);
      assertTrue(search_string.matches(".*"+website_title+".*"));

    } catch(AssertionError e) {
      captureScreen("LicenseTests.privacyPolicy");
      throw e;
    }

  }
  
  @DataProvider(name = "websitePages")
  public Object[][] websitePages() {
    Object[][] returnArray = new Object[][] { 
        { "contactus" }, 
        { "copyrightpolicy" }, 
        { "details/1" },
        { "errorPage" },
        { "imprint" },
        { "index" },
        { "license" },
        { "login" },
        //{ "menu" }, # has no title div!!
        { "passwordrecovery" },
        { "privacypolicy" },
        { "profile/catroweb" },
        { "projectlicense" },
        { "registration" },
        { "terms" } };
    return returnArray;
  }
  

}
