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

//include_once('setparameters_function.php');

//$dtz = $this->container->getParameter('default_time_zone');
//echo "dtz=".$dtz."<br>";

$host = $container->getParameter('database_host');
$driver = $container->getParameter('database_driver');
$dbname = $container->getParameter('database_name');
$user = $container->getParameter('database_user');
$password = $container->getParameter('database_password');

//echo "driver=".$driver."<br>";
//echo "host=".$host."<br>";
//echo "dbname=".$dbname."<br>";
//echo "user=".$user."<br>";
//echo "password=".$password."<br>";

$config = new \Doctrine\DBAL\Configuration();

$connectionParams = array(
    'dbname' => $dbname,
    'user' => $user,
    'password' => $password,
    'host' => $host,
    'driver' => $driver,
);
 
//upload paths can't be NULL
$employeesuploadpath = "directory/documents";
$employeesavataruploadpath = "directory/avatars";
$container->setParameter('employees.avataruploadpath',$employeesavataruploadpath);
$container->setParameter('employees.uploadpath',$employeesuploadpath);
//scan
$scanuploadpath = "scan-order/documents";
$container->setParameter('scan.uploadpath',$scanuploadpath);
//fellapp
$fellappuploadpath = "fellapp";
$container->setParameter('fellapp.uploadpath',$fellappuploadpath);
//vacreq
$vacrequploadpath = "vacreq";
$container->setParameter('vacreq.uploadpath',$vacrequploadpath);

$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

//testing
//$connected = $conn->connect();
//echo "connected=".$connected."<br>";
//echo "conn name=".$conn->getName()."<br>"; // connection 1

$table = 'user_siteParameters';

$schemaManager = $conn->getSchemaManager();

