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

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "LicenseTests" })
public class LicenseTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check privacy policy link/page")
  public void privacyPolicy() throws Throwable {
    try {
      openLocation();
      session().click("xpath=//a[@class='license']");
      waitForPageToLoad();

      assertTrue(session().isTextPresent("Privacy Policy"));
      session().isElementPresent("xpath=//p[@class='licenseText']/a");
    } catch(AssertionError e) {
      captureScreen("LicenseTests.privacyPolicy");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.privacyPolicy");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check terms of use link/page")
  public void termsOfUse() throws Throwable {
    try {
      openLocation();
      session().click("xpath=//a[@class='license'][2]");
      waitForPageToLoad();

      assertTrue(session().isTextPresent("Welcome to the Catroid community!"));
      assertTrue(session().isTextPresent("As part of the Catroid community, you are sharing projects and ideas with people:"));
      clickAndWaitForPopUp("xpath=//p[@class='licenseText'][3]/a", "_blank");
      assertRegExp("test", "test");
      assertRegExp(".*Creative Commons — Attribution-ShareAlike 2.0 Generic — CC BY-SA 2.0.*", session().getTitle());
      closePopUp();

      clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[2]", "_blank");
      assertTrue(session().isTextPresent("GNU GENERAL PUBLIC LICENSE"));
      assertTrue(session().isTextPresent("Version 3, 29 June 2007"));
      closePopUp();

      clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[3]", "_blank");
      assertTrue(session().isTextPresent("GNU AFFERO GENERAL PUBLIC LICENSE"));
      assertTrue(session().isTextPresent("Version 3, 19 November 2007"));
      closePopUp();

      clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[4]", "_blank");
      assertRegExp(".*catroid -.*", session().getTitle());
      assertRegExp(".*An on-device graphical programming language for Android inspired by Scratch.*", session().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.termsOfUse");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.termsOfUse");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check copyright policy link/page")
  public void copyrightPolicy() throws Throwable {
    try {
      openLocation();
      session().click("xpath=//a[@class='license'][3]");
      waitForPageToLoad();

      assertTrue(session().isTextPresent("Copyright Policy"));
      session().isElementPresent("xpath=//p[@class='licenseText']/a");
      clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[2]", "_blank");
      assertTrue(session().isTextPresent("Directive 2001/29/EC of the European Parliament and of the Council"));
      assertTrue(session().isTextPresent("32001L0029"));
      closePopUp();

      clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a[3]", "_blank");
      assertTrue(session().isTextPresent("Chilling Effects"));
      assertTrue(session().isTextPresent("Chilling Effects Clearinghouse - www.chillingeffects.org"));
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.copyrightPolicy");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.copyrightPolicy");
      throw e;
    }
  }

  @Test(groups = { "visibility", "popupwindows" }, description = "check imprint link/page")
  public void imprint() throws Throwable {
    try {
      openLocation();
      session().click("xpath=//a[@class='license'][4]");
      waitForPageToLoad();

      assertTrue(session().isTextPresent("Address"));
      assertTrue(session().isTextPresent("Institute for Software Technology"));
      assertTrue(session().isTextPresent("Graz University of Technology"));
      assertTrue(session().isTextPresent("Inffeldgasse 16B/II"));
      assertTrue(session().isTextPresent("8010 Graz"));
      assertTrue(session().isTextPresent("Austria"));

      clickAndWaitForPopUp("xpath=//p[@class='licenseText']/a", "_blank");
      assertRegExp(".*IST web - Index.*", session().getTitle());
      closePopUp();
    } catch(AssertionError e) {
      captureScreen("LicenseTests.imprint");
      throw e;
    } catch(Exception e) {
      captureScreen("LicenseTests.imprint");
      throw e;
    }
  }
}
