import argparse
import os

class html_generator:  
    templ = []  
    generated = []
    content_name = ''
    def __init__(self,t):
        self.templ = t
        
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
            mod = __import__(module_name)
            module_cont = mod.generate(self.content_name)
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
            print 'error while importing',module
    
    def write_to_file(self,html_file):
        html_file.writelines(self.generated)
        
    def clear(self):
        self.generated=[]
        self.content_name=''
         
def template_name_condition(f):
    return f.lower().endswith('.html') and not f.startswith('__') and f.startswith('_')



parser = argparse.ArgumentParser(description='Create HTML-Files from Template.')
parser.add_argument('template', type=argparse.FileType('r'),
                   help='the template file and output-dir')

args = parser.parse_args()

gen = html_generator(args.template.readlines())
output_dir = os.path.dirname(args.template.name)+'/'
input_dir = output_dir

template_files = [item for item in os.listdir(output_dir) if template_name_condition(item)]
#os.chdir(output_dir)
for f in template_files:
    content_file = open(input_dir+f,'r')
    new_file = open(output_dir+f[1:],'w')
    gen.insert_content(f[1:],content_file)
    gen.insert_module("nav")
    gen.write_to_file(new_file)
    gen.clear()
    print "generated",f[1:]
