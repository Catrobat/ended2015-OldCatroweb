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

package at.tugraz.ist.catroweb.common;

import static org.testng.AssertJUnit.assertTrue;


/**
 */
public class CommonAssertions  {
  public static void assertRegExp(String pattern, String string) {	
    assertTrue(string.matches(pattern));
  }	

  /** Description of isIndexLocation(String location)
   * 
   * @param location  asserts that the location matches [host]/catroid/index/[pagenumber]
   * @return      true if matches
   */
  public static boolean isIndexLocation(String location)
  { 
    // TODO get base path
    if (location.matches("^http://localhost(/)?"))
      return true;
    else if (location.matches(".*/catroid/index(/[0-9]+)?"))
      return true;
    else
      return false;
  } 
  
  /** Description of isDetailsLocation(String location)
   * 
   * @param location  asserts that the location matches [host]/catroid/details/[id]
   * @return      true if matches
   */
  public static boolean isDetailsLocation(String location)
  {
    if (location.matches(".*/catroid/details/[0-9]+"))
      return true;
    else
      return false;
  }
  
  /** Description of isMenuLocation(String location)
   * 
   * @param location  asserts that the location matches [host]/catroid/menu/
   * @return      true if matches
   */
  public static boolean isMenuLocation(String location)
  { 
    // TODO  /XXXX
    if (location.matches(".*/catroid/menu$"))
      return true;
    else
      return false;
  }
  
  /** Description of isBoardLocation(String location)
   * 
   * @param location  asserts that the location matches [host]/addons/board/
   * @return      true if matches
   */
  public static boolean isBoardLocation(String location)
  { 
    if (location.matches(".*/addons/board(/)?$"))
      return true;
    else
      return false;
  }
  
  /** Description of isWikiLocation(String location)
   * 
   * @param location  asserts that the location matches [host]/wiki/Main_Page/
   * @return      true if matches
   */
  public static boolean isWikiLocation(String location)
  { 
    if (location.matches(".*/wiki/Main_Page$"))
      return true;
    else if (location.matches(".*/wiki/Main_Page[?]action=purge$"))
      return true;
    else
      return false;
  }

  /** Description of isLoginLocation(String location)
   * 
   * @param location  asserts that the location matches [host]/catroid/login 
   * @return      true if matches
   */
  public static boolean isLoginLocation(String location)
  { 
    if (location.matches(".*/catroid/login"))
      return true;
    else if (location.matches(".*/catroid/login[?]requesturi=catroid/menu$"))
      return true;
    else
      return false;
  }

}
