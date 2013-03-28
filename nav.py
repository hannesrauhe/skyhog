'''
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
'''

import sqlite3,argparse,os

class NavException(Exception):
    def dummy(self):
        pass

parser = argparse.ArgumentParser(description='Add entries to the navigation menu, that will be created', prefix_chars='+-')
parser.add_argument('+nav_file', dest='add', type=str, default='',
                   help='the file you want to add/modify to navigation')
parser.add_argument('-nav_file', dest='rem', type=str, default='',
                   help='the file you want to delete navigation')
parser.add_argument('--db', type=str, default='preview/scihog.db',
                   help='the template file and in the preview directory')
parser.add_argument('--nav_name', type=str, default='',
                   help='the menu entry to show - defaults to filename without extension')
parser.add_argument('--nav_id', default='', type=str,
                   help='the id of the HTML entry - defaults to filename')
parser.add_argument('--nav_pos', default=0, type=int,
                   help='the position of the entry - defaults to last')

args = parser.parse_args()
connection = sqlite3.connect(args.db)
cursor = connection.cursor()

max_pos = 9

try:
    if len(args.add):
        e_link = args.add[1:] if args.add.startswith("_") else args.add
        e_id = args.nav_id if len(args.nav_id) else e_link
        e_name = args.nav_name if len(args.nav_name) else os.path.splitext(e_link)[0].capitalize()
        e_menu_order = args.nav_pos if args.nav_pos>0 else max_pos+1
        entry = (e_id,e_link,e_name,e_menu_order)
        cursor.execute("INSERT INTO nav VALUES (?,?,?,?)",entry)
        print "added entry", entry      
    elif len(args.rem):      
        e_link = args.rem[1:] if args.rem.startswith("_") else args.rem
        cursor.execute("DELETE FROM nav WHERE link=?",(e_link,))
        print "removed entry", e_link      
except NavException, e:
    print e

connection.commit()

cursor.execute("SELECT id,link,name,menu_order FROM nav WHERE menu_order>=0 ORDER BY menu_order")
for entry in cursor.fetchall():
    print entry
    
