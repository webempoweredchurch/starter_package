#!/usr/bin/env python

#
# Copyright (c) 2008
# WebEmpoweredChurch.com
# Written by Matt Bradshaw
#

"""\
Usage:

find_missing_translations_inxml.py <path>

"""

import sys
import xml.dom.minidom

if len(sys.argv) != 2:
    print __doc__
    sys.exit(1)


document = xml.dom.minidom.parse(sys.argv[1])
data = document.getElementsByTagName('data')[0]
languages = data.getElementsByTagName('languageKey')

# our horrific wrt time complexity algorithm
for language in languages:
    languageName = language.attributes['index'].value
    for mapping in language.getElementsByTagName('label'):
        key = mapping.attributes['index'].value
        value = mapping.firstChild.nodeValue

        # only consider a language element existing if it has a value component
        if value:
            for sublanguage in languages:
                sublanguageName = sublanguage.attributes['index'].value
                submapping = {}
                for labels in sublanguage.getElementsByTagName('label'):
                    labelkey = labels.attributes['index'].value
                    labelval = labels.firstChild.nodeValue
                    submapping[labelkey] = labelval
                if not submapping.has_key(key) or submapping[key] == '':
                    print "%s has %s (with value %s) but %s is missing this mapping" % (languageName, key, value, sublanguageName)
    
