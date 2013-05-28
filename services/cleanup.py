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


import glob
import os
import shutil
import sys
from pootle import Pootle
from release import Release
from sql import Sql
from tools import CSSCompiler, JSCompiler, Selenium


class Cleaner:
	basePath = os.getcwd()
	resourceDir = os.path.join(basePath, 'resources')
	cacheDir = os.path.join(basePath, 'cache')
	sqlOverviewDir = os.path.join(basePath, 'sql', 'overview')


	def cleanResources(self):
		for entry in glob.glob(os.path.join(self.resourceDir, 'catroid', '*')):
			if os.path.isdir(entry):
				shutil.rmtree(entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'projects', '*')):
			if not 'projects' in entry and not '/1.' in entry and not '/2.' in entry:
				os.remove(entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'thumbnails', '*.png')):
			if not '/1_' in entry and not '/2_' in entry and not '/thumbnail_' in entry:
				os.remove(entry)


	def clearCache(self):
		for js in glob.glob(os.path.join(self.cacheDir, "*.js")):
			os.remove(js)
		for css in glob.glob(os.path.join(self.cacheDir, "*.css")):
			os.remove(css)


	def removeSQLoverview(self):
		if os.path.isdir(self.sqlOverviewDir):
			shutil.rmtree(self.sqlOverviewDir)



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'website':
			clean = Cleaner()
			clean.clearCache()
			clean.cleanResources()
			Release().removeBuildDir()
			Sql().purgeDbs()
			clean.removeSQLoverview()
			Pootle().cleanGeneratedFiles()
		elif sys.argv[1] == 'tools':
			Selenium().removeSeleniumLibs()
			JSCompiler().removeCompiler()
			CSSCompiler().removeCompiler()
		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  website               Removes website related resources. (cache, database ...)')
		print('  tools                 Removes the website tools. (Selenium, compilers)')