if( $conn && $schemaManager->tablesExist(array($table)) == true ) {

    //exit("connected!");
    //echo("table true<br>");

    $sql = "SELECT * FROM ".$table;
    $params = $conn->query($sql); // Simple, but has several drawbacks

    //var_dump($params);
    //echo "count=".count($params)."<br>";

    if( $params && count($params) >= 1 ) {

        $aDLDAPServerAddress = null;
        $aDLDAPServerPort = null;
        $aDLDAPServerOu = null;
        $aDLDAPServerAccountUserName = null;
        $aDLDAPServerAccountPassword = null;
        $ldapExePath = null;
        $ldapExeFilename = null;

        $smtpServerAddress = null;
        $defaultSiteEmail = null;
        $institution_url = null;
        $institution_name = null;
        $subinstitution_url = null;
        $subinstitution_name = null;
        $department_url = null;
        $department_name = null;

        //titles
        $mainhome_title = null;
        $listmanager_title = null;
        $eventlog_title = null;
        $sitesettings_title = null;
        $contentabout_page = null;
        //$underlogin_msg_user = null;
        //$underlogin_msg_scan = null;

        //maintenance
//        $maintenance = null;
//        $maintenanceenddate = null;
//        $maintenanceloginmsg = null;
//        $maintenancelogoutmsg = null;

        //Aperio DB
        $database_host_aperio = null;
        $database_port_aperio = null;
        $database_name_aperio = null;
        $database_user_aperio = null;
        $database_password_aperio = null;

        //set path to binary for knp_snappy
        //$knp_snappy_path = $_SERVER['DOCUMENT_ROOT']."/order/scanorder/Scanorders2/src/Oleg/UserdirectoryBundle/Util/wkhtmltopdf/bin/";
        //$knp_snappy_path = str_replace("/","\\\\",$knp_snappy_path);
        //"\"C:\\Program Files (x86)\\Aperio\\Spectrum\\htdocs\\order\\scanorder\\Scanorders2\\src\\Oleg\\UserdirectoryBundle\\Util\\wkhtmltopdf\\bin\\wkhtmltopdf.exe\""
        //$knp_snappy_path_pdf = '"\"'.$knp_snappy_path.'wkhtmltopdf.exe'.'\""';
        //$knp_snappy_path_image = '"\"'.$knp_snappy_path.'wkhtmltoimage.exe'.'\""';
        //$container->setParameter('knp_snappy.pdf.binary',$knp_snappy_path_pdf);
        //$container->setParameter('knp_snappy.image.binary',$knp_snappy_path_image);
        //echo "knp_snappy.pdf.binary=".$container->getParameter('knp_snappy.pdf.binary')."<br>";

        while( $row = $params->fetch() ) {

            if( array_key_exists('aDLDAPServerAddress', $row) )
                $aDLDAPServerAddress = $row['aDLDAPServerAddress'];
            if( array_key_exists('aDLDAPServerPort', $row) )
                $aDLDAPServerPort = $row['aDLDAPServerPort'];
            if( array_key_exists('aDLDAPServerOu', $row) )
                $aDLDAPServerOu = $row['aDLDAPServerOu'];
            if( array_key_exists('aDLDAPServerAccountUserName', $row) )
                $aDLDAPServerAccountUserName = $row['aDLDAPServerAccountUserName'];
            if( array_key_exists('aDLDAPServerAccountPassword', $row) )
                $aDLDAPServerAccountPassword = $row['aDLDAPServerAccountPassword'];

            if (array_key_exists('ldapExePath', $row)) {
                $ldapExePath = $row['ldapExePath'];
            }
            if (array_key_exists('ldapExeFilename', $row)) {
                $ldapExeFilename = $row['ldapExeFilename'];
            }

            if( array_key_exists('smtpServerAddress', $row) )
                $smtpServerAddress = $row['smtpServerAddress'];

            if( array_key_exists('siteEmail', $row) )
                $defaultSiteEmail = $row['siteEmail'];

            if( array_key_exists('institutionurl', $row) )
                $institution_url = $row['institutionurl'];
            if( array_key_exists('institutionname', $row) )
                $institution_name = $row['institutionname'];
            if( array_key_exists('subinstitutionurl', $row) )
                $subinstitution_url = $row['subinstitutionurl'];
            if( array_key_exists('subinstitutionname', $row) )
                $subinstitution_name = $row['subinstitutionname'];
            if( array_key_exists('departmenturl', $row) )
                $department_url = $row['departmenturl'];
            if( array_key_exists('departmentname', $row) )
                $department_name = $row['departmentname'];

            //employees
            $employeesuploadpath = $row['employeesuploadpath'];
            $employeesavataruploadpath = $row['avataruploadpath'];
            //scan
            $scanuploadpath = $row['scanuploadpath'];
            //fellapp
            if (array_key_exists('fellappuploadpath', $row)) {
                $fellappuploadpath = $row['fellappuploadpath'];
            }
            //vacreq
            if (array_key_exists('vacrequploadpath', $row)) {
                $vacrequploadpath = $row['vacrequploadpath'];
            }

            //titles
            $mainhome_title = $row['mainHomeTitle'];
            $listmanager_title = $row['listManagerTitle'];
            $eventlog_title = $row['eventLogTitle'];
            $sitesettings_title = $row['siteSettingsTitle'];
            $contentabout_page = $row['contentAboutPage'];
            //$underlogin_msg_user = $row['underLoginMsgUser'];
            //$underlogin_msg_scan = $row['underLoginMsgScan'];
            //echo "mainhome_title=".$mainhome_title."<br>";

//            $maintenance = $row['maintenance'];
//            $maintenanceenddate = $row['maintenanceenddate'];
//            $maintenanceloginmsg = $row['maintenanceloginmsg'];
//            $maintenancelogoutmsg = $row['maintenancelogoutmsg'];
            //echo "department_url=".$department_url."<br>";

            //Symfony DB
            $database_host = $row['dbServerAddress'];
            $database_port = $row['dbServerPort'];
            $database_name = $row['dbDatabaseName'];
            $database_user = $row['dbServerAccountUserName'];
            $database_password = $row['dbServerAccountPassword'];

            //Aperio DB
            $database_host_aperio = $row['aperioeSlideManagerDBServerAddress'];
            $database_port_aperio = $row['aperioeSlideManagerDBServerPort'];
            $database_name_aperio = $row['aperioeSlideManagerDBName'];
            $database_user_aperio = $row['aperioeSlideManagerDBUserName'];
            $database_password_aperio = $row['aperioeSlideManagerDBPassword'];
        }

        $container->setParameter('mailer_host',$smtpServerAddress);
        $container->setParameter('default_system_email',$defaultSiteEmail);

        //footer params
        $container->setParameter('institution_url',$institution_url);
        $container->setParameter('institution_name',$institution_name);
        $container->setParameter('subinstitution_url',$subinstitution_url);
        $container->setParameter('subinstitution_name',$subinstitution_name);
        $container->setParameter('department_url',$department_url);
        $container->setParameter('department_name',$department_name);

        //uploads
        $container->setParameter('employees.avataruploadpath',$employeesavataruploadpath);
        $container->setParameter('employees.uploadpath',$employeesuploadpath);
        $container->setParameter('scan.uploadpath',$scanuploadpath);
        if( $fellappuploadpath )
            $container->setParameter('fellapp.uploadpath',$fellappuploadpath);
        if( $vacrequploadpath )
            $container->setParameter('vacreq.uploadpath',$vacrequploadpath);

        //titles
        $mainhome_title = str_replace("%","%%",$mainhome_title);
        $container->setParameter('mainhome_title',$mainhome_title);
        $listmanager_title = str_replace("%","%%",$listmanager_title);
        $container->setParameter('listmanager_title',$listmanager_title);
        $eventlog_title = str_replace("%","%%",$eventlog_title);
        $container->setParameter('eventlog_title',$eventlog_title);
        $sitesettings_title = str_replace("%","%%",$sitesettings_title);
        $container->setParameter('sitesettings_title',$sitesettings_title);

        //The percent sign inside a parameter or argument, as part of the string, must be escaped with another percent sign: % -> %%
        $contentabout_page = str_replace("%","%%",$contentabout_page);
        $container->setParameter('contentabout_page',$contentabout_page);

        //ldap
        if( $aDLDAPServerAddress )
            $container->setParameter('ldaphost',$aDLDAPServerAddress);
        if( $aDLDAPServerPort )
            $container->setParameter('ldapport',$aDLDAPServerPort);
        if( $aDLDAPServerAccountUserName )
            $container->setParameter('ldapusername',$aDLDAPServerAccountUserName);
        if( $aDLDAPServerAccountPassword )
            $container->setParameter('ldappassword',$aDLDAPServerAccountPassword);
        if( $aDLDAPServerOu )
            $container->setParameter('ldapou',$aDLDAPServerOu);
        if( $ldapExePath )
            $container->setParameter('ldapexepath',$ldapExePath);
        if( $ldapExeFilename )
            $container->setParameter('ldapexefilename',$ldapExeFilename);

        //maintenance
//        $container->setParameter('maintenance',$maintenance);
//        $container->setParameter('maintenanceenddate',$maintenanceenddate);
//        $container->setParameter('maintenanceloginmsg',$maintenanceloginmsg);
//        $container->setParameter('maintenancelogoutmsg',$maintenancelogoutmsg);
        //echo "maint=".$this->container->getParameter('maintenance')."<br>";
        //echo "department_url=".$department_url."<br>";
        //echo "container department_url=".$this->container->getParameter('department_url')."<br>";

        //TODO: assign a new parameters for DB does not work
        //Symfony DB
//        echo "database_host=[".$database_host."]<br>";
//        echo "database_port=[".$database_port."]<br>";
//        echo "database_name=[".$database_name."]<br>";
//        echo "database_user=[".$database_user."]<br>";
//        echo "database_password=[".$database_password."]<br>";

//        if( $database_host )
//            $container->setParameter('database_host',trim($database_host));
//        if( $database_port )
//            $container->setParameter('database_port',trim($database_port));
//        if( $database_name )
//            $container->setParameter('database_name',trim($database_name));
//        if( $database_user )
//            $container->setParameter('database_user',trim($database_user));
//        if( $database_password )
//            $container->setParameter('database_password',$database_password);

        //Aperio DB
//        echo "database_host_aperio=[".$database_host_aperio."]<br>";
//        echo "database_port_aperio=[".$database_port_aperio."]<br>";
//        echo "database_name_aperio=[".$database_name_aperio."]<br>";
//        echo "database_user_aperio=[".$database_user_aperio."]<br>";
//        echo "database_password_aperio=[".$database_password_aperio."]<br>";

//        if( $database_host_aperio )
//            $container->setParameter('database_host_aperio',trim($database_host_aperio));
//        if( $database_port_aperio )
//            $container->setParameter('database_port_aperio',trim($database_port_aperio));
//        if( $database_name_aperio )
//            $container->setParameter('database_name_aperio',trim($database_name_aperio));
//        if( $database_user_aperio )
//            $container->setParameter('database_user_aperio',trim($database_user_aperio));
//        if( $database_password_aperio )
//            $container->setParameter('database_password_aperio',trim($database_password_aperio));

    }//if param


} else {
    //exit("table false<br>");
    //echo("table false<br>");
}


