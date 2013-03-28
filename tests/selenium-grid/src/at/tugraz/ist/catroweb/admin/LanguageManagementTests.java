/**
  *Catroid: An on-device visual programming system for Android devices
  *Copyright (C) 2010-2013 The Catrobat Team
  *(<http://developer.catrobat.org/credits>)
  *
  *This program is free software: you can redistribute it and/or modify
  *it under the terms of the GNU Affero General Public License as
  *published by the Free Software Foundation, either version 3 of the
  *License, or (at your option) any later version.
  *
  *An additional term exception under section 7 of the GNU Affero
  *General Public License, version 3, is available at
  *http://developer.catrobat.org/license_additional_term
  *
  *This program is distributed in the hope that it will be useful,
  *but WITHOUT ANY WARRANTY; without even the implied warranty of
  *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  *GNU Affero General Public License for more details.
  *
  *You should have received a copy of the GNU Affero General Public License
  *along with this program. If not, see <http://www.gnu.org/licenses/>.
  */

package at.tugraz.ist.catroweb.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.Select;
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
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsLanguageManagement")).click();
      assertTrue(isTextPresent("Administration Tools - Language Management"));
      assertTrue(isElementPresent(By.xpath("//select[@id='supportedLanguageSelect']")));
      assertTrue(isElementPresent(By.xpath("//a[@id='doUpdateLink']")));
      (new Select(driver().findElement(By.id("supportedLanguageSelect")))).selectByValue("de");
      driver().findElement(By.id("doUpdateLink")).click();
      ajaxWait();
      assertTrue(isTextPresent("The language de was successfully updated!"));
      driver().findElement(By.id("aAdminToolsBackToTools")).click();
      assertTrue(isTextPresent("Administration Tools"));
    } catch(AssertionError e) {
      captureScreen("LanguageManagementTests.updateLanguagePack");
      throw e;
    }catch(Exception e) {
      captureScreen("LanguageManagementTests.updateLanguagePack");
      throw e;
    }
  }
}
