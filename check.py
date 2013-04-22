"""
Copyright 2012 Hannes Rauhe

This file is part of Skyhog.

Skyhog is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
    
Skyhog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Skyhog.  If not, see <http://www.gnu.org/licenses/>.
"""
import argparse
from bs4 import *

parser = argparse.ArgumentParser(description='reads an HTML file and outputs the code as a browser would interpret it')
parser.add_argument('file', metavar='filepath', type=str,
                   help='HTML file to check')

args = parser.parse_args()

p_dom = BeautifulSoup(open(args.file,"r").read().strip()) 

print p_dom.prettify()
