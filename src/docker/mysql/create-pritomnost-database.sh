#!/usr/bin/env bash
# Local stand-in for the external "Pritomnost" (attendance) database.
# Runs once, on first start of an empty MySQL volume.
mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS dochadzka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    GRANT ALL PRIVILEGES ON \`dochadzka%\`.* TO '$MYSQL_USER'@'%';
EOSQL
mysql --user=root --password="$MYSQL_ROOT_PASSWORD" dochadzka < /opt/pritomnost-schema.sql
