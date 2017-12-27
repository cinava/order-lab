<?php

namespace Oleg\TranslationalResearchBundle\Controller;

use Oleg\TranslationalResearchBundle\Entity\Invoice;
use Oleg\TranslationalResearchBundle\Entity\TransResRequest;
use Oleg\TranslationalResearchBundle\Form\FilterInvoiceType;
use Oleg\TranslationalResearchBundle\Form\InvoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Oleg\UserdirectoryBundle\Entity\User;

/**
 * Invoice controller.
 *
 * @Route("invoice")
 */
class InvoiceController extends Controller
{
    /**
     * Lists all invoice entities.
     *
     * @Route("/list/{id}", name="translationalresearch_invoice_index")
     * @Route("/list-all/", name="translationalresearch_invoice_index_all")
     * @Route("/list-all-my/", name="translationalresearch_invoice_index_all_my")
     * @Route("/list-all-issued/", name="translationalresearch_invoice_index_all_issued")
     * @Route("/list-all-pending/", name="translationalresearch_invoice_index_all_pending")
     * @Template("OlegTranslationalResearchBundle:Invoice:index.html.twig")
     * @Method("GET")
     */
    public function indexAction(Request $request, TransResRequest $transresRequest=null)
    {
        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $routeName = $request->get('_route');
        $advancedFilter = 0;

        $repository = $em->getRepository('OlegTranslationalResearchBundle:Invoice');
        $dql =  $repository->createQueryBuilder("invoice");
        $dql->select('invoice');

        $dql->leftJoin('invoice.submitter','submitter');
        $dql->leftJoin('invoice.salesperson','salesperson');
        $dql->leftJoin('invoice.transresRequests','transresRequests');
        $dql->leftJoin('invoice.principalInvestigators','principalInvestigators');

        $dqlParameters = array();

        if( $routeName == "translationalresearch_invoice_index_all" ) {
            $title = "List of All Invoices";
        }

        if( $routeName == "translationalresearch_invoice_index_all_my" ) {
            $title = "List of All My Invoices";
        }

        if( $routeName == "translationalresearch_invoice_index_all_issued" ) {
            $title = "List of All Issued Invoices";
        }

        if( $routeName == "translationalresearch_invoice_index_all_pending" ) {
            $title = "List of All Pending Invoices";
        }

        if( $routeName == "translationalresearch_invoice_index" ) {
            $title = "List of Invoices for Request ID ".$transresRequest->getOid();
            $dql->where("transresRequests.id = :transresRequestId");
            $dqlParameters["transresRequestId"] = $transresRequest->getId();
        }

        //////// create filter //////////
        $params = array(
            'routeName'=>$routeName,
            'transresRequest'=>$transresRequest
        );
        $filterform = $this->createForm(FilterInvoiceType::class, null,array(
            'method' => 'GET',
            'form_custom_value'=>$params
        ));

        $filterform->handleRequest($request);
        $submitter = $filterform['submitter']->getData();
        $status = $filterform['status']->getData();
        $principalInvestigators = $filterform['principalInvestigators']->getData();
        $salesperson = $filterform['salesperson']->getData();
        $idSearch = $filterform['idSearch']->getData();
        $totalMin = $filterform['totalMin']->getData();
        $totalMax = $filterform['totalMax']->getData();
        $startDate = $filterform['startDate']->getData();
        $endDate = $filterform['endDate']->getData();
        ////// EOF create filter //////////

        if( $submitter ) {
            $dql->andWhere("submitter.id = :submitterId");
            $dqlParameters["submitterId"] = $submitter->getId();
        }

        if( $status ) {
            $dql->andWhere("invoice.status = :status");
            $dqlParameters["status"] = $status;
        }

        if( $idSearch ) {
            $dql->andWhere("invoice.oid LIKE :idSearch");
            $dqlParameters["idSearch"] = "%".$idSearch."%";
        }

        if( $principalInvestigators && count($principalInvestigators)>0 ) {
            $dql->andWhere("principalInvestigators.id IN (:principalInvestigators)");
            $principalInvestigatorsIdsArr = array();
            foreach($principalInvestigators as $principalInvestigator) {
                $principalInvestigatorsIdsArr[] = $principalInvestigator->getId();
            }
            $dqlParameters["principalInvestigators"] = implode(",",$principalInvestigatorsIdsArr);
            $advancedFilter++;
        }

        if( $startDate ) {
            $dql->andWhere('invoice.dueDate >= :startDate');
            $dqlParameters['startDate'] = $startDate->format('Y-m-d H:i:s');
            $advancedFilter++;
        }
        if( $endDate ) {
            $endDate->modify('+1 day');
            $dql->andWhere('invoice.dueDate <= :endDate');
            $dqlParameters['endDate'] = $endDate->format('Y-m-d H:i:s');
            $advancedFilter++;
        }

        if( $salesperson ) {
            $dql->andWhere("salesperson.id = :salespersonId");
            $dqlParameters["salespersonId"] = $salesperson->getId();
            $advancedFilter++;
        }

        if( $totalMin ) {
            $dql->andWhere('invoice.total >= :totalMin');
            $dqlParameters['totalMin'] = $totalMin;
            $advancedFilter++;
        }

        if( $totalMax ) {
            $dql->andWhere('invoice.total <= :totalMax');
            $dqlParameters['totalMax'] = $totalMax;
            $advancedFilter++;
        }


        $limit = 30;
        $query = $em->createQuery($dql);

        if( count($dqlParameters) > 0 ) {
            $query->setParameters($dqlParameters);
        }

        //echo "query=".$query->getSql()."<br>";

        $paginationParams = array(
            'defaultSortFieldName' => 'invoice.id',
            'defaultSortDirection' => 'DESC'
        );

        $paginator  = $this->get('knp_paginator');
        $invoices = $paginator->paginate(
            $query,
            $request->query->get('page', 1),    /*page number*/
            $limit,                             /*limit per page*/
            $paginationParams
        );

        return array(
            'invoices' => $invoices,
            'transresRequest' => $transresRequest,
            'title' => $title,
            'filterform' => $filterform->createView(),
            'advancedFilter' => $advancedFilter
        );
    }

