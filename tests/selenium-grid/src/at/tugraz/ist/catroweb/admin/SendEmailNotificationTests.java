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

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.testng.annotations.Test;

import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;

@Test(groups = { "admin", "EditProjectsTests" })
public class SendEmailNotificationTests extends BaseTest {

  
  @Test(groups = { "functionality", "sendEmail" }, description = "check elements present (text, buttons) ")
  public void checkElements() throws Throwable {
    try {
      openAdminLocation();
      ajaxWait();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsSendEmailNotification")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("Administration Tools - Send e-mail notification"));
      assertTrue(isTextPresent("ID"));
      assertTrue(isTextPresent("Username"));
      assertTrue(isTextPresent("E-Mail"));
      assertTrue(isTextPresent("Gender"));
      assertTrue(isTextPresent("Country"));
      assertTrue(isTextPresent("Send E-Mail?"));
      assertTrue(isTextPresent(" (0 user(s) selected)"));
      assertTrue(isTextPresent(" select all"));
      
      assertTrue(isElementPresent(By.id("aAdminToolsBackToCatroidweb")));
      assertTrue(isElementPresent(By.id("chkboxSelectAll")));
      assertTrue(isElementPresent(By.id("sendEmailSubmit")));
    } catch(AssertionError e) {
      captureScreen("SendEmailNotificationTests.checkElements");
      throw e;
    } catch(Exception e) {
      captureScreen("SendEmailNotificationTests.checkElements");
      throw e;
    }
  }
  
  @Test(groups = { "functionality", "sendEmail" }, description = "send e-mail notification")
  public void sendButton() throws Throwable {
    try {
      openAdminLocation();
      ajaxWait();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsSendEmailNotification")).click();
      ajaxWait();
      
      List<WebElement> checkboxes = driver().findElements(By.className("chkBoxEmail"));

      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("sendEmailSubmit")).click();
      assertFalse(isTextPresent("Answer:"));
      assertFalse(isTextPresent("Number of emails selected:"));
      
      driver().findElement(By.id("chkboxSelectAll")).click();
      
      clickCancelOnNextConfirmationBox();
      driver().findElement(By.id("sendEmailSubmit")).click();
      assertFalse(isTextPresent("Answer:"));
      assertFalse(isTextPresent("Number of emails selected:"));
      
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("sendEmailSubmit")).click();
      
      assertTrue(isTextPresent("Answer:"));
      assertTrue(isTextPresent("Number of emails selected: " + checkboxes.size()));
      assertFalse(isTextPresent("Sending message to user \"anonymous\""));
      assertTrue(isTextPresent("Sending message to user \"catroweb\""));
      assertTrue(isTextPresent("Status: " + checkboxes.size() + " of " + + checkboxes.size() + " e-mails sent. (0 failed)"));
    } catch(AssertionError e) {
      captureScreen("SendEmailNotificationTests.sendButton");
      throw e;
    } catch(Exception e) {
      captureScreen("SendEmailNotificationTests.sendButton");
      throw e;
    }
  }
  
  @Test(groups = { "functionality", "sendEmail" }, description = "select checkboxes one by one and by using select all checkbox")
  public void checkCheckboxes() throws Throwable {
    try {
      int checked = 0;
      openAdminLocation();
      ajaxWait();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsSendEmailNotification")).click();
      ajaxWait();
      
      assertTrue(isTextPresent(" (" + checked + " user(s) selected)"));
      
      List<WebElement> checkboxes = driver().findElements(By.className("chkBoxEmail"));
      for(WebElement checkbox : checkboxes) {
        assertFalse(checkbox.isSelected());
      }
      
      for(WebElement checkbox : checkboxes) {
        driver().findElement(By.id(checkbox.getAttribute("id"))).click();
        checked++;
        assertTrue(isTextPresent(" (" + checked + " user(s) selected)"));
        assertTrue(checkbox.isSelected());
      }
      
      assertTrue(isTextPresent("unselect all"));
      // unselect all checkboxes by clicking on unselect all checkbox
      checked = 0;
      driver().findElement(By.id("chkboxSelectAll")).click();
      ajaxWait();
      
      for(WebElement checkbox : checkboxes) {
        assertFalse(checkbox.isSelected());
      }
      assertTrue(isTextPresent("select all"));
      assertTrue(isTextPresent(" (" + checked + " user(s) selected)"));
      
      // select all checkboxes by clicking on select all checkbox
      driver().findElement(By.id("chkboxSelectAll")).click();
      for(WebElement checkbox : checkboxes) {
        checked ++;
        assertTrue(checkbox.isSelected());
      }
      assertTrue(isTextPresent("unselect all"));
      assertTrue(isTextPresent(" (" + checked + " user(s) selected)"));
      
    } catch(AssertionError e) {
      captureScreen("SendEmailNotificationTests.checkCheckboxes");
      throw e;
    } catch(Exception e) {
      captureScreen("SendEmailNotificationTests.checkCheckboxes");
      throw e;
    }
  }
  
}
