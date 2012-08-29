#!/usr/bin/env python

import fileinput, glob, os, shutil, sys

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Cleaner:
	basePath					= os.getcwd()
	buildDir					= os.path.join(basePath, 'build')
	resourceDir				= os.path.join(basePath, 'resources')
	seleniumDir				= os.path.join(basePath, 'tests', 'selenium-grid')
	seleniumLibDir		= os.path.join(seleniumDir, 'lib')
	seleniumToolsDir	= os.path.join(seleniumDir, 'tools')

	#--------------------------------------------------------------------------------------------------------------------	
	def restartApache(self):
		print 'Please enter your password, it is necessary to restart apache:'
		os.system('sudo service apache2 restart')

	#--------------------------------------------------------------------------------------------------------------------	
	def dropDatabases(self):
		os.system('sudo -u postgres psql -d template1 -c "DROP DATABASE IF EXISTS catroboard"')
		os.system('sudo -u postgres psql -d template1 -c "DROP DATABASE IF EXISTS catroweb"')
		os.system('sudo -u postgres psql -d template1 -c "DROP DATABASE IF EXISTS catrowiki"')

	#--------------------------------------------------------------------------------------------------------------------	
	def cleanResources(self):
		for entry in glob.glob(os.path.join(self.resourceDir, 'catroid', '*')):
			if os.path.isdir(entry):
				os.system('sudo rm -rf ' + entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'projects', '*.catrobat')):
			if not '/2.' in entry:
				os.remove(entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'qrcodes', '*.png')):
			if not '/1_' in entry and not '/2_' in entry:
				os.remove(entry)

		for entry in glob.glob(os.path.join(self.resourceDir, 'thumbnails', '*.png')):
			if not '/1_' in entry and not '/2_' in entry and not '/thumbnail_' in entry:
				os.remove(entry)

	#--------------------------------------------------------------------------------------------------------------------
	def removeSeleniumLibs(self):
		if os.path.isdir(self.seleniumLibDir):
			shutil.rmtree(self.seleniumLibDir)
		for jar in glob.glob(os.path.join(self.seleniumToolsDir, "*.jar")):
			os.remove(jar)
		
	#--------------------------------------------------------------------------------------------------------------------
	def cleanDatabaseAndResources(self):
		if os.path.isdir(self.buildDir):
			shutil.rmtree(self.buildDir)

		self.restartApache()
		self.dropDatabases()
		self.cleanResources()

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	if len(sys.argv) > 1:
		if sys.argv[1] == 'website':
			Cleaner().cleanDatabaseAndResources()
        else:
            print "no argument given. did you mean 'website'?"
