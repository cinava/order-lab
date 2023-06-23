#!/usr/bin/env python
# Created by Oleg Ivanov

#Modify files
#1) find something like "->getRepository('"
#->getRepository('AppUserdirectoryBundle:EventObjectTypeList')->
#2) from tha line in 1 get the string 'AppUserdirectoryBundle' and string 'EventObjectTypeList'
#3) replace 'AppUserdirectoryBundle:EventObjectTypeList' by 'EventObjectTypeList::class'
#4) use string 'AppUserdirectoryBundle' to get the location of the file EventObjectTypeList
#5) construct the namespace according to the file location: 'use App\UserdirectoryBundle\Entity;'
#6) add this 'use ...' to the beginning of the file if it does not exist in the file

import os, sys, getopt
from subprocess import check_output
import glob, shutil
import re


DIR = ""
FINDSTR = ""

def process_files( dir, startstr, endstr ):
    output = []

    if dir == '':
        res = "Subject directory name is empty"
        output.append(res)
        print(res)
        return output

    if startstr == '':
        res = "String to find and process is empty"
        output.append(res)
        print(res)
        return output

    dir = dir.strip()
    startstr = startstr.strip()
    endstr = endstr.strip()

    if not os.path.exists(dir):
        res = "Folder does not exist: " + dir
        output.append(res)
        print(res)
        return output

    dir_path = os.path.abspath(dir)
    print("dir_path=", dir_path)

    #0) get all files
    files = getListOfFiles(dir_path)
    print("files=", len(list(files)))

    for filepath in files:
        if ".php" in filepath:
            #print("filepath=", filepath)
            file = open(filepath, mode='r', encoding='utf8')
            content = file.read()
            #fileObject = glob.glob(file)
            #content = fileObject.read()
            if startstr in content:
                #print(startstr + "exists in ", filepath)
                process_single_file(filepath,startstr,endstr)

            file.close()

        #1) find something like getRepository('AppUserdirectoryBundle:EventObjectTypeList')


    return output

def process_single_file( filepath, startstr, endstr ):
    with open(filepath, mode='r', encoding='utf8') as file:
        # read a list of lines into data
        data = file.readlines()

    for l_no, line in enumerate(data):
        print('string found in a file', filepath)
        print('Line Number:', l_no)
        print('startstr in:', line)
        #https://stackoverflow.com/questions/4719438/editing-specific-line-in-text-file-in-python
        linemodified = process_line(l_no, line, filepath, startstr, endstr)
        data[l_no] = linemodified

    # and write everything back
    #with open(filepath, 'w', encoding='utf8') as file:
    #    file.writelines(data)

    return

def process_line(l_no,origline,filepath,startstr, endstr):
    line = origline.lstrip()
    # print("\n")
    if startstr in line and endstr in line:
        if line[:2] != '//':
            if line.count(startstr) == 1 and line.count(endstr) == 1:
                # print('string found in a file', filepath)
                # print('Line Number:', l_no)
                # print('startstr in:', line)
                # result = re.search(startstr+'(.*)'+endstr, line)
                result = find_between(line, startstr, endstr)  # AppOrderformBundle:AccessionType
                print('result=', result)
                # AppOrderformBundle:AccessionType
                x = result.split(":")
                bundle = x[0]
                classname = x[1]
                # User::class
                searchstr = "'" + result + "'"
                replacedstr = classname + "::class"
                print('Replaced: bundle=', bundle, ', classname=', classname,"=> searchstr=" + searchstr + " replacedstr=" + replacedstr + "\n")
                linemodified = origline.replace(searchstr, replacedstr)
                return linemodified;
            else:
                print("Skipped in filepath=" + filepath + "\n" + "line=" + line + "\n" + "Skipped: start/end strings occurred more than 1 time" + "\n")
                # pass
        else:
            # print(filepath + "\n" + "line="+line+"\n"+"Skipped: line commented out")
            pass
    return None

