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

package at.tugraz.ist.catroweb.catroid.index;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;
import static org.testng.AssertJUnit.*;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;
import org.testng.annotations.Test;


import at.tugraz.ist.catroweb.common.*;

public class IndexTests {
  @BeforeMethod(groups = {"default", "index"}, alwaysRun = true)
  @Parameters({"seleniumHost", "seleniumPort", "browser", "webSite"})  
  protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
    startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
    session().setSpeed(CommonFunctions.getInstance().setSpeed());
    session().setTimeout(CommonConfig.TIMEOUT);
  }

  @AfterMethod(groups = {"default", "license"}, alwaysRun = true)
  protected void closeSession() {
    closeSeleniumSession();
  }

  @Test(groups = {"index", "firefox", "default"}, description = "location tests")    
  public void location() {

    session().open("/catroid/index/9999999999999999999");
    session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);
    session().waitForCondition(CommonFunctions.getInstance().getAjaxWaitString(), "5000");        
    //test page title and header title        
    assertTrue(session().getTitle().matches("^Catroid Website -.*"));        
    assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    assertFalse(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_NEXT_BUTTON));        

    String location = CommonDataProvider.getRandomLongString();
    session().open("/catroid/index/"+location);
    session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);
    session().waitForCondition(CommonFunctions.getInstance().getAjaxWaitString(), "5000");        
    //test page title and header title        
    assertTrue(session().getTitle().matches("^Catroid Website -.*"));        
    assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));
    assertFalse(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_PREV_BUTTON));        
    
    
    location = CommonDataProvider.getRandomLongString();
    session().open("/catroid/details/"+location);
    session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);
    session().waitForCondition(CommonFunctions.getInstance().getAjaxWaitString(), "5000");        
    //test page title and header title        
    CommonAssertions.assertRegExp(".*/catroid/errorPage", session().getLocation());
    assertTrue(session().isTextPresent(location));   
  }
     
    @Test(groups = {"index", "firefox", "default"}, description = "click download,header,details -links ")
    public void index() throws Throwable {
        session().open("/");
        session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);
        session().waitForCondition(CommonFunctions.getInstance().getAjaxWaitString(), "5000");        
        //test page title and header title        
        assertTrue(session().getTitle().matches("^Catroid Website -.*"));        
        assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));

        // test catroid header text
        assertTrue(session().isElementPresent("xpath=//img[@class='catroidLettering']"));        
        // test logo link
        assertTrue(session().isElementPresent("xpath=//div[@class='webHeadLogo']"));
        session().click("xpath=//div[@id='aIndexWebLogoLeft']");        
        session().waitForCondition(CommonFunctions.getInstance().getAjaxWaitString(), "5000");
        
        //test catroid download link
        assertTrue(session().isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
        session().click("xpath=//a[@id='aIndexWebLogoMiddle']");
        session().selectWindow("_blank");
        session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);
        assertTrue(session().isTextPresent("Catroid_0-4-3d.apk"));
        assertTrue(session().isTextPresent("Paintroid_0.6.4b.apk"));
        session().close();        
        session().selectWindow(null);
        
        //test links to details page
        session().click("xpath=//a[@class='projectListDetailsLink']");
        session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);
        //assertTrue(CommonAssertions.isDetailsLocation(session().getLocation()));        
        CommonAssertions.assertRegExp(".*/catroid/details/[0-9]+",session().getLocation());
        
        session().goBack();
        session().waitForPageToLoad(CommonConfig.WAIT_FOR_PAGE_TO_LOAD_LONG);                
        session().waitForCondition(CommonFunctions.getInstance().getWaitForConditionIsElementPresentString("xpath=//a[@id='aIndexWebLogoMiddle']"),"10000");
        session().waitForCondition(CommonFunctions.getInstance().getWaitForConditionIsTextPresentString(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE),"10000");
        
        session().waitForCondition(CommonFunctions.getInstance().getWaitForConditionIsElementPresentString("xpath=//a[@id='aIndexWebLogoMiddle']"),"10000");
        assertTrue(session().isTextPresent(CommonStrings.NEWEST_PROJECTS_PAGE_TITLE));        
        assertTrue(session().isElementPresent("xpath=//a[@id='aIndexWebLogoMiddle']"));
        
        //test home link
        session().click("xpath=//div[@id='aIndexWebLogoLeft']");        
        session().waitForCondition(CommonFunctions.getInstance().getAjaxWaitString(), "5000");
        assertTrue(session().isElementPresent("xpath=//img[@class='catroidLettering']"));        
        
      }    
}
