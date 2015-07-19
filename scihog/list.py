"""
Copyright 2012-2015 Hannes Rauhe

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
import argparse,logging,urlparse
from scihog.creator import *

parser = argparse.ArgumentParser(description='list templates found in preview directory')
parser.add_argument('--preview_dir', type=str, default='./',
                   help='the preview directory')

args = parser.parse_args()

#the exact template does not matter
gen = creator(os.path.join(args.preview_dir, "__template.html"), "", "")
    
for x in gen.get_pages():
    print x