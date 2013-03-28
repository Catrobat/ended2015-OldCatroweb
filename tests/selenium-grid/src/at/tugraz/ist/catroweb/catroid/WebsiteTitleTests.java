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

package at.tugraz.ist.catroweb.catroid;

import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "WebsiteTitleTests" })
public class WebsiteTitleTests extends BaseTest {

  @Test(dataProvider = "websitePages", groups = { "visibility" }, description = "check html website titles/page")
  public void websiteTitle(String actualPage) throws Throwable {
    try {
      openLocation("catroid/" + actualPage);
      String websiteTitle = driver().findElement(By.xpath("//div[@class='webMainContentTitle']")).getText();
      assertTrue(driver().getTitle().matches(".*" + websiteTitle + ".*"));
    } catch(AssertionError e) {
      captureScreen("LicenseTests.privacyPolicy");
      throw e;
    } catch(Exception e) {
      captureScreen("PasswordRecoveryTests.passwordRecoveryIntro");
      throw e;
    }
  }

  @DataProvider(name = "websitePages")
  public Object[][] websitePages() {
    Object[][] returnArray = new Object[][] { { "copyrightpolicy" }, { "details/1" }, { "errorPage" }, { "imprint" }, { "index" }, { "license" }, { "login" },
        { "passwordrecovery" }, { "privacypolicy" }, { "projectlicense" }, { "registration" }, { "termsofuse" }, { "licenseofuploadedprojects" },
        { "licenseofsystem" }, { "termsofservice" }, { "agpl3standalone" }, { "ccbysa3" }, { "licenseadditionalterm" } };
    return returnArray;
  }
}
