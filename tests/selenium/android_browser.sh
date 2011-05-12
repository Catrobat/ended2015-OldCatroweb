#!/bin/bash

source $(dirname $0)/android_sdk_home.sh

exec $ANDROID_HOME/platform-tools/adb shell \
  am start -a android.intent.action.VIEW -d \"$1\"


