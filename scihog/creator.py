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
import os,shutil,time,re
from sci_template import *


class creator(object):
    '''
    classdocs
    '''
    templ_path = ""        
    templ = []  
    template_files = []
    output_dir = ''
    input_dir = ''
    page_dir = ''
    
    def __init__(self,t,page_directory):
        '''
        Constructor
        '''
        self.page_dir = page_directory+'/'
        self.output_dir = os.path.dirname(t)+'/'
        self.input_dir = self.output_dir

        self.templ_path = t
        template_file = open(t, 'r')
        self.templ = template_file.readlines()
        self.template_files = [item for item in os.listdir(self.output_dir) if self.template_name_condition(item)]
        
    def template_name_condition(self,f):
        return f.lower().endswith('.html') and not f.startswith('__') and f.startswith('_')
            
    def generate(self):
        for f in self.template_files: 
            p_dom = BeautifulSoup(open(self.templ_path,"r").read().strip())    
            content_file_path = self.input_dir+f
            pluged_in = {}

            while True:
                el = p_dom.find("div",{"class":re.compile("__skyhog")})
#                print el
                if None==el:
                    break;
                for c in el["class"]:
                    if c.startswith("__skyhog"):
                        plugin_name = el["class"][0][9:]
                        
                if not pluged_in.has_key(plugin_name):
                    if "static_page"==plugin_name:
                        pluged_in[plugin_name] = sci_page(self.input_dir,f,self.output_dir,f[1:],p_dom)
                    elif "blog"==el["class"][0][9:]:
                        pluged_in[plugin_name] = sci_blog(self.input_dir,f,self.output_dir,f[1:],p_dom)
                    elif "nav"==el["class"][0][9:]:
                        pluged_in[plugin_name] = sci_nav(self.input_dir,f,self.output_dir,f[1:],p_dom)
                    else:
                        print "removed unknown element of class",el["class"]
                        el.extract()
                        continue
                
                artdom = pluged_in[plugin_name].generate(el["class"])
                el.replace_with(artdom)
#                print "___________________"
#                print p_dom.prettify()
#                print "____________________________!"
            
            
            new_file = open(self.output_dir+f[1:],'w')
            new_file.write(p_dom.prettify().encode("UTF-8"))
            print "generated",f[1:]
            
    def move_to_page_dir(self,bak_dir):
        if os.path.isdir(self.page_dir):
            if not os.path.isdir(bak_dir):
                os.mkdir(bak_dir)
            shutil.move(self.page_dir, bak_dir+"/"+str(time.time()))
        print self.output_dir,self.page_dir
        shutil.copytree(self.output_dir, self.page_dir, True, shutil.ignore_patterns('_*.html','.git'))
        
