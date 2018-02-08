#!/bin/bash

echo " - Copiando configuracion"
# Traigo la configuracion
cp /var/repos/config/* . -r

echo " - Permisos de carpetas"
chmod 777 logs -R
chmod 777 data -R
chmod 777 birt/Reports -R
chmod 777 public/sessionChartImages -R

echo " - Borrando reportes compilados"

rm birt/Reports/*.rptdesign

echo " - COMPOSER"
composer install

#migramos
echo " - Migraciones"
vendor/bin/phinx migrate

# compilo librerias
echo " - Compilando librerias de BIRT"
cd application/process/
./cm BirtCompileLibrarys

echo "OK"