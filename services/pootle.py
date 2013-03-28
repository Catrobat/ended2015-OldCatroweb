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


import os
import shutil
import sys
from tests import PhpUnit


class Pootle:
	basePath = os.getcwd()
	pootleScriptPath = os.path.join(basePath, 'pootle')
	languageFilePath = os.path.join(pootleScriptPath, 'en')


	def __init__(self):
		if not os.path.isdir(self.pootleScriptPath):
			print('** ERROR ***********************************************************************')
			print('Pootle folder is missing. The Pootle scripts should be located in:')
			print(' %s' % self.pootleScriptPath)
			sys.exit(-1)


	def cleanGeneratedFiles(self):
		print('removing Pootle language files')
		if os.path.isdir(self.languageFilePath):
			shutil.rmtree(self.languageFilePath)


	def generate(self):
		PhpUnit().run('framework/languageTest.php')
		os.system('cd %s; php generateStringsXml.php' % self.pootleScriptPath)
		os.system('cd %s; php generatePootleFile.php' % self.pootleScriptPath)
		print('generated Pootle language files')



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'generate':
			Pootle().generate()
		elif sys.argv[1] == 'clean':
			Pootle().cleanGeneratedFiles()
		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  generate              Generates a Pootle language file.')
		print('  clean                 Removes generated language files.')
