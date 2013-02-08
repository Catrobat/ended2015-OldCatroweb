#!/usr/bin/env python
'''   
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
'''

import fileinput, glob, os, shutil, sys

## TODO add init script (should check if jquery exists. 

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Release:
	basePath					= os.getcwd()
	buildDir					= os.path.join(basePath, 'build')
	resourceDir				= os.path.join(basePath, 'resources')

	#--------------------------------------------------------------------------------------------------------------------	
	def copyFiles(self):
		## clean build destination if it exists
		if os.path.isdir(self.buildDir):
			shutil.rmtree(self.buildDir)
		os.mkdir(self.buildDir)
		
		## copy files
		shutil.copy(os.path.join(self.basePath, 'commonFunctions.php'), self.buildDir)
		shutil.copy(os.path.join(self.basePath, 'config.php'), self.buildDir)
		shutil.copy(os.path.join(self.basePath, 'index.php'), self.buildDir)
		shutil.copy(os.path.join(self.basePath, 'passwords.php'), self.buildDir)
		shutil.copy(os.path.join(self.basePath, 'robots.txt'), self.buildDir)
		shutil.copy(os.path.join(self.basePath, 'statusCodes.php'), self.buildDir)
		shutil.copy(os.path.join(self.basePath, '.htaccess'), self.buildDir)

		## copy dirs
		shutil.copytree(os.path.join(self.basePath, 'addons'), os.path.join(self.buildDir, 'addons'))
		shutil.copytree(os.path.join(self.basePath, 'classes'), os.path.join(self.buildDir, 'classes'))
		shutil.copytree(os.path.join(self.basePath, 'images'), os.path.join(self.buildDir, 'images'))
		shutil.copytree(os.path.join(self.basePath, 'include'), os.path.join(self.buildDir, 'include'))
		shutil.copytree(os.path.join(self.basePath, 'modules'), os.path.join(self.buildDir, 'modules'))
		shutil.copytree(os.path.join(self.basePath, 'sql'), os.path.join(self.buildDir, 'sql'))
		shutil.copytree(os.path.join(self.basePath, 'viewer'), os.path.join(self.buildDir, 'viewer'))
		

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	Release().copyFiles()
