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

import argparse,logging
from scihog.creator import *

parser = argparse.ArgumentParser(description='Create HTML-Files from Template.')
parser.add_argument('--template', type=str, default='./__template.html',
                   help='the template file and in the preview directory')
parser.add_argument('--page_dir', type=str, default='live',
                   help='the directory for the final output')
parser.add_argument('--backup_dir', type=str, default='bak',
                   help='the directory for the backup output - requires --final')
parser.add_argument('--final', default=False,
                   help='provide to overwrite the files in the page_dir with the generated ones')
parser.add_argument('--verbose','-v',type=int, default=1,
                   help='verbosity 0 - 3')
parser.add_argument('--list_plugins',type=bool, default=False,
                   help='list plugins and quit')

args = parser.parse_args()

if 0==args.verbose:
    logging.basicConfig(level=logging.ERROR)
elif args.verbose==1:
    logging.basicConfig(level=logging.WARNING)
elif args.verbose==2:
    logging.basicConfig(level=logging.INFO)
else:
    print "Debug logging"
    logging.basicConfig(level=logging.DEBUG)

gen = creator(args.template,args.page_dir)

if args.list_plugins:
    gen.list_available_plugins()
    sys.exit()
    
gen.generate()

if args.final:
    print "Copying generated files to the page directory"
    gen.move_to_page_dir(args.backup_dir)