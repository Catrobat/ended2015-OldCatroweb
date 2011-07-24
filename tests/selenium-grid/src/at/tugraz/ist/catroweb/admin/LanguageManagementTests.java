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

package at.tugraz.ist.catroweb.admin;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "admin", "LanguageManagementTests" })
public class LanguageManagementTests extends BaseTest {

  @Test(groups = { "functionality" }, description = "update language packs")
  public void updateLanguagePack() throws Throwable {
    try {
      openAdminLocation("?userLanguage=" + Config.SITE_DEFAULT_LANGUAGE);
      session().click("aAdministrationTools");
      waitForPageToLoad();
      session().click("aAdminToolsLanguageManagement");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools - Language Management"));
      assertTrue(session().isElementPresent("xpath=//select[@id='supportedLanguageSelect']"));
      assertTrue(session().isElementPresent("xpath=//a[@id='doUpdateLink']"));
      session().select("supportedLanguageSelect", "value=de");
      session().click("xpath=//a[@id='doUpdateLink']");
      ajaxWait();
      captureScreen("LanguageManagementTests.updateLanguagePack12");
      assertTrue(session().isTextPresent("The language de was successfully updated!"));
      session().click("aAdminToolsBackToTools");
      waitForPageToLoad();
      assertTrue(session().isTextPresent("Administration Tools"));
    } catch(AssertionError e) {
      captureScreen("LanguageManagementTests.updateLanguagePack");
      throw e;
    }catch(Exception e) {
      captureScreen("LanguageManagementTests.updateLanguagePack");
      throw e;
    }
  }
}
