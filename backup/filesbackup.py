#!/usr/bin/env python
# Created by Oleg Ivanov

import os, sys, getopt, logging
import smtplib
from smtplib import SMTPException
from email.mime.text import MIMEText
import time
from datetime import datetime
import subprocess
from subprocess import PIPE
import shutil
#from os import listdir
import glob
from os.path import isfile, join
from mountdrive import check_and_mountdrive

#SOURCE_PATH = ""
#DESTINATION_PATH = ""
MAILER_HOST = ""
#MAILER_PORT = ""
MAILER_USERNAME = ""
#MAILER_PASSWORD = ""

COMMAND_COUNTER = 0

def help():
    print(
        "Usage: python filebackup.py [OPTION]...\n" \
        "\n" \
        "-s, --source           path to the source directory\n" \
        "-b, --basedir          directory to archive\n" \
        "-d, --dest             path to the destination directory\n" \
        "-k, --keepcount        number of files to keep in backup destination\n" \
        "-h, --mailerhost       mailerhost\n" \
        #"-u, --maileruser       maileruser\n" \
        "-H, --help             this help"
    )


#python filesbackup.py -s test -d myarchive
#python filesbackup.py -s 'C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\order-lab\backup' -d myarchive -b test -h "smtp.med.cornell.edu" -f oli2002@med.cornell.edu -r oli2002@med.cornell.edu
#python filesbackup.py -s 'C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\order-lab\orderflex\public' -d 'C:\Users\ch3\Documents\myarchive' -b 'Uploaded' -h "smtp.med.cornell.edu" -f oli2002@med.cornell.edu -r oli2002@med.cornell.edu
#Testing: python filesbackup.py -s 'C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\order-lab\orderflex\public' -d 'C:\Users\ch3\Documents\myarchive' -b 'docs' -h "smtp.med.cornell.edu" -f oli2002@med.cornell.edu -r oli2002@med.cornell.edu
#python filesbackup.py -s '/opt/order-lab/orderflex/public' -d /mnt/pathology/view-test-backup/uploadsarchive -b 'Uploaded' -h "smtp.med.cornell.edu" -f oli2002@med.cornell.edu -r oli2002@med.cornell.edu
def start_backup(source, dest, basedir):
    print("source=",source,", dest=",dest,", basedir=",basedir)
    #logging.info("get_site_status: url="+url)

    #https://docs.python.org/3/library/shutil.html#shutil.make_archive
    #>> > from shutil import make_archive
    #>> > import os
    #>> > archive_name = os.path.expanduser(os.path.join('~', 'myarchive'))
    #>> > root_dir = os.path.expanduser(os.path.join('~', '.ssh'))
    #>> > make_archive(archive_name, 'gztar', root_dir)
    #'/Users/tarek/myarchive.tar.gz'
    #archive_name = os.path.expanduser(os.path.join('~', 'myarchive'))

    now = datetime.now()
    #append now.hour
    dest = dest + "_"  + str(now.hour) + "_" + str(now.minute)

    archivefile = ''
    try:
        if basedir != None:
            archivefile = shutil.make_archive(dest, 'gztar', source, base_dir=basedir)
        else:
            archivefile = shutil.make_archive(dest, 'gztar', source)
    except Exception as error:
        print("Error archiving: ",error)
        return error

    #print('archivefile=',archivefile)

    return None


#https://janakiev.com/blog/python-shell-commands/
#https://stackoverflow.com/questions/89228/how-do-i-execute-a-program-or-call-a-system-command
def runCommand(command):
    # try to restart the server
    print("run: " + command)
    #print(os.popen(command).read())
    #output = subprocess.run([command], capture_output=True) #capture_output is for python > 3.7
    output = subprocess.run([command], stdout=PIPE, stderr=PIPE, shell=True)
    print(output)
    # sleep in seconds
    time.sleep(3)


def send_email_alert(mailerhost, fromEmail, toEmailList, emailSubject, emailBody):
    emailBody = emailBody + "\n\n" + datetime.now().strftime('%Y-%B-%d %H:%M:%S')
    msg = MIMEText(emailBody)
    msg['Subject'] = emailSubject
    msg['From'] = fromEmail
    msg['To'] = ', '.join(toEmailList)

    MAILER_PORT = ''

    try:
        #print("MAILER_HOST=" + MAILER_HOST+", MAILER_PORT="+MAILER_PORT)
        smtpObj = smtplib.SMTP(mailerhost, MAILER_PORT)
        #if MAILER_USERNAME != "" and MAILER_PASSWORD != "":
        #    smtpObj.starttls()
        #    smtpObj.login(MAILER_USERNAME, MAILER_PASSWORD)
        smtpObj.sendmail(fromEmail, toEmailList, msg.as_string())
        print("Successfully sent email")
    except SMTPException:
        print("Error: unable to send email")
        #pass


