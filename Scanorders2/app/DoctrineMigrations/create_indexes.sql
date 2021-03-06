ALTER TABLE transres_invoice RENAME COLUMN oid00000 TO oid;
ALTER TABLE scan_message RENAME COLUMN oid00000 TO oid;
ALTER TABLE transres_project RENAME COLUMN oid00000 TO oid;
ALTER TABLE transres_request RENAME COLUMN oid00000 TO oid;

#Unique indexes
CREATE INDEX idx_ea22b84e63b59e5c
    ON public.scan_calllogentrymessage_patientlist USING btree
    (calllogentrymessage_id ASC NULLS LAST)
    TABLESPACE pg_default;

CREATE INDEX idx_d312ef9263b59e5c
    ON public.scan_calllogentrymessage_entrytag USING btree
    (calllogentrymessage_id ASC NULLS LAST)
    TABLESPACE pg_default;

CREATE INDEX idx_d267b39c33f7837
    ON public.calllog_calllogentrymessage_document USING btree
    (document_id ASC NULLS LAST)
    TABLESPACE pg_default;

CREATE INDEX idx_7f071c8fd6e2fadc
    ON public.scan_message_encounter USING btree
    (encounter_id ASC NULLS LAST)
    TABLESPACE pg_default;

CREATE INDEX idx_cf568530a40f1370
    ON public.scan_message_accession USING btree
    (accession_id ASC NULLS LAST)
    TABLESPACE pg_default;

CREATE INDEX idx_5eb89a4de9ed820c
    ON public.scan_message_block USING btree
    (block_id ASC NULLS LAST)
    TABLESPACE pg_default;

CREATE INDEX idx_6be23a97726d9566
    ON public.scan_messagecategory_formnode USING btree
    (formnode_id ASC NULLS LAST)
    TABLESPACE pg_default;


#Update siteparameters
UPDATE public.user_siteparameters
	SET
	id=?, maxidletime=?, environment=?, siteemail=?, dbserveraddress=?,
	dbserverport=?, dbserveraccountusername=?, dbserveraccountpassword=?, dbdatabasename=?, smtpserveraddress=?,
	adldapserveraddress=?, adldapserverport=?, adldapserverou=?, adldapserveraccountusername=?, adldapserveraccountpassword=?,
	ldapexepath=?, ldapexefilename=?, institutionurl=?, institutionname=?, departmenturl=?, departmentname=?,
	maintenance=?, maintenanceenddate=?, maintenancelogoutmsg=?, maintenanceloginmsg=?, scanuploadpath=?,
	employeesuploadpath=?, avataruploadpath=?, fellappuploadpath=?, mainhometitle=?, listmanagertitle=?,
	eventlogtitle=?, sitesettingstitle=?, contentaboutpage=?, underloginmsguser=?, underloginmsgscan=?,
	allowpopulatefellapp=?, autoassigninstitution_id=?, confirmationsubjectfellapp=?, confirmationbodyfellapp=?,
	confirmationemailfellapp=?, lisname=?, lisversion=?, lisnametest=?, lisversiontest=?, lisnamedevelopment=?,
	lisversiondevelopment=?, clientemailfellapp=?, p12keypathfellapp=?, userimpersonateemailfellapp=?, templateidfellapp=?,
	localinstitutionfellapp=?, deleteimportedaplicationsfellapp=?, deleteoldaplicationsfellapp=?, yearsoldaplicationsfellapp=?,
	spreadsheetspathfellapp=?, applicantsuploadpathfellapp=?, reportsuploadpathfellapp=?, applicationpagelinkfellapp=?,
	codegoogleformfellapp=?, pdftkpathfellapp=?, gspathfellapp=?, googledriveapiurlfellapp=?, libreofficeconverttopdfpathfellapp=?,
	libreofficeconverttopdffilenamefellapp=?, libreofficeconverttopdfargumentsdfellapp=?, pdftkfilenamefellapp=?, pdftkargumentsfellapp=?,
	gsfilenamefellapp=?, gsargumentsfellapp=?, backupfileidfellapp=?, folderidfellapp=?, backupupdatedatetimefellapp=?, vacrequploadpath=?,
	academicyearstart=?, academicyearend=?, holidaysurl=?, vacationaccrueddayspermonth=?, livesiterooturl=?, subinstitutionurl=?,
	subinstitutionname=?, enablemetaphone=?, pathmetaphone=?, calllogresources=?, logininstruction=?, initialconfigurationcompleted=?,
	libreofficeconverttopdfpathfellapplinux=?, libreofficeconverttopdffilenamefellapplinux=?, libreofficeconverttopdfargumentsdfellapplinux=?,
	pdftkpathfellapplinux=?, pdftkfilenamefellapplinux=?, pdftkargumentsfellapplinux=?, gspathfellapplinux=?, gsfilenamefellapplinux=?,
	gsargumentsfellapplinux=?, networkdrivepath=?, enableautoassignmentinstitutionalscope=?, transresuploadpath=?, captchaenabled=?,
	permittedfailedloginattempt=?, noticeattemptingpasswordresetldap=?, noticesignupnocwid=?, noticehasldapaccount=?, noticeldapname=?,
	captchasitekey=?, captchasecretkey=?, calllogsiteparameter_id=?, mailertransport=?, mailerauthmode=?, mailerusesecureconnection=?,
	maileruser=?, mailerpassword=?, mailerport=?, mailerspool=?, mailerflushqueuefrequency=?, mailerdeliveryaddresses=?,
	pacsvendorslidemanagerdbserveraddress=?, pacsvendorslidemanagerdbserverport=?, pacsvendorslidemanagerdbusername=?,
	pacsvendorslidemanagerdbpassword=?, pacsvendorslidemanagerdbname=?, showcopyrightonfooter=?, lisdbserveraddress=?,
	lisdbserverport=?, lisdbaccountusername=?, lisdbaccountpassword=?, lisdbname=?, lisdbserveraddresstest=?,
	lisdbserverporttest=?, lisdbaccountusernametest=?, lisdbaccountpasswordtest=?, lisdbnametest=?, lisdbserveraddressdevelopment=?,
	lisdbserverportdevelopment=?, lisdbaccountusernamedevelopment=?, lisdbaccountpassworddevelopment=?, lisdbnamedevelopment=?,
	defaultprimarypublicuseridtype_id=?, navbarfilterinstitution1_id=?, navbarfilterinstitution2_id=?, defaultdeidentifieraccessiontype_id=?,
	defaultscanaccessiontype_id=?, defaultscanmrntype_id=?, defaultscandelivery_id=?, defaultorganizationrecipient_id=?, defaultscanner_id=?,
	wkhtmltopdfpath=?, wkhtmltopdfpathlinux=?, phantomjs=?, phantomjslinux=?, rasterize=?, rasterizelinux=?, connectionchannel=?,
	transresprojectselectionnote=?, ldapall=?, adldapserveraddress2=?, adldapserverport2=?, adldapserverou2=?, adldapserveraccountusername2=?,
	adldapserveraccountpassword2=?, ldapexepath2=?, ldapexefilename2=?, ldapmapperemail=?, ldapmapperemail2=?, transresdashboardinstitution_id=?,
	transreshumansubjectname=?, transresanimalsubjectname=?, ldapmapperprimarypublicuseridtype_id=?, ldapmapperprimarypublicuseridtype2_id=?,
	emailcriticalerror=?, restartservererrorcounter=?, reclettersaltfellapp=?, configfilefolderidfellapp=?, sendemailuploadletterfellapp=?,
	identificationuploadletterfellapp=?, fellappsiteparameter_id=?, callloguploadpath=?
	WHERE <condition>;


