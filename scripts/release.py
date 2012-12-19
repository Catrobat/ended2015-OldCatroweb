#!/usr/bin/env python
'''   
 *    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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
'''

import fileinput, glob, os, shutil, sys
from datetime import date

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Release:
	basePath					= os.getcwd()
	today							= date.today().strftime("%Y%m%d")
	buildDir					= os.path.join(basePath, 'build')
	releaseDir				= os.path.join(buildDir, today)

	#--------------------------------------------------------------------------------------------------------------------	
	def copyFiles(self):
		
		## clean build destination if it exists
		if not os.path.isdir(self.buildDir):
			os.mkdir(self.buildDir)
		if os.path.isdir(self.releaseDir):
			shutil.rmtree(self.releaseDir)
		os.mkdir(self.releaseDir)
		
		## copy files
		shutil.copy(os.path.join(self.basePath, 'commonFunctions.php'), self.releaseDir)
		shutil.copy(os.path.join(self.basePath, 'config.php'), self.releaseDir)
		shutil.copy(os.path.join(self.basePath, 'ga.php'), self.releaseDir)
		shutil.copy(os.path.join(self.basePath, 'index.php'), self.releaseDir)
		shutil.copy(os.path.join(self.basePath, 'robots.txt'), self.releaseDir)
		shutil.copy(os.path.join(self.basePath, 'statusCodes.php'), self.releaseDir)
		shutil.copy(os.path.join(self.basePath, '.htaccess'), self.releaseDir)

		## copy dirs
		shutil.copytree(os.path.join(self.basePath, 'addons'), os.path.join(self.releaseDir, 'addons'))
		shutil.copytree(os.path.join(self.basePath, 'classes'), os.path.join(self.releaseDir, 'classes'))
		shutil.copytree(os.path.join(self.basePath, 'images'), os.path.join(self.releaseDir, 'images'))
		shutil.copytree(os.path.join(self.basePath, 'include'), os.path.join(self.releaseDir, 'include'))
		shutil.copytree(os.path.join(self.basePath, 'modules'), os.path.join(self.releaseDir, 'modules'))
		shutil.copytree(os.path.join(self.basePath, 'sql'), os.path.join(self.releaseDir, 'sql'))
		shutil.copytree(os.path.join(self.basePath, 'tools'), os.path.join(self.releaseDir, 'tools'))
		shutil.copytree(os.path.join(self.basePath, 'viewer'), os.path.join(self.releaseDir, 'viewer'))

	#--------------------------------------------------------------------------------------------------------------------	
	def create(self):
		self.copyFiles()
		

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	Release().create()
