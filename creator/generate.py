import argparse
from scihog.creator import *

parser = argparse.ArgumentParser(description='Create HTML-Files from Template.')
parser.add_argument('--template', type=str, default='../__template.html',
                   help='the template file and output-dir')

args = parser.parse_args()

gen = creator(args.template)

gen.generate()