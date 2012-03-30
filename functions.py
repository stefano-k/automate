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
    if distro_codename == "wheezy":
        return "debian"
    elif distro_codename == "oneiric":
        return "ubuntu"
    elif distro_codename == "precise":
        return "ubuntu"
    