    /**
     * Creates a new invoice entity.
     *
     * @Route("/new/{id}", name="translationalresearch_invoice_new")
     * @Template("OlegTranslationalResearchBundle:Invoice:new.html.twig")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, TransResRequest $transresRequest)
    {
        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $transresUtil = $this->get('transres_util');
        $transresRequestUtil = $this->get('transres_request_util');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        //$user = null; //testing
        $cycle = "new";

        $invoice = $transresRequestUtil->createNewInvoice($transresRequest,$user);

        $form = $this->createInvoiceForm($invoice,$cycle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //exit('new');

            $msg = $transresRequestUtil->createSubmitNewInvoice($transresRequest,$invoice,$form);

            if( $form->getClickedButton() && 'saveAndSend' === $form->getClickedButton()->getName() ) {
                //TODO: generate and send PDF
            }

            //$msg = "New Invoice has been successfully created for the request ID ".$transresRequest->getOid();

            $this->get('session')->getFlashBag()->add(
                'notice',
                $msg
            );

            return $this->redirectToRoute('translationalresearch_invoice_show', array('id'=>$transresRequest->getId(), 'oid' => $invoice->getOid()));
        }

        return array(
            'transresRequest' => $transresRequest,
            'invoice' => $invoice,
            'form' => $form->createView(),
            'title' => "New Invoice for the Request ID ".$transresRequest->getOid(),
            'cycle' => $cycle
        );
    }

    /**
     * Finds and displays a invoice entity.
     *
     * @Route("/show/{id}/{oid}", name="translationalresearch_invoice_show")
     * @Template("OlegTranslationalResearchBundle:Invoice:new.html.twig")
     * @Method("GET")
     */
    public function showAction(Request $request, TransResRequest $transresRequest, $oid)
    {
        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $invoice = $em->getRepository('OlegTranslationalResearchBundle:Invoice')->findOneByOid($oid);
        if( !$invoice ) {
            throw new \Exception("Invoice is not found by invoice number (oid) '" . $oid . "'");
        }

        $cycle = "show";
        $routeName = $request->get('_route');

        $form = $this->createInvoiceForm($invoice,$cycle);

        //$deleteForm = $this->createDeleteForm($invoice);

        return array(
            'transresRequest' => $transresRequest,
            'invoice' => $invoice,
            'form' => $form->createView(),
            //'delete_form' => $deleteForm->createView(),
            'cycle' => $cycle,
            'title' => "Invoice for the Request ID ".$transresRequest->getOid(),
        );
    }

