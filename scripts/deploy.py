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

import commands, fileinput, glob, os, shutil, sys, paramiko
from datetime import date, datetime, timedelta
from release import Release
from sql import Sql

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class RemoteShell:
	ssh								= None
	sftp							= None
	remoteDir					= ''
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self, server, user, password, port=22, remoteDir='/var/www/catroid'):
		try:
			self.remoteDir = remoteDir
			self.ssh = paramiko.SSHClient()
			self.ssh.load_system_host_keys()
			self.ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
			self.ssh.connect(server, port, user, password)
			self.sftp = paramiko.SFTPClient.from_transport(self.ssh.get_transport())
		except Exception, e:
			print "ERROR: Couldn't connect to " + user + "@" + server + " on port " + str(port) + "."
			print 'Exception: %s' % e
			sys.exit(-1)

	#--------------------------------------------------------------------------------------------------------------------
	def run(self, command):
		(stdin, stdout, stderr) = self.ssh.exec_command('cd ' + self.remoteDir + '; ' + command)
		error = stderr.readlines()
		if len(error) > 0:
			print '** Output **********************************************************************'
			print ''.join(error)
			return ''.join(stdout.readlines() + error).strip()
		return ''.join(stdout.readlines()).strip()


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
class Deploy:
	basePath					= os.getcwd()
	today							= date.today().strftime("%Y%m%d")
	buildDir					= os.path.join(basePath, 'build')
	
	sftp							= None
	remoteDir					= ''
	
	#--------------------------------------------------------------------------------------------------------------------
	def __init__(self, remoteShell):
		try:
			self.remoteCommand = remoteShell.run
			self.sftp = remoteShell.sftp
			self.remoteDir = remoteShell.remoteDir
		except Exception, e:
			print 'FATAL ERROR: unrecognized shell, please use a RemoteShell object.'
			print 'Exception: %s' % e
			sys.exit(-1)

	#--------------------------------------------------------------------------------------------------------------------
	def remoteCommand(self, command):
		print 'FATAL ERROR: shell not set.'
		sys.exit(-1)

	#--------------------------------------------------------------------------------------------------------------------
	def formatSize(self, number):
		for unit in ['B  ', 'KiB', 'MiB', 'GiB']:
			if number < 1024.0:
				return "%6.1f %s" % (number, unit)
			number /= 1024.0
		return "%6.1f %s" % (number, 'TiB')

	#--------------------------------------------------------------------------------------------------------------------
	def getSize(self, path = '.'):
		totalSize = 0
		for file in os.listdir(path):
			fileIterator = os.path.join(path, file)
			if os.path.isfile(fileIterator):
				totalSize += os.path.getsize(fileIterator)
		return totalSize

	#--------------------------------------------------------------------------------------------------------------------
	def upload(self, localPath, remotePath):
		totalSize = 0
		startTime = datetime.now()
		
		os.chdir(os.path.dirname(localPath))
		for (path, dirs, files) in os.walk(os.path.basename(localPath)):
			directorySize = self.getSize(path)
			totalSize += directorySize
			print self.formatSize(directorySize) + ' - - ' + os.path.join(remotePath, path)

			try:
				self.sftp.mkdir(os.path.join(remotePath, path))
			except:
				pass
			for file in files:
				self.sftp.put(os.path.join(path, file), os.path.join(remotePath, path, file))
		
		print 'copied ' +  self.formatSize(totalSize) + ' in ' + str((datetime.now() - startTime).seconds) + ' seconds.'
		print ''

	#--------------------------------------------------------------------------------------------------------------------
	def moveFilesIntoPlace(self, localPath, release):
		os.chdir(os.path.dirname(localPath))
		for entry in os.listdir(os.path.basename(localPath)):
			self.remoteCommand('rm -rf ' + entry)

		self.remoteCommand('mv ' + release + '/* .; mv ' + release + '/.htaccess .')
		self.remoteCommand('rm -rf ' + release)
		
		if 'No such file' in str(self.remoteCommand('ls passwords.php')):
			print 'Warning: passwords.php is missing, please add one.'

		self.remoteCommand('mkdir -m 0777 -p cache')
		self.remoteCommand('mkdir -m 0777 -p resources/catroid')
		self.remoteCommand('mkdir -m 0777 -p resources/projects')
		self.remoteCommand('mkdir -m 0777 -p resources/qrcodes')
		self.remoteCommand('mkdir -m 0777 -p resources/thumbnails')
		
	#--------------------------------------------------------------------------------------------------------------------
	def run(self, release=today):
		if not os.path.isdir(os.path.join(self.buildDir, release)):
			if not os.path.isdir(os.path.join(self.buildDir, self.today)):
				Release().create()
		
		sqlShell = Sql(self.remoteCommand)
		if sqlShell.checkConnection():
			self.upload(os.path.join(self.buildDir, release), self.remoteDir)
			self.moveFilesIntoPlace(os.path.join(self.buildDir, release), release)
			#sqlShell.purgeDbs()
			#sqlShell.initDbs()
			#sqlShell.dumpDb('catroweb')
			#sqlShell.restoreDb('catroweb')
			#sqlShell.backupDbs()
			sqlShell.restoreDbs('sql-20130115.tar')
		else:
			print 'ERROR: deployment failed!'


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
	#shell = RemoteShell('192.168.1.110', 'chris', '')
	shell = RemoteShell('catroidwebtest.ist.tugraz.at', 'unpriv', '')
	#shell = RemoteShell('catroidtest.ist.tugraz.at', 'unpriv', '')
	#shell = RemoteShell('catroidweb.ist.tugraz.at', 'unpriv', '')
	#Deploy(shell).run()

	#Backup(shell).createBackup()
	Backup().restoreBackup('catroweb-20130116.tar')
