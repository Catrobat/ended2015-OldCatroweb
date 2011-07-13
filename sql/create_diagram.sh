#!/bin/bash
# Generating ER Diagram for catroweb
#
# dependencies
#
#  on debian:
#    apt-get install postgresql-autodoc graphviz
#  on fedora:
#    yum install postgresql_autodoc graphviz
#

rm -rf ../sql-overview
mkdir ../sql-overview
cd ../sql-overview

array=(catroboard catroweb catrowiki)
count=${#array[*]}

i=0
while [ $i -lt $count ]; do
  dbname=${array[$i]}
  postgresql_autodoc -h localhost -p 5432 -d ${dbname} -u website --password=cat.roid.web
  dot -Tpng ${dbname}.dot > ${dbname}.png
  sed -i "s/public<\/a><\/li><ul>/public<\/a><\/li><ul>\n      <li><a href=\"${dbname}.png\" target=\"_blank\">overview<\/a><\/li>/" ${dbname}.html
  let i++
done

rm *.dia
rm *.dot
rm *.neato
rm *.xml

cat >> index.html << FOE
<html>
  <head>
    <title>Catroweb SQL Overview</title>
  </head>
  <body>
    <h1>Catroweb SQL Overview</h1>
    <h2>tables:</h2>
    <ul>
      <li><a href="catroboard.html">catroboard</a></li>
      <li><a href="catroweb.html">catroweb</a></li>
      <li><a href="catrowiki.html">catrowiki</a></li>
    </ul>
  </body>
</html>
FOE

