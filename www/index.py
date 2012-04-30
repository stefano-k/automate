#!/usr/bin/env python

import cgitb
import os

cgitb.enable()

def template(name):
    tpl_file = os.path.join("templates", name + ".html")
    tpl_content = open(tpl_file).read()
    print tpl_content

# headers
print "Content-Type: text/html\n"

template("header")

#FIXME test

template("footer")