UPDATE public.user_siteparameters SET connectionchannel='http';
UPDATE public.user_siteparameters SET emailcriticalerror=false;
UPDATE public.user_siteparameters SET p12keypathfellapp='/opt/order-lab/Scanorders2/src/App/FellAppBundle/Util/FellowshipApplication-f1d9f98353e5.p12';

UPDATE public.user_siteparameters SET pdftkfilenamefellapp='/usr/bin';
UPDATE public.user_siteparameters SET libreofficeconverttopdffilenamefellapplinux='soffice';
UPDATE public.user_siteparameters SET libreofficeconverttopdfargumentsdfellapplinux='--headless -convert-to pdf -outdir';
UPDATE public.user_siteparameters SET pdftkpathfellapplinux='/usr/bin';
UPDATE public.user_siteparameters SET pdftkfilenamefellapplinux='pdftk';
UPDATE public.user_siteparameters SET pdftkargumentsfellapplinux='###inputFiles### cat output ###outputFile### dont_ask';
UPDATE public.user_siteparameters SET gspathfellapplinux='/usr/bin';
UPDATE public.user_siteparameters SET gsfilenamefellapplinux='ghostscript';
UPDATE public.user_siteparameters SET gsArgumentsFellAppLinux='-q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=###outputFile### -c .setpdfwrite -f ###inputFiles###';
UPDATE public.user_siteparameters SET wkhtmltopdfpathLinux='/usr/bin/xvfb-run wkhtmltopdf';
UPDATE public.user_siteparameters SET phantomjsLinux='/opt/phantomjs-2.1.1-linux-x86_64/bin/phantomjs';
UPDATE public.user_siteparameters SET rasterizeLinux='/opt/order-lab/Scanorders2/vendor/olegutil/phantomjs/rasterize.js';
UPDATE public.user_siteparameters SET pathMetaphone='/opt/order-lab/Scanorders2/vendor/olegutil/Metaphone3/metaphone3.php';
UPDATE public.user_siteparameters SET networkDrivePath='';
UPDATE public.user_siteparameters SET libreOfficeConvertToPDFPathFellAppLinux='/usr/bin';

UPDATE public.user_siteparameters SET allowpopulatefellapp=false;
UPDATE public.user_siteparameters SET mailerspool=false;
UPDATE public.user_siteparameters SET environment='test';


UPDATE public.user_siteparameters SET connectionChannel='http';
UPDATE public.user_siteparameters SET connectionChannel='http';
UPDATE public.user_siteparameters SET connectionChannel='http';

user_organizationalgroupdefault_permittedinstitutionalphiscope (63 chars max)
user_organizationalGroupDefault_permittedInstitutionalPHIScope


//Do not import these not existing tables:
$this->processSql('DROP TABLE scan_accountrequest');
$this->processSql('DROP TABLE scan_collaboration');
$this->processSql('DROP TABLE scan_accountrequest_institution');
$this->processSql('DROP TABLE scan_systemaccountrequesttype');

$this->processSql('DROP TABLE transres_invoiceadditem');
$this->processSql('DROP TABLE transres_project_billingcontact');
$this->processSql('DROP TABLE transres_request_invoice');

$this->processSql('DROP TABLE user_collaboration');
$this->processSql('DROP TABLE user_collaboration_institution');

$this->processSql('DROP TABLE vacreq_request_availability');
$this->processSql('DROP TABLE vacreq_availabilitylist');

Cannot create foreign key:ERROR:  relation "scan_collaboration" does not exist

