import argparse
import os

class html_generator:  
    templ = []  
    generated = []
    def __init__(self,t):
        self.templ = t
        
    def insert_content(self,content_file):
        skip = False
        for line in self.templ:        
            if line.strip().startswith("<!-- GENERATOR END content -->"):
                skip = False
            
            if not skip:
                self.generated += line 
                
            if line.strip().startswith("<!-- GENERATOR BEGIN content -->"):                
                self.generated += content_file.readlines()
                skip = True
                
    def create_file(self,html_file,content_file):
        self.generated = []
        self.insert_content(content_file)
        html_file.writelines(self.generated)
         
def template_name_condition(f):
    return f.lower().endswith('.html') and not f.startswith('__') and f.startswith('_')



parser = argparse.ArgumentParser(description='Create HTML-Files from Template.')
parser.add_argument('template', type=argparse.FileType('r'),
                   help='the template file and output-dir')

args = parser.parse_args()

gen = html_generator(args.template.readlines())
output_dir = os.path.dirname(args.template.name)

template_files = [item for item in os.listdir(output_dir) if template_name_condition(item)]
os.chdir(output_dir)
for f in template_files:
    content_file = open(f,'r')
    new_file = open(f[1:],'w')
    gen.create_file(new_file,content_file)
    print "generatedd",f[1:]
