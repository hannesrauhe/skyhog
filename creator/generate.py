import argparse
from scihog.creator import *

parser = argparse.ArgumentParser(description='Create HTML-Files from Template.')
parser.add_argument('--template', type=str, default='preview/__template.html',
                   help='the template file and in the preview directory')
parser.add_argument('--page_dir', type=str, default='../',
                   help='the directory for the final output')
parser.add_argument('--final', default=False,
                   help='provide to overwrite the files in the page_dir with the generated ones')

args = parser.parse_args()

gen = creator(args.template,args.page_dir)

gen.generate()

if args.final:
    gen.move_to_page_dir()