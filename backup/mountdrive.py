#Check and mount network shared drive:

#accessuser     - access permission for this user (i.e. apache, postgres)
#networkfolder  - remote folder, mount point
#localdrive     - local folder to mount
#username       - username of the service account\
#password       - password of the service account

def check_and_mountdrive(accessuser, networkfolder, localfolder, username, password):
    print('check_and_mountdrive: accessuser',accessuser, 'networkfolder=',networkfolder, ', localfolder=',localfolder, 'username=',username, ", password=",password)

    command = "sudo mount -t cifs -o"
    command = command + " username='"+username+"',password='"+password+"'"+",uid=48,forceuid,gid=48,forcegid,file_mode=0664,dir_mode=0775"
    command = command + " " + networkfolder + " " + localfolder
    print("command="+command)

    runCommand(command)  # testing

    try:
        if command != None:
            mountError = runCommand(command)
        else:
            return "Mount command is empty"
    except Exception as error:
        print("Error archiving: ",error)
        return error

    return None

def help():
    print(
        "Usage: python filebackup.py [OPTION]...\n" \
        "\n" \
        "-a, --accessuser       access permission for this user (i.e. apache, postgres)\n" \
        "-n, --networkfolder    remote folder, mount point\n" \
        "-l, --localdrive       local folder to mount\n" \
        "-e, --username         username of the service account\n" \
        "-p, --password         password of the service account\n" \
        "-H, --help             this help"
    )

def main(argv):

    print("\n### mountdrive.py "+datetime.now().strftime('%Y-%B-%d %H:%M:%S')+"###")
    #logging.basicConfig(filename='checksites.log',level=logging.INFO)
    #logging.info('main start')

    accessuser      = ''  #accessuser
    networkfolder   = ''  #networkfolder
    localfolder     = ''  #localfolder
    username        = ''  #username
    password        = ''  #password

    try:
        opts, args = getopt.getopt(
            argv,
            "a:n:l:e:p:H",
            ["accessuser=", "networkfolder=", "localfolder=", "username=", "password=", "help"]
        )
    except getopt.GetoptError:
        print('Parameters error')
        #logging.warning('Parameters error')
        #help()
        sys.exit(2)

    for opt, arg in opts:
        if opt in ("-a", "--accessuser"):
            accessuser = arg
        if opt in ("-n", "--networkfolder"):
            networkfolder = arg
        elif opt in ("-l", "--localfolder"):
            localfolder = arg
        elif opt in ("-e", "--username"):
            username = arg
        elif opt in ("-p", "--password"):
            password = arg
        elif opt in ("-H", "--help"):
           help()
           return
        else:
            #print('backupfiles.py: invalid option')
            #logging.warning('backupfiles.py: parameter errors')
            help()
            sys.exit(2)

    print('accessuser',accessuser, 'networkfolder=',networkfolder, ', localfolder=',localfolder, 'username=',username, ", password=",password)

    if accessuser == '':
        print('Nothing to do: accessuser is not provided')
        return

    if networkfolder == '':
        print('Nothing to do: networkfolder is not provided')
        return

    if localfolder == '':
        print('Nothing to do: localfolder is not provided')
        return

    if username == '':
        print('Nothing to do: username is not provided')
        #logging.warning('Nothing to do: destination is not provided')
        return

    if password == '':
        print('Nothing to do: password is not provided')

    runCommand('whoami') #testing

    mountError = check_and_mountdrive(accessuser, networkfolder, localfolder, username, password)

    if mountError:
        print("mountError=", mountError)
    else:
        print("Mount completed successfully")


if __name__ == '__main__':
    #python filesbackup.py -s test -d myarchive

    main(sys.argv[1:])

