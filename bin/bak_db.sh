#!/bin/bash
clear

echo ' -- SmartSoftware SRL --'
echo 'Back up de datos de servidor remoto'

echo -n "Ingrese la tablas separadas por espacios o [ENTER] para toda la base: "
read tabla

echo -n "Ingrese nombre de la Base de Datos o [ENTER] smartGestionDesarrollo: "
read -i "smartGestionDesarrollo" -e base

mysqldump --opt --routines -h 127.0.0.1 -uroot -pvidalac116059 $base $tabla |gzip -c > backup.$base.$tabla.`date +"%Y-%m-%d_%H%M%S"`.sql.gz