    /**
     * Displays a form to edit an existing invoice entity.
     *
     * @Route("/edit/{id}/{oid}", name="translationalresearch_invoice_edit")
     * @Template("OlegTranslationalResearchBundle:Invoice:new.html.twig")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TransResRequest $transresRequest, $oid)
    {

        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $transresUtil = $this->get('transres_util');

        $invoice = $em->getRepository('OlegTranslationalResearchBundle:Invoice')->findOneByOid($oid);
        if( !$invoice ) {
            throw new \Exception("Invoice is not found by invoice number (oid) '" . $oid . "'");
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $cycle = "edit";

        //$deleteForm = $this->createDeleteForm($invoice);

        //$editForm = $this->createForm('Oleg\TranslationalResearchBundle\Form\InvoiceType', $invoice);
        $editForm = $this->createInvoiceForm($invoice,$cycle);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            //update user
            $invoice->setUpdateUser($user);

            $this->getDoctrine()->getManager()->flush();

            $msg = "Invoice with ID ".$invoice->getOid()." has been updated.";

            $this->get('session')->getFlashBag()->add(
                'notice',
                $msg
            );

            $eventType = "Invoice Updated";
            $msg = "Invoice with ID ".$invoice->getOid()." has been updated.";
            $transresUtil->setEventLog($invoice,$eventType,$msg);

            return $this->redirectToRoute('translationalresearch_invoice_show', array('id'=>$transresRequest->getId(), 'oid' => $invoice->getOid()));
        }

        return array(
            'transresRequest' => $transresRequest,
            'invoice' => $invoice,
            'form' => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
            'cycle' => $cycle,
            'title' => "Invoice for the Request ID ".$transresRequest->getOid(),
        );
    }

    /**
     * Deletes a invoice entity.
     *
     * @Route("/delete/{id}", name="translationalresearch_invoice_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Invoice $invoice)
    {
        exit("Delete is not allowed.");

        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_ADMIN') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $transresUtil = $this->get('transres_util');

        $form = $this->createDeleteForm($invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $msg = "Invoice with ID ".$invoice->getOid()." has been successfully deleted.";

            $em = $this->getDoctrine()->getManager();
            $em->remove($invoice);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                $msg
            );

            $eventType = "Invoice Deleted";
            $transresUtil->setEventLog($invoice,$eventType,$msg);
        }

        return $this->redirectToRoute('translationalresearch_invoice_index_all');
    }

    /**
     * Generate Invoice PDF
     *
     * @Route("/generate-invoice-pdf/{id}/{oid}", name="translationalresearch_invoice_generate_pdf")
     * @Template("OlegTranslationalResearchBundle:Invoice:new.html.twig")
     * @Method("GET")
     */
    public function generateInvoicePdfAction(Request $request, TransResRequest $transresRequest, $oid) {

        $em = $this->getDoctrine()->getManager();
        $transresPdfUtil = $this->get('transres_pdf_generator');
        $transresRequestUtil = $this->get('transres_request_util');
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $invoice = $em->getRepository('OlegTranslationalResearchBundle:Invoice')->findOneByOid($oid);
        if( !$invoice ) {
            throw new \Exception("Invoice is not found by invoice number (oid) '" . $oid . "'");
        }

        if( false === $transresRequestUtil->isInvoiceBillingContact($invoice,$user) ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $res = $transresPdfUtil->generateInvoicePdf($transresRequest,$invoice,$user);
        
        $filename = $res['filename'];
        $pdf = $res['pdf'];
        $size = $res['size'];

        $msg = "PDF has been created for Invoice ID " . $invoice->getOid() . "; filename=".$filename."; size=".$size;

        //exit("<br><br>".$msg);

        $this->get('session')->getFlashBag()->add(
            'notice',
            $msg
        );

        //return $this->redirectToRoute('translationalresearch_invoice_index_all');
        return $this->redirectToRoute('translationalresearch_invoice_show', array('id'=>$transresRequest->getId(), 'oid' => $invoice->getOid()));

    }

    /**
     * Show PDF version of invoice
     *
     * @Route("/download-invoice-pdf/{id}/{oid}", name="translationalresearch_invoice_download")
     * @Template("OlegTranslationalResearchBundle:Invoice:pdf-show.html.twig")
     * @Method("GET")
     */
    public function downloadPdfAction(Request $request, TransResRequest $transresRequest, $oid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $logger = $this->container->get('logger');
        $routeName = $request->get('_route');
        $userSecUtil = $this->container->get('user_security_utility');

        //download: user or localhost
        //$user = $this->get('security.token_storage')->getToken()->getUser();
        //download link can be accessed by a console as localhost with role IS_AUTHENTICATED_ANONYMOUSLY, so simulate login manually
        if( !($user instanceof User) ) {
            $firewall = 'ldap_translationalresearch_firewall';
            $systemUser = $userSecUtil->findSystemUser();
            if( $systemUser ) {
                $token = new UsernamePasswordToken($systemUser, null, $firewall, $systemUser->getRoles());
                $this->get('security.token_storage')->setToken($token);
                //$this->get('security.token_storage')->setToken($token);
            }
            $logger->notice("Download view: Logged in as systemUser=".$systemUser);
        } else {
            $logger->notice("Download view: Token user is valid security.token_storage user=".$user);
        }

        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_USER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $invoice = $em->getRepository('OlegTranslationalResearchBundle:Invoice')->findOneByOid($oid);
        if( !$invoice ) {
            throw new \Exception("Invoice is not found by invoice number (oid) '" . $oid . "'");
        }

        $cycle = "download";
        //$routeName = $request->get('_route');

        //$form = $this->createInvoiceForm($invoice,$cycle);

        //$deleteForm = $this->createDeleteForm($invoice);

        return array(
            'transresRequest' => $transresRequest,
            'invoice' => $invoice,
            //'form' => $form->createView(),
            //'delete_form' => $deleteForm->createView(),
            'cycle' => $cycle,
            'title' => "Invoice for the Request ID ".$transresRequest->getOid(),
        );
    }

    /**
     * Show the most recent PDF version of invoice
     *
     * @Route("/download-recent-invoice-pdf{id}/{oid}", name="translationalresearch_invoice_download_recent")
     * @Template("OlegTranslationalResearchBundle:Invoice:pdf-show.html.twig")
     * @Method("GET")
     */
    public function downloadRecentPdfAction(Request $request, TransResRequest $transresRequest, $oid)
    {
        if( false === $this->get('security.authorization_checker')->isGranted('ROLE_TRANSRES_BILLING_ADMIN') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $logger = $this->container->get('logger');
        $routeName = $request->get('_route');
        $userSecUtil = $this->container->get('user_security_utility');
        $transresRequestUtil = $this->get('transres_request_util');

        $em = $this->getDoctrine()->getManager();
        $invoice = $em->getRepository('OlegTranslationalResearchBundle:Invoice')->findOneByOid($oid);
        if( !$invoice ) {
            throw new \Exception("Invoice is not found by invoice number (oid) '" . $oid . "'");
        }

        if( false === $transresRequestUtil->isInvoiceBillingContact($invoice,$user) ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('translationalresearch.sitename').'-nopermission') );
        }

        //get the most recent PDF document
        $invoicePDF = $invoice->getRecentPDF();

        if( $invoicePDF ) {

//            $routeName = $request->get('_route');
//            if( $routeName == "fellapp_view_pdf" ) {
//                return $this->redirect( $this->generateUrl('fellapp_file_view',array('id' => $reportDocument->getId())) );
//            } else {
//                return $this->redirect( $this->generateUrl('fellapp_file_download',array('id' => $reportDocument->getId())) );
//            }

            return $this->redirect( $this->generateUrl('translationalresearch_file_view',array('id' => $invoicePDF->getId())) );

        } else {
            $this->get('session')->getFlashBag()->add(
                'warning',
                'Invoice PDF does not exists.'
            );

            return $this->redirectToRoute('translationalresearch_invoice_show', array('id'=>$transresRequest->getId(), 'oid' => $invoice->getOid()));
        }
    }


    /**
     * Creates a form to delete a invoice entity.
     *
     * @param Invoice $invoice The invoice entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Invoice $invoice)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('translationalresearch_invoice_delete', array('id' => $invoice->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function createInvoiceForm( $invoice, $cycle ) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $params = array(
            'cycle' => $cycle,
            'em' => $em,
            'user' => $user,
            'invoice' => $invoice,
            'SecurityAuthChecker' => $this->get('security.authorization_checker'),
        );

        if( $cycle == "new" ) {
            $disabled = false;
        }

        if( $cycle == "show" ) {
            $disabled = true;
        }

        if( $cycle == "edit" ) {
            $disabled = false;
        }

        if( $cycle == "download" ) {
            $disabled = true;
        }

        $form = $this->createForm(InvoiceType::class, $invoice, array(
            'form_custom_value' => $params,
            'disabled' => $disabled,
        ));

        return $form;
    }
}
