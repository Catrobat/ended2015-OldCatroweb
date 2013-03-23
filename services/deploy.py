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
import sys
from datetime import date, datetime, timedelta
from release import Release
from remoteShell import RemoteShell
from sql import Sql


class Deploy:
	basePath = os.getcwd()
	today = date.today().strftime("%Y%m%d")
	buildDir = os.path.join(basePath, 'build')
	
	sftp = None
	remoteDir = ''
	

	def __init__(self, remoteShell):
		try:
			if not isinstance(remoteShell, RemoteShell):
				raise Exception("This is not a valid remote shell object.")
			self.remoteCommand = remoteShell.run
			self.sftp = remoteShell.sftp
			self.remoteDir = remoteShell.remoteDir
		except Exception, e:
			print('FATAL ERROR: unrecognized shell, please use a RemoteShell object.')
			print('Exception: %s' % e)
			sys.exit(-1)
			
		print('Do you want to continue [y/N]?')
		if sys.stdin.readline() != 'y\n':
			sys.exit(-1)


	def remoteCommand(self, command):
		print('FATAL ERROR: shell not set.')
		sys.exit(-1)


	def formatSize(self, number):
		for unit in ['B  ', 'KiB', 'MiB', 'GiB']:
			if number < 1024.0:
				return "%6.1f %s" % (number, unit)
			number /= 1024.0
		return "%6.1f %s" % (number, 'TiB')


	def getSize(self, path = '.'):
		totalSize = 0
		for file in os.listdir(path):
			fileIterator = os.path.join(path, file)
			if os.path.isfile(fileIterator):
				totalSize += os.path.getsize(fileIterator)
		return totalSize


	def checkSetup(self):
		if 'No such file or directory' in self.remoteCommand('ls -al %s' % os.path.join(self.remoteDir, '.setup')):
			self.sftp.put(os.path.join(self.basePath, 'services', 'init', 'environment', 'webserver.sh'), 'setup.sh')
			self.sftp.put(os.path.join(self.basePath, 'services', 'init', 'environment', 'setup-db.sh'), 'setup-db.sh')
			self.sftp.put(os.path.join(self.basePath, 'services', 'init', 'environment', 'VirtualHost.conf'), 'VirtualHost.conf')
			print('This host is not prepared to run catroweb.')
			print('To setup please ssh into your server and run: su -c "sh setup.sh"')
			sys.exit(-1)

	#--------------------------------------------------------------------------------------------------------------------
	def upload(self, localPath, remotePath):
		totalSize = 0
		startTime = datetime.now()
		
		os.chdir(os.path.dirname(localPath))
		for (path, dirs, files) in os.walk(os.path.basename(localPath)):
			directorySize = self.getSize(path)
			totalSize += directorySize
			print('%s - - %s' % (self.formatSize(directorySize), os.path.join(remotePath, path)))

			try:
				self.sftp.mkdir(os.path.join(remotePath, path))
			except:
				pass
			for file in files:
				self.sftp.put(os.path.join(path, file), os.path.join(remotePath, path, file))
		
		print('copied %s in %s seconds.' % (self.formatSize(totalSize), str((datetime.now() - startTime).seconds)))
		print('')


	def moveFilesIntoPlace(self, localPath, release):
		os.chdir(os.path.dirname(localPath))
		for entry in os.listdir(os.path.basename(localPath)):
			self.remoteCommand('rm -rf %s' % entry)

		self.remoteCommand('mv %s/* .; mv %s/.htaccess .' % (release, release))
		self.remoteCommand('rm -rf %s' % release)

		self.remoteCommand('mkdir -m 0777 -p cache')
		self.remoteCommand('mkdir -m 0777 -p resources/catroid')
		self.remoteCommand('mkdir -m 0777 -p resources/projects')
		self.remoteCommand('mkdir -m 0777 -p resources/qrcodes')
		self.remoteCommand('mkdir -m 0777 -p resources/thumbnails')
		

	def run(self, release=today):
		self.checkSetup()
		
		if not os.path.isdir(os.path.join(self.buildDir, release)):
			if not os.path.isdir(os.path.join(self.buildDir, self.today)):
				Release().create()
		
		sqlShell = Sql(self.remoteCommand)
		if sqlShell.checkConnection():
			self.upload(os.path.join(self.buildDir, release), self.remoteDir)
			self.moveFilesIntoPlace(os.path.join(self.buildDir, release), release)
			sqlShell.purgeDbs()
			sqlShell.initDbs()
		else:
			print('ERROR: deployment failed!')



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'webtest':
			Deploy(RemoteShell('catroidwebtest.ist.tugraz.at', 'unpriv', '')).run()
		elif sys.argv[1] == 'catroidtest':
			Deploy(RemoteShell('catroidtest.ist.tugraz.at', 'unpriv', '')).run()
		elif sys.argv[1] == 'public':
			Deploy(RemoteShell('catroidweb.ist.tugraz.at', 'unpriv', '')).run()
		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  webtest               Deploys a new version to catroidwebtest.ist.tugraz.at.')
		print('  catroidtest           Deploys a new version to catroidtest.ist.tugraz.at.')
		print('  public                Deploys a new version to catroidweb.ist.tugraz.at.')
