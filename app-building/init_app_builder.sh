#!/bin/bash

current_sdk=android-sdk_r12-linux_x86.tgz
sdk_folder=android-sdk-linux_x86
source_folder=catroid-source

if [ -d ${source_folder} ]; then
  echo "updating catroid source..."
  echo ""
  cd ${source_folder}
  hg revert -a
  hg pull -u
  cd ..
else
  echo "getting source and android sdk..."
  echo ""
  hg clone -r default https://code.google.com/p/catroid/ ${source_folder}

  wget http://dl.google.com/android/${current_sdk}
  tar -xf ${current_sdk}
  rm ${current_sdk}
  ./${sdk_folder}/tools/android update sdk -u -t 8,platform-tool
fi

./${sdk_folder}/tools/android update project --path ${source_folder}/catroid --name NativeAppActivity
echo ""
echo "native app builder is ready"

