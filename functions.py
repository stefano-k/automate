import hashlib
import json
import os
import subprocess

def debug_message(debug_active, debug_message):
    if debug_active:
        print debug_message

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
    if distro_codename in ["wheezy", "squeeze"]:
        return "debian"
    elif distro_codename in ["precise", "oneiric", "natty", "maverick", "lucid", "karmic", "jaunty", "hardy"]:
        return "ubuntu"
    else:
        print "E: unknown distro name (%s)!" % distro_codename
        sys.exit(1)

def sha1file(filepath):
    if os.path.exists(filepath):
        return hashlib.sha1(open(filepath, "rb").read()).hexdigest()
    else:
        return ""
