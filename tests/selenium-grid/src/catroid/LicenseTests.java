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

package at.tugraz.ist.catroweb.catroid.license;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;
import static org.testng.AssertJUnit.assertTrue;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;
import org.testng.annotations.Test;

import at.tugraz.ist.catroweb.common.*;

public class LicenseTests {
  @BeforeMethod(groups = {"default", "license"}, alwaysRun = true)
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

  @Test(groups = {"license", "firefox", "default"}, description = "check privacy policy link/page")
  public void privacyPolicy() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().click("xpath=//a[@class='license']");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);

    assertTrue(session().isTextPresent("Privacy Policy"));
    session().isElementPresent("xpath=//p[@class='licenseText']/a");
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check terms of use link/page")
  public void termsOfUse() throws Throwable {
	session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().click("xpath=//a[@class='license'][2]");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);

    assertTrue(session().isTextPresent("Welcome to the Catroid community! As part of the Catroid community, you are sharing projects and ideas with people: "));
    session().click("xpath=//p[@class='licenseText'][3]/a");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    CommonAssertions.assertRegExp(".*Creative Commons — Attribution-ShareAlike 2.0 Generic — CC BY-SA 2.0.*", session().getTitle());
    session().close();
    session().selectWindow(null);

    session().click("xpath=//p[@class='licenseText']/a[2]");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    assertTrue(session().isTextPresent("GNU GENERAL PUBLIC LICENSE"));
    assertTrue(session().isTextPresent("Version 3, 29 June 2007"));
    session().close();
    session().selectWindow(null);

    session().click("xpath=//p[@class='licenseText']/a[3]");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    assertTrue(session().isTextPresent("GNU AFFERO GENERAL PUBLIC LICENSE"));
    assertTrue(session().isTextPresent("Version 3, 19 November 2007"));
    session().close();
    session().selectWindow(null);

    session().click("xpath=//p[@class='licenseText']/a[4]");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    CommonAssertions.assertRegExp(".*catroid -.*", session().getTitle());
    CommonAssertions.assertRegExp(".*An on-device graphical programming language for Android inspired by Scratch.*", session().getTitle());
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check copyright policy link/page")
  public void copyrightPolicy() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().click("xpath=//a[@class='license'][3]");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);

    assertTrue(session().isTextPresent("Copyright Policy"));
    session().isElementPresent("xpath=//p[@class='licenseText']/a");
    session().click("xpath=//p[@class='licenseText']/a[2]");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    assertTrue(session().isTextPresent("Directive 2001/29/EC of the European Parliament and of the Council"));
    assertTrue(session().isTextPresent("32001L0029"));
    session().close();
    session().selectWindow(null);
    
    session().click("xpath=//p[@class='licenseText']/a[3]");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    assertTrue(session().isTextPresent("Chilling Effects"));
    assertTrue(session().isTextPresent("Chilling Effects Clearinghouse - www.chillingeffects.org"));
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check imprint link/page")
  public void imprint() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().click("xpath=//a[@class='license'][4]");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);

    assertTrue(session().isTextPresent("Address"));
    assertTrue(session().isTextPresent("Institut für Softwaretechnologie"));
    assertTrue(session().isTextPresent("Technische Universität Graz"));
    assertTrue(session().isTextPresent("Inffeldgasse 16B/II"));
    assertTrue(session().isTextPresent("8010 Graz"));
    assertTrue(session().isTextPresent("Austria"));
    session().click("xpath=//p[@class='licenseText']/a");
    session().waitForPopUp("_blank", CommonConfig.TIMEOUT);
    session().selectWindow("_blank");
    CommonAssertions.assertRegExp(".*IST web - Index.*", session().getTitle());
  }

  @Test(groups = {"license", "firefox", "default"}, description = "check contact us link/page")
  public void contactUs() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().click("xpath=//a[@class='license'][5]");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    assertTrue(session().isTextPresent(("Contact us")));
    session().isElementPresent("xpath=//p[@class='licenseText']/a");
  }
}
