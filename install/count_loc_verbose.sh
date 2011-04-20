#!/bin/bash
cloc --report-file=loc_catroweb_byfile --list-file=loc_filelist_catroweb --exclude-list=loc_exclude_files_catroweb --by-file
cloc --report-file=loc_tests_byfile --list-file=loc_filelist_tests --exclude-list=loc_exclude_files_tests --force-lang="html",txt --by-file
cloc --sum-reports --report_file=loc_sum  loc_catroweb  loc_tests
echo " "
echo "========= RESULTS ========="
cat loc_sum.file
cat loc_sum.lang



