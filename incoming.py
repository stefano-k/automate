#!/usr/bin/env python

# copyright 2011-2012 Stefano Karapetsas <stefano@karapetsas.com>

import functions
import glob
import os
import pwd
import sys
import time

from configobj import ConfigObj
from debian import deb822

class Incoming:
    
    def __init__(self, config):
        
        self.config = config
    
    def run_queue(self):

        # check for *.changes in incoming directory
        for changes_file in glob.glob(os.path.join(self.config['incoming_path'], "*.changes")):
            
            print "I: found %s" % changes_file
            
            # check gpg key
            gpg_res = functions.command_result("gpgv %(changes)s" % {"changes": changes_file})
            
            if gpg_res == 0:
                
                # load .changes file
                deb_changes = deb822.Changes(file(changes_file))
            
                # check sha1 checksum
                sha1_checksum = True
                source_files = deb_changes['Checksums-Sha1']
                for source_file in source_files:
                    source_file_path = os.path.join(self.config['incoming_path'], source_file['name'])
                    
                    if source_file['size'] != str(os.stat(source_file_path).st_size):
                        print "E: %s has not a valid size!" % source_file_path
                        sha1_checksum = False
                    elif source_file['sha1'] != functions.sha1file(source_file_path):
                        print "E: %s has not a valid SHA1 checksum!" % source_file_path
                        sha1_checksum = False
                    
                if sha1_checksum:
                    
                    # calculate the next build id
                    build_id = str(len(glob.glob(os.path.join(self.config['builds_path'], "*"))) + 1)
                    
                    # build paths
                    build_dir = os.path.join(self.config['builds_path'], build_id)
                    build_dir_source = os.path.join(build_dir, "source")
                    
                    # move files to new source directory
                    os.makedirs(build_dir_source)
                    os.system("chmod 777 %s" % build_dir)
                    os.rename(changes_file, os.path.join(build_dir_source, os.path.basename(changes_file)))
                    for source_file in source_files:
                        source_file_path = os.path.join(self.config['incoming_path'], source_file['name'])
                        os.rename(source_file_path, os.path.join(build_dir_source, os.path.basename(source_file['name'])))
                    
                    # save package info
                    package_info = {}
                    package_info['build_id'] = build_id
                    package_info['timestamp'] = time.strftime("%Y/%m/%d %H:%M:%S")
                    package_info['package'] = deb_changes['Source']
                    package_info['version'] = deb_changes['Version']
                    package_info['maintainer'] = deb_changes['Maintainer']
                    package_info['changed_by'] = deb_changes['Changed-By']
                    package_info['source_dir'] = build_dir_source
                    package_info['dists'] = self.config['dists']
                    package_info['archs'] = self.config['archs']
                    package_info_filename = os.path.join(build_dir, "build.json")
                    functions.json_save(package_info, package_info_filename)
                    
                    # prepare single build requests
                    for dist in self.config['dists']:
                        for arch in self.config['archs']:
                            
                            queue = {}
                            queue['build_id'] = build_id
                            queue['package'] = deb_changes['Source']
                            queue['version'] = deb_changes['Version']
                            queue['maintainer'] = deb_changes['Maintainer']
                            queue['changed_by'] = deb_changes['Changed-By']
                            queue['source_dir'] = build_dir_source
                            queue['dist'] = dist
                            queue['arch'] = arch
                            
                            queue_filename = os.path.join(self.config['queue_path'], \
                                "%(id)s_%(package)s_%(version)s_%(dist)s_%(arch)s.json" % \
                                {
                                    "id": build_id,
                                    "package": queue['package'],
                                    "version": queue['version'],
                                    "dist": dist,
                                    "arch": arch,
                                })
                            
                            functions.json_save(queue, queue_filename)
                    
                    sendmail = os.popen("sendmail -t", "w")
                    sendmail.write("From: %s\n" % "MATE Build Daemon <mate@karapetsas.com>")
                    sendmail.write("To: %s\n" % deb_changes['Changed-By'])
                    sendmail.write("Subject: %s ACCEPTED into AutoMate\n" % os.path.basename(changes_file))
                    sendmail.write("\n")
                    sendmail.write("Accepted:\n")
                    for source_file in source_files:
                        sendmail.write("%s\n" % source_file['name'])
                    sendmail.write("\n")
                    sendmail.write("%s\n" % deb_changes['Description'])
                    sendmail.write("%s\n" % deb_changes['Changes'])
                    sendmail.write("\n")
                    sendmail.write("Thank you for your contribution to MATE.\n")
                    sendmail_result = sendmail.close()
                                
            else:
                
                print "E: %s has not a valid GPG signature!" % os.path.basename(changes_file)
