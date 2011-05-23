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

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.HashMap;

import javax.xml.bind.ParseConversionEvent;

import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class DetailsTests extends BaseTest {

  @Test(dataProvider = "randomIds", groups = { "catroid", "firefox", "default" }, description = "view + download counter test")
  public void detailsPageCounter(String id, String title, String description) throws Throwable {
    session().open(Config.TESTS_BASE_PATH + "catroid/details/" + id);
    waitForPageToLoad();
    // project title
    assertEquals(title, session().getText("xpath=//div[@class='detailsProjectTitle']"));

    // test the view counter
    int numOfViews = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats']/b"));
    session().refresh();
    waitForPageToLoad();
    int numOfViewsAfter = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats']/b"));
    assertEquals(numOfViews + 1, numOfViewsAfter);
    
    // test the download counter
    int numOfDownloads = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats'][2]/b"));
    session().click("xpath=//div[@class='detailsDownloadButton']/a[1]");
       
    //session().waitForPopUp("1", "2000");
    session().keyPressNative("27"); // press escape key
    session().refresh();
    waitForPageToLoad();    
    
    int numOfDownloadsAfter = Integer.parseInt(session().getText("xpath=//p[@class='detailsStats'][2]/b"));
    log(numOfDownloads);;
    log(numOfDownloadsAfter);;
    assertEquals(numOfDownloads + 1, numOfDownloadsAfter);
    session().click("xpath=//div[@class='detailsMainImage']/a[1]");    
    session().keyPressNative("27"); // press escape key
    session().refresh();
    waitForPageToLoad();
    numOfDownloadsAfter = Integer.valueOf(session().getText("xpath=//p[@class='detailsStats'][2]/b"));
    log(numOfDownloads);;
    log(numOfDownloadsAfter);;
    assertEquals(numOfDownloads + 2, numOfDownloadsAfter);
  }

  @DataProvider(name = "randomIds")
  public Object[][] randomIds() {
    HashMap<String, String> data = CommonData.getRandomProject();
    Object[][] returnArray = new Object[][] { { data.get("id"), data.get("title"), data.get("description") }, };
    return returnArray;
  }
}
