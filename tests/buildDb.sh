#!/bin/bash
echo "drop database dbtest;create database dbtest;"|mysql -u root -pvidalac116059
mysql -u root -pvidalac116059 dbtest<../db/db.sql
mysql -u root -pvidalac116059 dbtest<../db/common.sql
# mysql -u root -pvidalac116059 dbtest<../db/test.sql
mysql -u root -pvidalac116059 dbtest<db/test.sql