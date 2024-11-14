#!/bin/bash
# ____________________________________________________________________________
# 
#  _____ _____ __                   _____         _ _           
# |     |     |  |      ___ ___ ___|     |___ ___|_| |_ ___ ___ 
# |-   -| | | |  |__   | .'| . | . | | | | . |   | |  _| . |  _|
# |_____|_|_|_|_____|  |__,|  _|  _|_|_|_|___|_|_|_|_| |___|_|  
#                          |_| |_|                              
#                    _                            _                       
#            ___ ___|_|   ___ ___ ___ _ _ ___ ___| |_                     
#           | .'| . | |  |  _| -_| . | | | -_|_ -|  _|                    
#           |__,|  _|_|  |_| |___|_  |___|___|___|_|                      
#               |_|                |_|                                                                     
# ____________________________________________________________________________
# 
# APPMONITOR API CLIENT
#
# @author Axel Hahn
# @link https://github.com/iml-it/appmonitor-api-client
# @license GPL
# @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
# --------------------------------------------------------------------------------
# 2024-11-14  0.1  axel.hahn@unibe.ch  first lines
# --------------------------------------------------------------------------------


bDebug=0

line=-----------------------------------------------------------------------------
self=$( basename $0 )


USAGE="
APPMONITOR API CLIENT :: Bash

SYNTAX
    $self [OPTIONS] [URL]

OPTIONS
    -h|--help      show this help and exit
    -d|--debug     show more output

EXAMPLES
    $self /api/v1/apps/tags 
                   list all tags

    $self /api/v1/apps/tags/monitoring/meta
                   Get metadata of all apps with tag 'monitoring'

    $self /api/v1/apps/tags/monitoring,live/meta
                   Get metadata of all apps with tags 'monitoring' and 'live'

"

. "$( dirname "$0" )/api_config.sh" || exit 1



# ----------------------------------------------------------------------
# FUNCTIONS
# ----------------------------------------------------------------------


# make an http request to fetch the software
#
# param  string  method; should be GET
# param  string  request url (without protocol and server)
# param  string  optional: filename for output data
# param  string  optional: secret; default: it will be generated
#
# global int     bDebug        (0|1)
# global string  AM_APIURL     appmonitor url
# global string  AM_APIUSER    name of api user
# global string  AM_APISECRET  secret for hmac hash
# global string  AM_APIPW      or: password for basic auth
#
# global string  line    string for a line with dashes
function makeRequest(){

  local apiMethod=$1
  local apiRequest=$2
  local params=()

    params+=(
        -H "Accept: application/json" -H "Content-Type: application/json"
        -X $apiMethod
        -s
    )

  # local outfile=$( mktemp )

  if [ $bDebug = 1 ]; then
    echo $line
    echo "$apiMethod ${apiHost}${apiRequest}"
    params+=( -i )
  fi

  if [ ! -z "$AM_APISECRET" ]; then

    # --- date in http format
    LANG=en_EN
    # export TZ=GMT
    apiTS=$(date "+%a, %d %b %Y %H:%M:%S.%N %Z")


# --- generate data to hash: method + uri + timestamp; delimited with line break
data="${apiMethod}
${apiRequest}
${apiTS}
"
    # these ase non critical data ... it does not show the ${secret}
    if [ "$bDebug" = "1" ]; then
        echo $line
        echo "RAW data for hashed secret:"
        echo "$data"
    fi

    # generate hash - split in 2 commands (piping "cut" sends additional line break)
    myHash=$(echo -n "$data" | openssl dgst -sha1 -hex -hmac "${AM_APISECRET}" | cut -f 2 -d " ")
    myHash=$(echo -n "$myHash" | base64)

    params+=(
      -H "Date: ${apiTS}"
      -H "Authorization: ${AM_APIUSER:-api}:${myHash}"
    )

  else
    if [ ! -z "$AM_APIPW" ]; then
        params+=(
        -u "${AM_APIUSER}:${AM_APIPW}"
        )
    fi

  fi


  test $bDebug = 1 && ( 
        echo "$line"
        echo "Curl params:"
        echo "${params[@]}"
        echo "${AM_APIURL}${apiRequest}"
        echo 
        echo "$line"
    )
  curl "${params[@]}" ${AM_APIURL}${apiRequest}
  rc=$?

  test $bDebug = 1 && ( echo; echo "rc=$rc" )
}


# ----------------------------------------------------------------------
# MAIN
# ----------------------------------------------------------------------

# parse params
while [[ "$#" -gt 0 ]]; do case $1 in
    -h|--help)      echo "$USAGE"; exit 0;;
    -d|--debug)   bDebug=1; shift;;
    *) if grep "^-" <<< "$1" >/dev/null ; then
        echo; echo "ERROR: Unknown parameter: $1"; echo; _showHelp; exit 2
       fi
       break;
       ;;
esac; done

makeRequest GET $*

# ----------------------------------------------------------------------
