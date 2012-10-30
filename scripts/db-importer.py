#!/usr/bin/python

from optparse import OptionParser
import json, sys, re, oursql

"""
@author jbowens

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
parser.add_option('-s', '--source-file', dest='source', help="The file to read the input json from. If not provided, the script will expect input from stdin.")

(options,args) = parser.parse_args()

if len(args) != 2:
    print "This script expects exactly 2 arguments, the esprit config file and the db table to import into"
    sys.exit(1)

config_contents = ""
try:
    config_contents =  open(args[0], 'r').read()
except:
    print "Unable to read configuration file: %s" % args[0]
    sys.exit(1)

config = json.loads(config_contents)

# Make sure the db data is legit
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

# Get the data to input
if options.source:
    input_str = open(options,source,'r').read()
else:
    input_str = '\n'.join(sys.stdin.readlines())

input_json = json.loads(input_str)

conn = oursql.connect(host=dsn_info['host'], user=db_default_user, passwd=db_default_pass, db=dsn_info['dbname']);
cur = conn.cursor(oursql.DictCursor)
rows_inserted = 0
for k in input_json:
    cur.execute( 'INSERT INTO ' + args[1] + ' ('+', '.join(input_json[k].keys())+') VALUES( ' + ', '.join(['?' for x in range(len(input_json[k]))]) + ')', input_json[k].values() )
    rows_inserted = rows_inserted + cur.rowcount;

print( "Inserted %d rows" % rows_inserted )

