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

package at.tugraz.ist.catroweb.api;

import static org.testng.AssertJUnit.*;
import org.testng.annotations.Test;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class UploadTests extends BaseTest {
  @Test(groups = { "upload", "firefox", "default" }, description = "upload valid projects")
  public void uploadValidProjects() {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    projectUploader.upload(DataProvider.getUploadPayload("testing project upload", "some description for my test project.", "test.zip",
        "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0"));
  }

  @Test(groups = { "upload", "firefox", "default" }, description = "upload invalid projects")
  public void uploadInvalidProjects() {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    ajaxWait();
    projectUploader.upload(DataProvider.getUploadPayload("insulting word in description", "fuck the project!!!!", "test.zip",
        "72ed87fbd5119885009522f08b7ee79f", "", "", "", "0"));
  }
}
