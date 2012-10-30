#!/usr/bin/python

from optparse import OptionParser
import json, sys, re, oursql

"""
@author jbowens

Exports the database's translations strings to to a json file.
"""

# parse_dsn function borrowed from 
# http://e-mats.org/2011/01/parse-a-dsn-string-in-python/
def parse_dsn(dsn):
    m = re.search("([a-zA-Z0-9]+):(.*)", dsn)
    values = {}
   
    if (m and m.group(1) and m.group(2)):
        values['driver'] = m.group(1)
        m_options = re.findall("([a-zA-Z0-9]+)=([a-zA-Z0-9]+)", m.group(2))
       
        for pair in m_options:
            values[pair[0]] = pair[1]
 
    return values

parser = OptionParser()
parser.add_option('-c', '--config-file', dest='config', help="the esprit config file to use when connecting to the db")
parser.add_option('-o', '--output-file', dest='output', help="The file to output the translations to. if ommitted, stdout")

(options,args) = parser.parse_args()

if not options.config:
    print "You must provide an esprit config file"
    sys.exit(1)

config_contents = ""
try:
    config_contents =  open(options.config, 'r').read()
except:
    print "Unable to read configuration file: %s" % options.config
    sys.exit(1)

config = json.loads(config_contents)

if not "db_default_dsn" in config:
    print "'db_default_dsn' not set in config file"
    sys.exit(1)
if not "db_default_user" in config:
    print "'db_default_user' not set in config file"
    sys.exit(1)
if not "db_default_pass" in config:
    print "'db_default_pass' not set in config file"
    sys.exit(1)

db_default_dsn = config['db_default_dsn']
db_default_user = config['db_default_user']
db_default_pass = config['db_default_pass']

dsn_info = parse_dsn(db_default_dsn)

conn = oursql.connect(host=dsn_info['host'], user=db_default_user, passwd=db_default_pass, db=dsn_info['dbname']);
cur = conn.cursor()
cur.execute( "SELECT * FROM translations" );
for row in cur.fetchall():
    print row
