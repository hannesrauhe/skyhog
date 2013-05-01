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

def updateEntry(col,val,e_link,cursor):
    cursor.execute("UPDATE nav SET "+col+"=? WHERE link=?",(val,e_link))
    

class NavException(Exception):
    def dummy(self):
        pass

parser = argparse.ArgumentParser(description='Add entries to the navigation menu, that will be created', prefix_chars='+-')
parser.add_argument('+link',"+l", dest='add', type=str, default='',
                   help='the link you want to add/modify to navigation')
parser.add_argument('-link','-l', dest='rem', type=str, default='',
                   help='the link you want to delete from navigation (other arguments will be ignored)')
parser.add_argument('--file','-f', dest='file', type=str, default='',
                   help='the file you want to delete navigation')
parser.add_argument('--db','-d', type=str, default='scihog.db',
                   help='the template file and in the preview directory')
parser.add_argument('--name','-n', type=str, default='',
                   help='the menu entry to show - defaults to filename without extension')
parser.add_argument('--id','-i', default='', type=str,
                   help='the id of the HTML entry - defaults to filename')
parser.add_argument('--pos','-p', default=0, type=int,
                   help='the position of the entry - defaults to last')

args = parser.parse_args()
connection = sqlite3.connect(args.db)
cursor = connection.cursor()

max_pos = 9

try:
    if len(args.add):
        e_link = args.add[1:] if args.add.startswith("_") else args.add
        cursor.execute("SELECT id,link,file,name,menu_order FROM nav WHERE link=?",(e_link,))
        entry = cursor.fetchone()
        if entry==None:
            e_id = args.id if len(args.id) else e_link
            e_file = args.file if len(args.file) else e_link if os.path.exists(e_link) or os.path.exists('_'+e_link) else ""
            e_name = args.name if len(args.name) else os.path.splitext(e_link)[0].capitalize()
            e_menu_order = args.pos if args.pos>0 else max_pos+1
            entry = (e_id,e_link,e_file,e_name,e_menu_order)
            cursor.execute("INSERT INTO nav(id,link,file,name,menu_order) VALUES (?,?,?,?,?)",entry)
            print "added entry", entry     
        else:
            if len(args.id):
                updateEntry("id",args.id,e_link,cursor)
            if len(args.file):
                updateEntry("file",args.file,e_link,cursor)
            if 0==args.pos:
                updateEntry("menu_order",args.pos,e_link,cursor)
            if len(args.name):
                updateEntry("name",args.name,e_link,cursor)
            print e_link,"modified"
    elif len(args.rem):      
        e_link = args.rem[1:] if args.rem.startswith("_") else args.rem
        cursor.execute("DELETE FROM nav WHERE link=?",(e_link,))
        print "removed entry", e_link    
    else:
        print "nothing to do - either -l or +l is needed"  
except NavException, e:
    print e

connection.commit()

cursor.execute("SELECT id,link,file,name,menu_order FROM nav WHERE menu_order>=0 ORDER BY menu_order")
for entry in cursor.fetchall():
    print entry
    