def main(argv):

    print("\n### filesbackup.py "+datetime.now().strftime('%Y-%B-%d %H:%M:%S')+"###")
    #logging.basicConfig(filename='checksites.log',level=logging.INFO)
    #logging.info('main start')

    source = ''           # -s
    basedir = ''          # -b
    dest = ''             # -d
    mailerhost = ''       # -h
    #maileruser = ''       # -u
    receivers = ''        # -r
    fromEmail = ''        # -f
    keepcount = 1         # -k

    accessuser = ''
    networkfolder = ''
    localfolder = ''
    credentials = ''
    #username = ''
    #password = ''

    try:
        opts, args = getopt.getopt(
            argv,
            "s:b:d:h:r:f:k:" + "a:n:l:c:" + "H",
            [
                "source=", "basedir=", "dest=", "mailerhost=", "receivers=", "fromemail=", "keepcount=",
                "accessuser=", "networkfolder=", "localfolder=", "credentials=",
                "help"
            ]
        )
    except getopt.GetoptError:
        print('Parameters error')
        #logging.warning('Parameters error')
        #help()
        sys.exit(2)

    for opt, arg in opts:
        if opt in ("-s", "--source"):
            source = arg
            #print('filesbackup.py --urls=' + urls)
        elif opt in ("-b", "--basedir"):
            basedir = arg
        elif opt in ("-d", "--dest"):
            dest = arg
        elif opt in ("-h", "--mailerhost"):             # == "--mailerhost":
            mailerhost = arg
        #elif opt in ("-u", "--maileruser"):             # == "--maileruser":
        #    maileruser = arg
        elif opt in ("-r", "--receivers"):              #Array of the receiver emails
            receivers = arg
        elif opt in ("-f", "--fromemail"):                 #Sender email
            fromEmail = arg
        elif opt in ("-k", "--keepcount"):                 #number of files to keep in backup destination
            keepcount = arg

        #mountdrive parameters
        elif opt in ("-a", "--accessuser"):
            accessuser = arg
        elif opt in ("-n", "--networkfolder"):
            networkfolder = arg
        elif opt in ("-l", "--localfolder"):
            localfolder = arg
        elif opt in ("-c", "--credentials"):
            credentials = arg
        #elif opt in ("-U", "--username"):
        #    username = arg
        #elif opt in ("-P", "--password"):
        #    password = arg

        elif opt in ("-H", "--help"):
           help()
           #sys.exit()
           return
        else:
            #print('backupfiles.py: invalid option')
            #logging.warning('backupfiles.py: parameter errors')
            help()
            sys.exit(2)

    print('source=',source,', basedir=',basedir, 'dest=',dest, ", mailerhost=",mailerhost,", receivers=",receivers,", fromEmail=",fromEmail,", keepcount=",keepcount)
    print('accessuser', accessuser, 'networkfolder=', networkfolder, ', localfolder=', localfolder, 'credentials=', credentials)

    if source == '':
        print('Nothing to do: source is not provided')
        #logging.warning('Nothing to do: source is not provided')
        return

    if basedir == '':
        print('Nothing to do: basedir is not provided')
        return

    if dest == '':
        print('Nothing to do: destination is not provided')
        #logging.warning('Nothing to do: destination is not provided')
        return

    if keepcount == '':
        print('Please provide keepcount - number of files to keep in backup destination. Default keepcount=1')
        keepcount = 1

    toEmailList = ''
    if receivers:
        # receivers is comma separated string of receiver, convert to list
        receivers = receivers.replace(" ", "")
        # receivers is comma separated string of receiver, convert to list
        toEmailList = list(receivers.split(","))

    runCommand('whoami') #testing

    if accessuser and networkfolder and localfolder and credentials:
        mountError = check_and_mountdrive(accessuser, networkfolder, localfolder, credentials)
        if mountError:
            if mailerhost:
                emailSubject = "Error mount folder " + localfolder
                emailBody = "Error mount folder: accessuser=" + str(accessuser) + ", networkfolder=" + str(networkfolder) \
                            + ", localfolder=" + str(localfolder) + ", credentials=" + str(credentials) + "; Error=" + repr(mountError)
                send_email_alert(mailerhost, fromEmail, toEmailList, emailSubject, emailBody)
            else:
                print("Mailer parameters are not provided: Error email has not been sent. Error=",mountError)
    else:
        print("Skip check_and_mountdrive: parameters are not provided")

    archivefileError = start_backup(source, dest, basedir)

    if archivefileError:
        if mailerhost:
            emailSubject = "Error archiving folder " + basedir
            emailBody = "Error creating archive '" + str(dest) + "' for folder " + str(basedir) + " in " + str(source) + "; Error=" + repr(archivefileError)
            #emailBody = "Error creating archive: " + repr(archivefileError)
            send_email_alert(mailerhost, fromEmail, toEmailList, emailSubject, emailBody)
        else:
            print("Mailer parameters are not provided: Error email has not been sent. Error=",archivefileError)

        print("archivefileError=", archivefileError)
    else:
        print("Archive completed successfully")

    ### Remove old files from output directory based on keepcount ###
    #1) take output directory (dest or -d) and get base and filename
    basename, filename = os.path.split(dest)
    print("basename=",basename,", filename=",filename)
    #2) list all files in output directory
    # Get list of all files only in the given directory
    onlyfiles = filter(os.path.isfile, glob.glob(basename + '/' + filename + '*'))
    # Sort list of files based on last modification time in ascending order
    onlyfiles = sorted(onlyfiles, key=os.path.getmtime)

    for count, file in enumerate(onlyfiles):
        timestamp_str = time.strftime('%m/%d/%Y :: %H:%M:%S', time.gmtime(os.path.getmtime(file)))
        print("count=",count,", timestamp_str=",timestamp_str, "file=",file)

    if len(onlyfiles) > int(keepcount):
        lendiff = len(onlyfiles) - int(keepcount)
        for i in range(lendiff):
            print(i," remove=", onlyfiles[i])
            os.remove(onlyfiles[i])
    ### EOF Remove old files from output directory based on keepcount ###


if __name__ == '__main__':
    #python filesbackup.py -s test -d myarchive

    main(sys.argv[1:])


