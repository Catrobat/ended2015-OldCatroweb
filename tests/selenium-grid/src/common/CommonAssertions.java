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

import org.apache.tools.ant.taskdefs.Definer;

/**
 */
public class CommonAssertions  {

	private static CommonAssertions instance = new CommonAssertions();

	private CommonAssertions(){
		
	}
	
	public static CommonAssertions getInstance() {
        return instance;
    }
	
	/** Description of isIndexLocation(String location)
	 * 
	 * @param location	asserts that the location matches [host]/catroid/index/[pagenumber]
	 * @return			true if matches
	 */
	public boolean isIndexLocation(String location)
	{	
		if (location.matches(".*/catroid/index/[0-9]+"))
			return true;
		else
			return false;
	}	
	
	/** Description of isDetailsLocation(String location)
	 * 
	 * @param location	asserts that the location matches [host]/catroid/details/[id]
	 * @return			true if matches
	 */
	public boolean isDetailsLocation(String location)
	{
		if (location.matches(".*/catroid/details/[0-9]+"))
			return true;
		else
			return false;
	}
	


}
