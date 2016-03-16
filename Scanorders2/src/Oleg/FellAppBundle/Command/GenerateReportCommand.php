<?php
/**
 * Created by PhpStorm.
 * User: DevServer
 * Date: 8/20/15
 * Time: 2:33 PM
 */

namespace Oleg\FellAppBundle\Command;


use Oleg\FellAppBundle\Util\ReportGeneratorManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class GenerateReportCommand extends ContainerAwareCommand {


    protected function configure() {
        $this
            ->setName('fellapp:generatereport')
            ->setDescription('Import and Populate Fellowship Applications from Google Form')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Fellowship Application id'
            )
            ;
    }

    //php app/console fellapp:generatereport fellappid
    protected function execute(InputInterface $input, OutputInterface $output) {

        $id = $input->getArgument('id');

        $fellappRepGen = $this->getContainer()->get('fellapp_reportgenerator');
        
     if(1) {             
        $res = $fellappRepGen->generateFellAppReport( $id );
     } else {
        //testing
        $reportsUploadPathFellApp = "Reports";
        //$userUtil = new UserUtil();
        //$reportsUploadPathFellApp = $userUtil->getSiteSetting($this->em,'reportsUploadPathFellApp');
        $uploadReportPath = 'Uploaded/' . $this->getContainer()->getParameter('fellapp.uploadpath').'/'.$reportsUploadPathFellApp;
        $reportPath = $this->getContainer()->get('kernel')->getRootDir() . '/../web/' . $uploadReportPath.'/';
        $outdir = $reportPath.'temp_'.$id.'/';
        $applicationFilePath = $outdir . "application_ID" . $id . ".pdf";
        $res = $fellappRepGen->generateApplicationPdf($id,$applicationFilePath);
    }
        
        $output->writeln($res);

    }


} 