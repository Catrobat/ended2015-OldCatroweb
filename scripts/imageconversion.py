#!/usr/bin/python
import os, glob
from PIL import Image
import sys
import subprocess


try:

	if(sys.argv[1] == ""):
		raise IndexError
		
	stream = subprocess.Popen('du -h '+sys.argv[1], stdout=subprocess.PIPE, stderr=subprocess.PIPE, shell=True)
	error = stream.stderr.read()
	if len(error) > 0:
		print(''.join(error))
		sys.exit()
		
	old_size = stream.stdout.read()
	stream.stdout.close()
	stream.stderr.close()

	images = glob.glob(sys.argv[1]+"*.png")
	if len(images) == 0:
		print('no .png-images found...')
		sys.exit()
		
	decision = raw_input( '\r\n\r\nCAUTION!\r\nThis will convert {} png-files in {} to jpg, then DELETE the png.\r\nAre you sure?[y/n]:'.format(len(images),sys.argv[1]))

	if(decision == 'y'):

		print ('0/{} converted\r'.format(len(images))),
		i = 0
		for infile in images:
				f, e = os.path.splitext(infile)
				#print p
				outfile = f + ".jpg"
				if infile != outfile:
				    im = Image.open(infile)
				    im.convert('RGB').save(outfile,'JPEG')
				    subprocess.Popen('rm -f '+infile, shell=True)
				    i+=1
				    print ('{}/{} converted\r'.format(i,len(images))),
		print
		print 'old size was: {}'.format(old_size)
		print 'new size is: {}'.format(subprocess.Popen('du -h '+sys.argv[1], stdout=subprocess.PIPE, shell=True).stdout.read())
		print 'done'
		
	else:
		print 'Aborted'
		sys.exit()
        
except IndexError:
		print('Options:')
		print('  path/to/png/files          replaces png with jpg-files.')
