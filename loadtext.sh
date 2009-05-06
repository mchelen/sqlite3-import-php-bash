#! /usr/bin/env bash

sqlite3 database.db <<SQL_ENTRY_TAG_1
.separator "\t"
.import file_list.txt filelist
SQL_ENTRY_TAG_1
