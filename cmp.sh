#!/bin/bash

DIR_ORIG=$1 #/var/www/
DIR_DEST=$2 #/var/www-encriptado/

FILES=`diff <(cd ${DIR_ORIG}; find * -depth) <(cd ${DIR_DEST}; find * -depth) | grep ^\> | cut -c3-`

for F in ${FILES}
do
    echo "borrando "${DIR_DEST}/${F} 
    rm -Rf ${DIR_DEST}/${F}
done;