def process_single_file_orig(filepath, startstr, endstr):
    count = 0
    with open(filepath, mode='r', encoding='utf8') as fp:
        for l_no, origline in enumerate(fp):
            line = origline.lstrip()
            #print("\n")
            if startstr in line and endstr in line:
                if line[:2] != '//':
                    if line.count(startstr) == 1 and line.count(endstr) == 1:
                        #print('string found in a file', filepath)
                        #print('Line Number:', l_no)
                        #print('startstr in:', line)
                        #result = re.search(startstr+'(.*)'+endstr, line)
                        result = find_between(line,startstr,endstr) #AppOrderformBundle:AccessionType
                        print('result=', result)
                        #AppOrderformBundle:AccessionType
                        x = result.split(":")
                        bundle = x[0]
                        classname = x[1]
                        #User::class
                        searchstr = "'"+result+"'"
                        replacedstr = classname+"::class"
                        print('bundle=', bundle, ', classname=', classname, "=> searchstr="+searchstr+" replacedstr="+replacedstr+"\n")
                        count = count + 1
                    else:
                        print("Skipped in filepath="+filepath + "\n" + "line=" + line + "\n" + "Skipped: start/end strings occurred more than 1 time"+"\n")
                        #pass
                else:
                    #print(filepath + "\n" + "line="+line+"\n"+"Skipped: line commented out")
                    pass

    print("count=",count," filepath="+filepath)

def getListOfFiles(dirName):
    # create a list of file and sub directories
    # names in the given directory
    listOfFile = os.listdir(dirName)
    allFiles = list()
    # Iterate over all the entries
    for entry in listOfFile:
        # Create full path
        fullPath = os.path.join(dirName, entry)
        # If entry is a directory then get the list of files in this directory
        if os.path.isdir(fullPath):
            allFiles = allFiles + getListOfFiles(fullPath)
        else:
            allFiles.append(fullPath)

    return allFiles

def find_between( s, first, last ):
    try:
        start = s.index( first ) + len( first )
        end = s.index( last, start )
        return s[start:end]
    except ValueError:
        return ""

def copyfiles( source_dir, dest_dir, pattern ):
    files = glob.iglob(os.path.join(source_dir,pattern))
    # print("files=", len(list(files)))
    for file in files:
        # print(file)
        if os.path.isfile(file):
            print(file)
            shutil.copy2(file, dest_dir)

def find_between_r( s, first, last ):
    try:
        start = s.rindex( first ) + len( first )
        end = s.rindex( last, start )
        return s[start:end]
    except ValueError:
        return ""

def runCommand(command):
    print("run: " + command)
    #output = subprocess.run([command], stdout=PIPE, stderr=PIPE, shell=True)
    output = check_output(command, shell=True)
    print(output)
    return output

def help():
    print(
        "Usage: python process.py [OPTIONS]\n" \
        "Example: python process.py --dir DeidentifierBundle --startstr \"getRepository('\" --endstr \"')\" \n" \
        "\n" \
        "-d, --dir              subject directory to process files\n" \
        "-s, --startstr         start string to replace\n" \
        "-e, --endstr           end string to replace\n" \
        "-H, --help             this help"
    )

def main(argv):
    print("\n### process.py "+"###")

    dir = ''
    startstr = ''
    endstr = ''

    try:
        opts, args = getopt.getopt(
            argv,
            "d:s:e:h",
            ["dir=", "startstr=", "endstr=", "help"]
        )
    except getopt.GetoptError:
        print('Parameters error')
        sys.exit(2)

    for opt, arg in opts:
        #print('opt=' + opt + ", arg="+arg)
        if opt in ("-d", "--dir"):
            dir = arg
        elif opt in ("-s", "--startstr"):
            startstr = arg
        elif opt in ("-e", "--endstr"):
            endstr = arg
        elif opt in ("-h", "--help"):
           help()
           #sys.exit()
           return
        else:
            #print('webmonitor.py: invalid option')
            #logging.warning('webmonitor.py: parameter errors')
            help()
            sys.exit(2)

    if dir:
        global DIR
        DIR = dir

    if startstr:
        global FINDSTR
        FINDSTR = startstr

    print('dir=' + dir + ', startstr=' + startstr + ', endstr=' + endstr)

    if dir == '':
        print('Nothing to do: subject directory are not provided')
        return

    if startstr == '':
        print('Nothing to do: startstr is not provided')
        return

    if endstr == '':
        print('Nothing to do: endstr is not provided')
        return

    runCommand('whoami') #testing runCommand

    output = process_files(dir,startstr,endstr)

    print(output)

if __name__ == '__main__':
    #python fellapp.py --dir DeidentifierBundle --startstr "->getRepository('" --endstr "')->"
    #C:\Users\ch3\Documents\MyDocs\WCMC\ORDER\replace-test\DeidentifierBundle
    #python process.py -d ../../orderflex/src/App/TestBundle -s "->getRepository('" -e "')->"
    main(sys.argv[1:])