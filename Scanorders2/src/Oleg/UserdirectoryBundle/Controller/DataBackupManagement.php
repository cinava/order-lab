<?php
/**
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 6/29/2017
 * Time: 11:23 AM
 */

namespace Oleg\UserdirectoryBundle\Controller;


use Doctrine\DBAL\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DataBackupManagement extends Controller
{

    /**
     * Resources:
     * https://blogs.msdn.microsoft.com/brian_swan/2010/07/01/restoring-a-sql-server-database-from-php/
     * https://channaly.wordpress.com/2012/01/31/backup-and-restoring-mssql-database-with-php/
     * https://blogs.msdn.microsoft.com/brian_swan/2010/04/06/backup-and-restore-a-database-with-the-sql-server-driver-for-php/
     * Bundle (no MSSQL): https://github.com/dizda/CloudBackupBundle
     *
     * Table specific backup/restore:
     * http://www.php-mysql-tutorial.com/wikis/mysql-tutorials/using-php-to-backup-mysql-databases.aspx
     * https://www.phpclasses.org/package/5761-PHP-Dump-a-Microsoft-SQL-server-database.html#view_files/files/29084
     *
     * @Route("/data-backup-management/", name="employees_data_backup_management")
     * @Template("OlegUserdirectoryBundle:DataBackup:data_backup_management.html.twig")
     * @Method("GET")
     */
    public function dataBackupManagementAction(Request $request) {

        if( false === $this->get('security.context')->isGranted('ROLE_PLATFORM_ADMIN') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        //networkDrivePath
        $userSecUtil = $this->container->get('user_security_utility');
        $networkDrivePath = $userSecUtil->getSiteSettingParameter('networkDrivePath');
        if( !$networkDrivePath ) {
            //exit("No networkDrivePath is defined");
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                "Cannot continue with Backup: No Network Drive Path is defined in the Site Settings"
            );
            return $this->redirect($this->generateUrl('employees_data_backup_management'));
        }

        $sitename = "employees";

        //get backup files
        $backupFiles = $this->getBackupFiles($networkDrivePath);

        return array(
            'sitename' => $sitename,
            'title' => "Data Backup Management",
            'cycle' => 'new',
            'networkDrivePath' => $networkDrivePath,
            'backupFiles' => $backupFiles
        );
    }


    /**
     * //@Template("OlegUserdirectoryBundle:DataBackup:create_backup.html.twig")
     *
     * @Route("/create-backup/", name="employees_create_backup")
     * @Template("OlegUserdirectoryBundle:DataBackup:data_backup_management.html.twig")
     * @Method("GET")
     */
    public function createBackupAction(Request $request) {

        if( false === $this->get('security.context')->isGranted('ROLE_PLATFORM_ADMIN') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        //networkDrivePath
        $userSecUtil = $this->container->get('user_security_utility');
        $networkDrivePath = $userSecUtil->getSiteSettingParameter('networkDrivePath');
        if( !$networkDrivePath ) {
            //exit("No networkDrivePath is defined");
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                "Cannot continue with Backup: No Network Drive Path is defined in the Site Settings"
            );
            return $this->redirect($this->generateUrl('employees_data_backup_management'));
        }

        $em = $this->getDoctrine()->getManager();
        $sitename = "employees";


        if( $networkDrivePath ) {

            //create backup
            $backupfile = "c:\\backup\\test.bak";
            $res = $this->creatingBackupSQL($backupfile);

            $this->get('session')->getFlashBag()->add(
                'notice',
                $res
            );

            return $this->redirect($this->generateUrl('employees_data_backup_management'));
        }


        $this->get('session')->getFlashBag()->add(
            'pnotify-error',
            "Error backup"
        );

        return $this->redirect($this->generateUrl('employees_data_backup_management'));
//        return array(
//            //'form' => $form->createView(),
//            'sitename' => $sitename,
//            'title' => "Create Backup",
//            'cycle' => 'new'
//        );
    }


    /**
     * @Route("/restore-backup/{backupFilePath}", name="employees_restore_backup", options={"expose"=true})
     * @Template("OlegUserdirectoryBundle:DataBackup:data_backup_management.html.twig")
     * @Method("GET")
     */
    public function restoreBackupAction( Request $request, $backupFilePath ) {

        if( false === $this->get('security.context')->isGranted('ROLE_PLATFORM_ADMIN') ) {
            return $this->redirect( $this->generateUrl('employees-nopermission') );
        }

        //networkDrivePath
        $userSecUtil = $this->container->get('user_security_utility');
        $networkDrivePath = $userSecUtil->getSiteSettingParameter('networkDrivePath');
        if( !$networkDrivePath ) {
            //exit("No networkDrivePath is defined");
            $this->get('session')->getFlashBag()->add(
                'pnotify-error',
                "Cannot continue with Backup: No Network Drive Path is defined in the Site Settings"
            );
            return $this->redirect($this->generateUrl('employees_data_backup_management'));
        }

        echo "backupFilePath=".$backupFilePath."<br>";

        //get backup files
        $backupFiles = $this->getBackupFiles($networkDrivePath);

        $sitename = "employees";

        if( $backupFilePath ) {

            //create backup

            $this->get('session')->getFlashBag()->add(
                'pnotify',
                "DB has been restored by backup ".$backupFilePath
            );

            return $this->redirect($this->generateUrl('employees_data_backup_management'));
        }

        return array(
            'sitename' => $sitename,
            'title' => "Data Backup Management",
            'cycle' => 'new',
            'networkDrivePath' => $networkDrivePath,
            'backupFiles' => $backupFiles
        );
    }


    public function getBackupFiles( $networkDrivePath ) {
        if( !$networkDrivePath ) {
            return null;
        }

        $file0 = array("id"=>null,"name"=>"");
        $file1 = array("id"=>1,"name"=>"file 1");
        $file2 = array("id"=>2,"name"=>"file 2");
        $backupFiles = array($file0,$file1,$file2);

        return $backupFiles;
    }

    public function getConnection() {
//        $dbname = "ScanOrder";
//        $uid = "symfony2";
//        $pwd = "symfony2";
//        $host = "127.0.0.1";
//        $driver = "pdo_sqlsrv";

        $dbname = $this->getParameter('database_name');
        $uid = $this->getParameter('database_user');
        $pwd = $this->getParameter('database_password');
        $host = $this->getParameter('database_host');
        $driver = $this->getParameter('database_driver');
        echo "driver=".$driver."<br>";
        //$pwd = $pwd."1";

        if( 1 ) {
            $connOptions = array("Database"=>$dbname, "UID"=>$uid, "PWD"=>$pwd);
            $conn = sqlsrv_connect("COLLAGE", $connOptions);

            //testing
//            $sql = "SELECT * FROM user_siteParameters";
//            echo "sql=".$sql."<br>";
//            $params = sqlsrv_query($conn, $sql);
//            $res = $params->fetch();
//            echo "env=".$res['environment']."<br>";
        }

        if( 0 ) {
            $config = new \Doctrine\DBAL\Configuration();
            $connectionParams = array(
                'dbname' => $dbname,
                'user' => $uid,
                'password' => $pwd,
                'host' => $host,
                'driver' => $driver,
            );
            $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

            //testing
            $sql = "SELECT * FROM user_siteParameters";
            echo "sql=".$sql."<br>";
            $params = $conn->query($sql); // Simple, but has several drawbacks
            $res = $params->fetch();
            echo "env=".$res['environment']."<br>";
        }

        if( $conn ) {
            echo "Connection established.<br />";
        }else{
            echo "Connection could not be established.<br />";
            die( print_r( sqlsrv_errors(), true));
        }



        return $conn;

        //$em = $this->getDoctrine()->getManager();
        //return $em->getConnection();
    }

    //SQL Server Database backup
    public function creatingBackupSQL( $backupfile ) {
        $msg = null;
        $conn = $this->getConnection();
        $dbname = $this->getParameter('database_name');
        echo "dbname=".$dbname."<br>";

        $backupfile = "testbackup.bak";
        $backupfile = "c:\\backup\\testbackup.bak";

        //$em = $this->getDoctrine()->getManager();
        sqlsrv_configure( "WarningsReturnAsErrors", 0 );
        $sql = "BACKUP DATABASE $dbname TO DISK = '".$backupfile."'";

        //$sql = "SELECT name FROM scan_stainlist";
        //$sql = "SELECT * FROM user_siteParameters";

        echo "FULL sql=".$sql."<br>";

//        $params['backupfile'] = $backupfile;
//        $query = $em->getConnection()->prepare($sql);
//        $res = $query->execute($params);
//        echo "res=".$res."<br>";

        $stmt = sqlsrv_query($conn, $sql);
        //$stmt = $conn->query($sql);

        if($stmt === false)
        {
            die(print_r(sqlsrv_errors(),true));
        }
        else
        {
            $msg = "Database backed up to $backupfile; stmt=".$stmt;
            echo $msg."<br>";
        }


        //Backup log. Put DB into “Restoring…” state.
        $backupfileLog = "c:\\backup\\testbackupLog.bak";
        $sql = "BACKUP LOG $dbname TO DISK = '".$backupfileLog."' WITH NORECOVERY";
        echo "LOG sql=".$sql."<br>";
        $stmt = sqlsrv_query($conn, $sql);
        if($stmt === false)
        {
            die(print_r(sqlsrv_errors()));
        }
        else
        {
            $msgLog = "Transaction log backed up to $backupfileLog";
            $msg = $msg . " <br> " . $msgLog;
            echo $msgLog;
        }

        return $msg;
    }
}