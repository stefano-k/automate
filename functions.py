# copyright 2011-2012 Stefano Karapetsas <stefano@karapetsas.com>

# This file is part of AutoMate.
#
# AutoMate is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# AutoMate is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with AutoMate.  If not, see <http://www.gnu.org/licenses/>.

import hashlib
import json
import os
import pwd
import subprocess
import sys

def debug_message(debug_active, debug_message):
    if debug_active:
        print debug_message

# check if user is root, exit if normal user
def check_root():
    usrinfo = pwd.getpwuid(os.getuid())
    if not usrinfo.pw_name == "root":
        print "E: root privileges required!"
        sys.exit(1)

def command_result(command_string, output=True):
    command = command_string.split(" ")
    if output:
        p = subprocess.Popen(command, shell=False)
    else:
        DEVNULL = open('/dev/null', 'w')
        p = subprocess.Popen(command, shell=False, stdout=DEVNULL, stderr=DEVNULL)
    while p.returncode is None:
        p.poll()
    return p.returncode

def json_load(json_file):
    return json.load(open(json_file, "r"))

def json_save(json_object, json_file):
    json_fd = open(json_file, "w")
    json_fd.write(json.dumps(json_object, indent=4))
    json_fd.close()

def distro_name(distro_codename):
    if distro_codename in ["jessie", "wheezy"]:
        return "debian"
    elif distro_codename in ["trusty", "saucy", "raring", "quantal", "precise", "oneiric", "natty", "maverick", "lucid", "karmic", "jaunty", "hardy"]:
        return "ubuntu"
    else:
        print "E: unknown distro name (%s)!" % distro_codename
        sys.exit(1)

def sha1file(filepath):
    if os.path.exists(filepath):
        return hashlib.sha1(open(filepath, "rb").read()).hexdigest()
    else:
        return ""
