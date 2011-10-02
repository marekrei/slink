#!/usr/bin/env python
import urlparse
import rorrim
import sys

if (len(sys.argv) >= 3):
	s = rorrim.Site(source=sys.argv[1], destination=sys.argv[2])
	print s.home.destination


