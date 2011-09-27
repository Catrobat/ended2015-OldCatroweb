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

import java.util.HashMap;

import org.openqa.selenium.By;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "catroid", "MenuTests" })
public class MenuTests extends BaseTest {

  @Test(groups = { "visibility" }, description = "check button visibility")
  public void buttonVisibility() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      assertTrue(isVisible(By.id("menuLoginButton")));
      assertTrue(isEditable(By.id("menuLoginButton")));
      
      assertTrue(isVisible(By.id("menuRegistrationButton")));
      assertTrue(isEditable(By.id("menuRegistrationButton")));
      
      assertTrue(isVisible(By.id("menuProfileButton")));
      assertFalse(isEditable(By.id("menuProfileButton")));
      
      assertTrue(isVisible(By.id("menuForumButton")));
      assertTrue(isEditable(By.id("menuForumButton")));
      
      assertTrue(isVisible(By.id("menuWikiButton")));
      assertTrue(isEditable(By.id("menuWikiButton")));

    } catch(AssertionError e) {
      captureScreen("MenuTests.buttonVisibility");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.buttonVisibility");
      throw e;
    }
  }


  @Test(dataProvider = "validLoginData", groups = { "visibility", "popupwindows" }, description = "check profile button + wiki links; logged in/out")
  public void loginAndProfileLinks(HashMap<String, String> dataset) throws Throwable {
    try {
      openLocation();
      
      String wikiUsername = dataset.get("username").substring(0, 1).toUpperCase() + dataset.get("username").substring(1).toLowerCase();
      
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      driver().findElement(By.id("headerProfileButton"));      
      assertTrue(isVisible(By.id("menuProfileButton")));
      assertFalse(isEditable(By.id("menuProfileButton")));
      
      driver().findElement(By.id("menuLoginButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      
      driver().findElement(By.id("headerCancelButton")).click();
      assertFalse(isVisible(By.id("loginUsername")));
      assertFalse(isVisible(By.id("loginPassword")));
      assertFalse(isVisible(By.id("loginSubmitButton")));
      
      driver().findElement(By.id("menuLoginButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      
      driver().findElement(By.id("loginUsername")).sendKeys(dataset.get("username"));
      driver().findElement(By.id("loginPassword")).sendKeys(dataset.get("password"));

      driver().findElement(By.id("loginSubmitButton")).click();
      ajaxWait();

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertFalse(isTextPresent("Login"));
      assertTrue(isTextPresent("Logout"));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertTrue(isTextPresent(wikiUsername));
      closePopUp();
      
      driver().findElement(By.id("headerProfileButton"));      
      assertTrue(isVisible(By.id("menuProfileButton")));
      assertTrue(isEditable(By.id("menuProfileButton")));
      driver().findElement(By.id("menuProfileButton")).click();
      ajaxWait();
      
      assertTrue(isTextPresent(dataset.get("username")+"\'s Profile"));
      
      driver().findElement(By.id("headerMenuButton")).click();
      ajaxWait();
      
      assertTrue(isVisible(By.id("menuProfileButton")));
      assertTrue(isEditable(By.id("menuProfileButton")));
      driver().findElement(By.id("headerProfileButton")).click();
      ajaxWait();
      
      assertTrue(isVisible(By.id("logoutSubmitButton")));
      driver().findElement(By.id("logoutSubmitButton")).click();
      ajaxWait();
      
      assertTrue(isVisible(By.id("projectListTitle")));
    } catch(AssertionError e) {
      captureScreen("MenuTests.loginAndProfileLinks." + dataset.get("username"));
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.loginAndProfileLinks." + dataset.get("username"));
      throw e;
    }
  }
  
  @Test(groups = { "visibility", "popupwindows" }, description = "check board + wiki links; logged in/out")
  public void boardAndWikiLinks() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      clickAndWaitForPopUp(By.id("menuForumButton"));
      assertRegExp(".*/addons/board(/)?$", driver().getCurrentUrl());
      assertTrue(isTextPresent(("Board index")));
      assertTrue(isTextPresent(("Login")));
      closePopUp();

      clickAndWaitForPopUp(By.id("menuWikiButton"));
      assertRegExp(".*/wiki/Main_Page$", driver().getCurrentUrl());

      assertTrue(isTextPresent(("Main Page")));
      assertFalse(isElementPresent(By.id("pt-userpage")));
      closePopUp();

      driver().findElement(By.id("menuLoginButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      assertTrue(isVisible(By.id("loginSubmitButton")));

    } catch(AssertionError e) {
      captureScreen("MenuTests.boardAndWikiLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.boardAndWikiLinks");
      throw e;
    }
  }
  
  @Test(groups = { "visibility", "popupwindows" }, description = "check board + wiki links; logged in/out")
  public void loginSignUpRecoveryLinks() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      // TODO Login Broken
      driver().findElement(By.id("menuLoginButton")).click();
      ajaxWait();
      assertTrue(isVisible(By.id("loginUsername")));
      assertTrue(isVisible(By.id("loginPassword")));
      assertTrue(isVisible(By.id("loginSubmitButton")));
      
      driver().findElement(By.id("headerCancelButton")).click();
      assertFalse(isVisible(By.id("loginUsername")));
      assertFalse(isVisible(By.id("loginPassword")));
      assertFalse(isVisible(By.id("loginSubmitButton")));

      driver().findElement(By.id("menuRegistrationButton")).click();
      assertRegExp(".*/catroid/registration$", driver().getCurrentUrl());
      assertTrue(isTextPresent(("Create a new account")));
      assertTrue(isVisible(By.id("registrationSubmit")));
      
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

    } catch(AssertionError e) {
      captureScreen("loginSignUpRecoveryLinks.boardAndWikiLinks");
      throw e;
    } catch(Exception e) {
      captureScreen("loginSignUpRecoveryLinks.boardAndWikiLinks");
      throw e;
    }
  }

  @Test(groups = { "visibility" }, description = "check menu home button")
  public void homeButton() throws Throwable {
    try {
      openLocation();
      driver().findElement(By.id("headerMenuButton")).click();
      assertRegExp(".*/catroid/menu$", driver().getCurrentUrl());

      assertTrue(isElementPresent(By.id("headerHomeButton")));
      driver().findElement(By.id("headerHomeButton")).click();
      assertFalse(isElementPresent(By.id("headerHomeButton")));
      assertRegExp(".*/catroid/index(/[0-9]+)?", driver().getCurrentUrl());
    } catch(AssertionError e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    } catch(Exception e) {
      captureScreen("MenuTests.homeButton");
      throw e;
    }
  }
  
  @SuppressWarnings("serial")
  @DataProvider(name = "validLoginData")
  public Object[][] validLoginData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "catroweb");
        put("password", "cat.roid.web");
      }
    } } };
    return dataArray;
  }

  @SuppressWarnings("serial")
  @DataProvider(name = "invalidLoginData")
  public Object[][] invalidLoginData() {
    Object[][] dataArray = new Object[][] { { new HashMap<String, String>() {
      {
        put("username", "wrongUser");
        put("password", "wrongPassword");
      }
    } } };
    return dataArray;
  }
}
