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
import sys
from datetime import date, datetime, timedelta
from remoteShell import RemoteShell
from sql import Sql


class Backup:
	today = date.today().strftime("%Y%m%d")
	remoteDir = ''
	

	def __init__(self, remoteShell=None):
		try:
			self.run = remoteShell.run
			self.download = remoteShell.sftp.get
			self.upload = remoteShell.sftp.put
			self.remoteDir = remoteShell.remoteDir
			print 'remote backup:'
		except:
			print 'local backup:'


	def run(self, command):
		return commands.getoutput(command)


	def download(self, remoteFile, localFile):
		pass


	def upload(self, localFile, remoteFile):
		pass


	def createBackup(self):
		sqlShell = Sql(self.run)
		if sqlShell.checkConnection():
			sqlShell.backupDbs()
			self.run('tar -c resources.tar --no-recursion resources/*')
			for project in sqlShell.getProjectList():
				self.run('tar --append --file=resources.tar resources/projects/%s.*' % project)
				self.run('tar --append --file=resources.tar resources/thumbnails/%s_small*' % project)
				self.run('tar --append --file=resources.tar resources/thumbnails/%s_large*' % project)

			for featuredProject in sqlShell.getFeaturedProjectList():
				self.run('tar --append --file=resources.tar resources/featured/%s.*' % featuredProject)
			
			self.run('tar --append --file=resources.tar resources/thumbnails/thumbnail_small.png')
			self.run('tar --append --file=resources.tar resources/thumbnails/thumbnail_large.png')
			
			self.run('tar -zcf catroweb-' + self.today + '.tar.gz sql.tar resources.tar')
			self.run('rm sql.tar resources.tar')
			self.download(os.path.join(self.remoteDir, 'catroweb-' + self.today + '.tar.gz'), os.path.join(os.getcwd(), 'catroweb-' + self.today + '.tar.gz'))
			print 'created backup'


	def restoreBackup(self, backup):
		tempResources = 'resources-tmp'
		if 'No such file' not in commands.getoutput('ls ' + os.path.join(os.getcwd(), backup)):
			sqlShell = Sql(self.run)
			if sqlShell.checkConnection():
				self.upload(os.path.join(os.getcwd(), backup), os.path.join(self.remoteDir, backup))

				result = self.run('tar -xf ' + backup)
				if 'Error' in result:
					print result
					sys.exit(-1)
					
				sqlShell.restoreDbs('sql.tar')
				self.run('rm -rf sql.tar')
				
				self.run('mkdir -p resources-tmp')
				result = self.run('tar -xf resources.tar -C resources-tmp')
				if 'Error' in result:
					print result
					sys.exit(-1)
				
				
				self.run('mv resources resources-old; mv resources-tmp/resources resources')
				self.run('rm -rf resources-old resources-tmp resources.tar')
				self.run('mkdir -p resources/catroid; for file in resources/projects/*; do filename=`basename "$file"`; unzip -d "resources/catroid/${filename%.*}" "$file"; done')
				self.run('chmod -R 0777 resources')

				print 'restored backup: ' + backup
		else:
			print 'FATAL ERROR: No such file: ' + backup



if __name__ == '__main__':
	parameter = 'empty'
	try:
		if sys.argv[1] == 'local':
			if sys.argv[2] == 'backup':
				Backup().createBackup()
			elif sys.argv[2] == 'restore':
				Backup().restoreBackup(sys.argv[3])

		elif sys.argv[1] == 'webtest':
			shell = RemoteShell('catroidwebtest.ist.tugraz.at', 'unpriv', '')
			if sys.argv[2] == 'backup':
				Backup(shell).createBackup()
			elif sys.argv[2] == 'restore':
				Backup(shell).restoreBackup(sys.argv[3])

		elif sys.argv[1] == 'catroidtest':
			shell = RemoteShell('catroidtest.ist.tugraz.at', 'unpriv', '')
			if sys.argv[2] == 'backup':
				Backup(shell).createBackup()
			elif sys.argv[2] == 'restore':
				Backup(shell).restoreBackup(sys.argv[3])

		elif sys.argv[1] == 'public':
			shell = RemoteShell('catroidpublic.ist.tugraz.at', 'unpriv', '')
			if sys.argv[2] == 'backup':
				Backup(shell).createBackup()
			elif sys.argv[2] == 'restore':
				Backup(shell).restoreBackup(sys.argv[3])

		else:
			parameter = '%s:' % sys.argv[1]
			raise IndexError()
	except IndexError:
		print('%s parameter not supported' % parameter)
		print('')
		print('Options:')
		print('  local backup          Creates a backup of the local website.')
		print('  local restore <BACKUP>')
		print('                        Restores the backup to the local website.')
		print('  webtest backup        Creates a backup of the catroidwebtest website.')
		print('  webtest restore <BACKUP>')
		print('                        Restores the backup to the catroidwebtest website.')
		print('  catroidtest backup    Creates a backup of the catroidtest website.')
		print('  catroidtest restore <BACKUP>')
		print('                        Restores the backup to the catroidtest website.')
		print('  public backup         Creates a backup of the public website.')
		print('  public restore <BACKUP>')
		print('                        Restores the backup to the public website.')
