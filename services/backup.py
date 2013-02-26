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

import commands, os, sys
from datetime import date, datetime, timedelta
from remoteShell import RemoteShell
from sql import Sql


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Backup:
	today							= date.today().strftime("%Y%m%d")
	remoteDir					= ''
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self, remoteShell=None):
		try:
			self.run = remoteShell.run
			self.download = remoteShell.sftp.get
			self.upload = remoteShell.sftp.put
			self.remoteDir = remoteShell.remoteDir
			print 'remote backup:'
		except:
			print 'local backup:'

	#--------------------------------------------------------------------------------------------------------------------
	def run(self, command):
		return commands.getoutput(command)

	#--------------------------------------------------------------------------------------------------------------------
	def download(self, remoteFile, localFile):
		pass

	#--------------------------------------------------------------------------------------------------------------------
	def upload(self, localFile, remoteFile):
		pass

	#--------------------------------------------------------------------------------------------------------------------
	def createBackup(self):
		sqlShell = Sql(self.run)
		if sqlShell.checkConnection():
			sqlShell.backupDbs()
			self.run('tar -zcf resources.tar.gz resources')
			self.run('tar -cf catroweb-' + self.today + '.tar sql.tar resources.tar.gz')
			self.run('rm sql.tar resources.tar.gz')
			self.download(os.path.join(self.remoteDir, 'catroweb-' + self.today + '.tar'), os.path.join(os.getcwd(), 'catroweb-' + self.today + '.tar'))
			print 'created backup'

	#--------------------------------------------------------------------------------------------------------------------
	def restoreBackup(self, backup):
		tempResources = 'resources-tmp'
		if 'No such file' not in commands.getoutput('ls ' + os.path.join(os.getcwd(), backup)):
			sqlShell = Sql(self.run)
			if sqlShell.checkConnection():
				self.upload(os.path.join(os.getcwd(), backup), os.path.join(self.remoteDir, 'catroweb-' + self.today + '.tar'))

				result = self.run('tar -xf ' + backup)
				if 'Error' in result:
					print result
					sys.exit(-1)
					
				self.run('mkdir -p resources-tmp')
				result = self.run('tar -xf resources.tar.gz -C resources-tmp')
				if 'Error' in result:
					print result
					sys.exit(-1)
				
				sqlShell.restoreDbs('sql.tar')
				
				result = self.run('mv resources resources-old; mv resources-tmp/resources resources')
				result = self.run('rm -rf resources-old resources-tmp resources.tar.gz sql.tar')

				print 'restored backup: ' + backup
		else:
			print 'FATAL ERROR: No such file: ' + backup
			

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
## command handler
if __name__ == '__main__':
	if len(sys.argv) > 1:
		if sys.argv[1] == 'webtest':
			shell = RemoteShell('192.168.1.113', 'chris', '')
			Backup(shell).createBackup()
	#shell = RemoteShell('192.168.1.110', 'chris', '')
	#shell = RemoteShell('catroidwebtest.ist.tugraz.at', 'unpriv', '')
	#shell = RemoteShell('catroidtest.ist.tugraz.at', 'unpriv', '')
	#shell = RemoteShell('catroidweb.ist.tugraz.at', 'unpriv', '')
	#Deploy(shell).run()

	#Backup().restoreBackup('catroweb-20130116.tar')
