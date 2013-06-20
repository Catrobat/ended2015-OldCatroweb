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


import commands
import os
import re
import sys
from tools import CSSCompiler, JSCompiler, Selenium
from remoteShell import RemoteShell
import shutil
from sql import Sql
import tempfile
import zipfile


class EnvironmentChecker:
	basePath = os.getcwd()
	folders = [[os.path.join('addons', 'board', 'cache'), False],
			[os.path.join('addons', 'board', 'images', 'avatars', 'upload'), False],
			['cache', False], 
			[os.path.join('resources', 'catroid'), False],
			[os.path.join('resources', 'featured'), False],
			[os.path.join('resources', 'projects'), False],
			[os.path.join('resources', 'thumbnails'), False],
			[os.path.join('include', 'xml', 'lang'), True],
			[os.path.join('tests', 'phpunit', 'framework', 'testdata'), True]]
	thumbnailPath = os.path.join(basePath, 'resources', 'thumbnails')
	projectPath = os.path.join(basePath, 'resources', 'projects')


	def run(self):
		for folder in self.folders:
			path = os.path.join(self.basePath, folder[0])
			self.setPermission(path, folder[1])
		self.adaptThumbnails()


	def setPermission(self, path, recursive=False):
		if not os.path.isdir(path):
			print('creating directory %s' % path)
			os.makedirs(path)

		if(os.stat(path).st_mode & 0777) != 0777:
			print('setting permissions for %s' % path)
			os.chmod(path, 0777)

		if recursive:
			for r,d,f in os.walk(path):
				self.setPermission(r)
				for file in f:
					currentFile = os.path.join(r, file)
					if(os.stat(currentFile).st_mode & 0777) != 0777:
						print('setting permissions for %s' % currentFile)
						os.chmod(currentFile, 0777)


	def adaptThumbnails(self):
		print('adapting thumbnails:')
		for r,d,f in os.walk(self.thumbnailPath):
			for file in f:
				imagex = 0
				imagey = 0
				
				filePath = os.path.join(r, file)
				match = re.match(r".*?(?P<imagex>[0-9]+)x(?P<imagey>[0-9]+) ", commands.getoutput('identify %s' % filePath))
				try:
					imagex = int(match.groupdict()['imagex'])
					imagey = int(match.groupdict()['imagey'])
				except:
					pass

				if 'small' in file:
					if imagex != imagey:
						print('cropping %s' % file)
						os.system('convert %s -crop %dx%d+0+%d %s' % (filePath, imagex, imagex, ((imagey - imagex) / 2), filePath))
					if imagex != 160:
						print('resizing %s' % file)
						os.system('convert %s -resize 160x %s' % (filePath, filePath))

				if 'large' in file:
					if imagex != imagey:
						print('cropping %s' % file)
						os.system('convert %s -crop %dx%d+0+%d %s' % (filePath, imagex, imagex, ((imagey - imagex) / 2), filePath))
					if imagex != 480:
						print('resizing %s' % file)
						os.system('convert %s -resize 480x %s' % (filePath, filePath))


	def updateProjectXMLs(self):
		replaceStrings = [['elseBrick', 'ifElseBrick'],
						['beginBrick', 'ifBeginBrick']]

		for r,d,f in os.walk(self.projectPath):
			for project in f:
				file = os.path.join(r, project)
				tempdir = tempfile.mkdtemp()
				
				print(file)
				try:
					tempname = os.path.join(tempdir, 'new.zip')
					with zipfile.ZipFile(file, 'r') as zipRead:
						with zipfile.ZipFile(tempname, 'w') as zipWrite:
							for item in zipRead.infolist():
								data = zipRead.read(item.filename)
								if '.xml' in item.filename:
									for task in replaceStrings:
										if task[0] in data:
											print('  replace: ' + task[0])
											data = data.replace(task[0], task[1])
								zipWrite.writestr(item, data)
					shutil.move(tempname, file)
				finally:
					shutil.rmtree(tempdir)
				


class SetupBackup:
	basePath = os.getcwd()
	remoteDir = os.path.join('home', 'catback', 'backup')


	def init(self):
		#shell = RemoteShell('jenkinsmaster', 'catback', '', remoteDir=self.remoteDir)
		shell = RemoteShell('192.168.1.113', 'chris', '', remoteDir=self.remoteDir)
		try:
			shell.sftp.mkdir('backup')
		except:
			pass
		shell.sftp.put(os.path.join(self.basePath, 'services', 'backup.py'), os.path.join('backup', 'backup.py'))
		shell.sftp.put(os.path.join(self.basePath, 'services', 'remoteShell.py'), os.path.join('backup', 'remoteShell.py'))
		shell.sftp.put(os.path.join(self.basePath, 'services', 'sql.py'), os.path.join('backup', 'sql.py'))
		shell.sftp.put(os.path.join(self.basePath, 'services', 'init', 'backup', 'backup_daemon.sh'), os.path.join('backup', 'backup_daemon.sh'))
		shell.sftp.put(os.path.join(self.basePath, 'services', 'init', 'backup', 'backup_setup.sh'), os.path.join('backup', 'backup_setup.sh'))

		try:
			shell.sftp.mkdir(os.path.join('backup', 'sql'))
			shell.sftp.mkdir(os.path.join('backup', 'sql', 'catroboard'))
			shell.sftp.mkdir(os.path.join('backup', 'sql', 'catroweb'))
			shell.sftp.mkdir(os.path.join('backup', 'sql', 'catrowiki'))
		except:
			pass
				


if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'website':
			EnvironmentChecker().run()
			sql = Sql()
			sql.initDbs()
			sql.createDocs()
		elif sys.argv[1] == 'tools':
			Selenium().update()
			JSCompiler().update()
			CSSCompiler().update()
		elif sys.argv[1] == 'dev':
			print('Please enter your password to run this script:')
			os.system('sudo sh services/init/environment/local.sh')
		elif sys.argv[1] == 'xmlupdate':
			EnvironmentChecker().updateProjectXMLs()
		elif sys.argv[1] == 'backup':
			SetupBackup().init()
		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  website               Initializes or updates the database and checks if the')
		print('                        required folders exist and have the right permissions.')
		print('  tools                 Initializes or updates the required tools.')
		print('  dev                   Initializes development environment (server, tools).')
		print('  xmlupdate             Updates all project XMLs with the given replacement')
		print('                        rules in replaceStrings.')
		print('  backup                ......TODO')
