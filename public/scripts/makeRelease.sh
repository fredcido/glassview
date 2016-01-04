#!/bin/sh

mv dojo dojoAnt

HERE=`dirname $(readlink -f $0)`
DATA=`date +%Y.%m.%d`

echo ""
echo -e "\e[31m Atual diretorio: $HERE \e[m"
echo ""
echo -e "\e[31m [                                                  0%] \e[m"


cd $HERE/dojo-release-1.5.1-src/util/buildscripts

./build.sh  action=release optimize=shrinksafe layerOptimize=shrinksafe copyTests=false stripConsole=all releaseDir=$HERE/  profileFile=$HERE/profile.js version=GlassView-$DATA cssOptimize=comments releaseName=dojo


cd $HERE/

echo ""
echo -e "\e[32m Zipando pacote\e[m"
echo ""
echo -e "\e[31m [ #############################                   60%] \e[m"

zip -r dojo.zip dojo

echo ""
echo -e "\e[32m Removendo pacote novo\e[m"
echo ""
echo -e "\e[31m [ ######################################         80%] \e[m"
rm -rf dojo
mv dojoAnt dojo 

echo ""
echo -e "\e[32m Descompactado pacote\e[m"
echo ""
echo -e "\e[31m [ ##########################################      90%] \e[m"
unzip dojo.zip

echo ""
echo -e "\e[32m Removendo pacote Zipado\e[m"
echo ""
echo -e "\e[31m [ ###########################################     95%] \e[m"
rm -rf dojo.zip

echo ""
echo -e "\e[32m [ ############################################## 100%] \e[m"
echo ""