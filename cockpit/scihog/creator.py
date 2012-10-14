import os
import shutil
import time

'''
Created on 26.06.2012

@author: hannes
'''

class creator(object):
    '''
    classdocs
    '''        
    templ = []  
    generated = []
    content_name = ''
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

        template_file = open(t, 'r')
        self.templ = template_file.readlines()
        self.template_files = [item for item in os.listdir(self.output_dir) if self.template_name_condition(item)]
        
    def insert_content(self,content_name,content_file):
        self.content_name = content_name
        skip = False
        for line in self.templ:        
            if line.strip().startswith("<!-- GENERATOR END content -->"):
                skip = False
            
            if not skip:
                self.generated.append(line) 
                
            if line.strip().startswith("<!-- GENERATOR BEGIN content -->"):                
                self.generated.extend(content_file.readlines())
                skip = True
                
    def insert_module(self,module_name):
        try:
            mod = __import__("scihog."+module_name, globals(), locals(), [module_name], -1)
            module_cont = mod.generate(self,self.content_name)
            new_generated = [] 
            skip = False
            for line in self.generated:        
                if line.strip().startswith("<!-- GENERATOR END %s -->" % module_name):
                    skip = False
                
                if not skip:
                    new_generated.append(line) 
                    
                if line.strip().startswith("<!-- GENERATOR BEGIN %s -->" % module_name):                
                    new_generated.extend(module_cont)
                    skip = True
            if not skip:
                self.generated = new_generated
            else:
                print 'start tag without end tag for module %s found in template -> ignoring module' % module_name
        except ImportError:
            print 'error while importing',module_name
    
    def write_to_file(self,html_file):
        html_file.writelines(self.generated)
        
    def template_name_condition(self,f):
        return f.lower().endswith('.html') and not f.startswith('__') and f.startswith('_')
    
    def clear(self):
        self.generated=[]
        self.content_name=''
        
    def generate(self):        
        for f in self.template_files:
            content_file = open(self.input_dir+f,'r')
            new_file = open(self.output_dir+f[1:],'w')
            self.insert_content(f[1:],content_file)
            self.insert_module("nav")
            self.write_to_file(new_file)
            self.clear()
            print "generated",f[1:]
            
    def move_to_page_dir(self):
        if os.path.isdir(self.page_dir):
            shutil.move(self.page_dir, self.page_dir[:-1]+"."+str(time.time()))
        shutil.copytree(self.output_dir, self.page_dir, True, shutil.ignore_patterns('_*.html'))
