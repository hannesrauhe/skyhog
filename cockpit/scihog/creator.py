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
import os,shutil,time
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
        t_dom = parse(self.templ_path)       
        for f in self.template_files: 
            content_file_path = self.input_dir+f
            
            p_dom = t_dom.cloneNode(True)
            while True:
                matchingNodes = [node for node in p_dom.getElementsByTagName("div") if node.hasAttribute("class") and node.getAttribute("class").startswith("__skyhog")]           
                if len(matchingNodes)==0:
                    break;
                el = matchingNodes[0]
                parent = el.parentNode
                if "blog"==el.getAttribute("class")[9:]:
                    artdom = sci_blog(self.input_dir,f,self.output_dir,f[1:],el.attributes).generate()
                    parent.replaceChild(artdom.childNodes[0],el)
                elif "static_page"==el.getAttribute("class")[9:]:
                    artdom = sci_page(self.input_dir,f,self.output_dir,f[1:],el.attributes).generate()
                    parent.replaceChild(artdom.childNodes[0],el)
                elif "nav"==el.getAttribute("class")[9:]:
                    artdom = sci_nav(self.input_dir,f,self.output_dir,f[1:],el.attributes).generate()
                    parent.replaceChild(artdom,el)
                else:
                    print "removed unknown element of class",el.getAttribute("class")[9:]
                    parent.removeChild(el)
#                print p_dom.toxml()
#                print "____________________________!"
            
            
            new_file = open(self.output_dir+f[1:],'w')
            new_file.write(p_dom.toxml())
            print "generated",f[1:]
            
    def move_to_page_dir(self):
        if os.path.isdir(self.page_dir):
            shutil.move(self.page_dir, self.page_dir[:-1]+"."+str(time.time()))
        shutil.copytree(self.output_dir, self.page_dir, True, shutil.ignore_patterns('_*.html'))
        
