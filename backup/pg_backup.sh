. $HOME/.bash_profile

send_alert()
{
   alert_dba -FALERT -S"PostgreSQL backup failed" -B"$LOG" -P"$PROG" -AY -GN -C"$HOSTNAME"
}

#dbname - DB name, username - DB username

. $HOME/.bash_profile

backup=$1
backup_type=$2  #HOURLY or DAILY
DATETIME=`date +%Y%m%d%H%M%S`
PROG=`basename $0`
HOSTNAME=`uname -n`
LOG=$LOG_DIR/${HOSTNAME}_${PROG}_${DATETIME}.log

PORT=`netstat -a | grep PGSQL | awk -F"." '{print $NF}' | uniq`

if [ ! -f ${PGDATA}/postmaster.pid ]; then
   alert_dba -FALERT -S"PostgreSQL database is down" -B"PostgreSQL database is down in this machine $HOSTNAME" -P"$PROG" -AY -GN -C"$HOSTNAME"
   exit
else
   printf "Starting backup....\n" > $LOG
   BACKUP_LABEL=${DATETIME}_`pg_ctl status -D ${PGDATA} | grep PID | awk -F":" '{print $3}' | awk -F")" '{print $1}' | sed 's/ //g'`
   BACKUP_FNAME=postgres_${backup_type}_${BACKUP_LABEL}.tar.gz
   printf "Execute this SELECT pg_start_backup('${BACKUP_LABEL}') with this filename ${BACKUP_FNAME} to here ${backup}\n" >> $LOG
   psql -d dbname -U username -c "SELECT pg_start_backup('${BACKUP_LABEL}')" 2>&1 | tee -a >> $LOG
   if [ ! "$?" -eq 0 ]; then
      printf "Start backup command pg_start_backup failed\n" >> $LOG
      send_alert
      exit
   fi
   printf "Execute this: tar -zcvf $backup/${BACKUP_FNAME} ${PGDATA}\n" >> $LOG
   cd ${PGDATA}
   cd .. 
   tar -zcvf $backup/${BACKUP_FNAME} data 2>&1 | tee -a >> $LOG
   if [ ! "$?" -eq 0 ]; then
      printf "tar command: tar -zcvf $backup/${BACKUP_FNAME} ${PGDATA} failed\n" >> $LOG
      send_alert
      exit
   fi
   printf "Execute this: SELECT pg_stop_backup()\n" >> $LOG
   psql -d dbname -U username -c "SELECT pg_stop_backup()" 2>&1 | tee -a >> $LOG
   if [ ! "$?" -eq 0 ]; then
      printf "stop command pg_stop_backup failed\n" >> $LOG
      send_alert
      exit
   fi
fi

find $backup -type f -mtime +5 -name 'postgres_HOURLY_*.tar.gz' -exec rm {} \; 2>&1 | tee -a >> $LOG
find $backup -type f -mtime +14 -name 'postgres_DAILY_*.tar.gz' -exec rm {} \; 2>&1 | tee -a >> $LOG
find /var/lib/pgsql/dba/logs -type f -mtime +15 -name '*.log' -exec rm {} \;

#alert_dba -FSUCCEED -S"PostgreSQL base backup succeeded" -B"$LOG" -P"$PROG" -AY -GY -C"$HOSTNAME"