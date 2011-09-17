#!/bin/bash

# if a table doesn't exist, no worries... we keep on iterating

if [[ "X${1}" == "X" ]]; then
  echo "please provide a database name that the root user (passwordless) can access"
  exit 1
fi

dbname=$1

tablelist=$(cat <<EOT
tx_wecsermons_resources
tx_wecsermons_resource_types
tx_wecsermons_sermons_resources_rel
tx_wecsermons_series_resources_rel
tx_wecsermons_sermons
tx_wecsermons_series
tx_wecsermons_sermons_series_rel
tx_wecsermons_topics
tx_wecsermons_sermons_topics_rel
tx_wecsermons_series_topics_rel
tx_wecsermons_seasons
tx_wecsermons_series_seasons_rel
tx_wecsermons_speakers
tx_wecsermons_sermons_speakers_rel
tx_wecsermons_sermons_resources_uid_mm
tx_wecsermons_series_resources_uid_mm
tx_wecsermons_meta
EOT);

echo "Note that if the script just finds a handful (<4) of tables missing, this is likely kosher..."
echo "It simply is a result of trying to delete database structures from previous versions (the uid_mm tables)"

for tbl in $tablelist; do echo "drop table $tbl;" | mysql -u root $dbname; done 
