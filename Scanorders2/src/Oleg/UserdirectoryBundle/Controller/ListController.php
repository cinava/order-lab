<?php

namespace Oleg\UserdirectoryBundle\Controller;

use Oleg\UserdirectoryBundle\Entity\CompositeNodeInterface;
use Oleg\UserdirectoryBundle\Entity\Permission;
use Oleg\UserdirectoryBundle\Form\ListFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

use Oleg\UserdirectoryBundle\Form\GenericListType;
use Oleg\UserdirectoryBundle\Util\ErrorHelper;


/**
 * Common list controller
 * @Route("/admin/list")
 */
class ListController extends Controller
{

    /**
     * Lists all entities.
     *
     * @Route("/source-systems/", name="sourcesystems-list")
     * @Route("/roles/", name="role-list")
     * @Route("/institutions/", name="institutions-list", options={"expose"=true})
     * @Route("/states/", name="states-list")
     * @Route("/countries/", name="countries-list")
     * @Route("/board-certifications/", name="boardcertifications-list")
     * @Route("/employment-termination-reasons/", name="employmentterminations-list")
     * @Route("/event-log-event-types/", name="loggereventtypes-list")
     * @Route("/primary-public-user-id-types/", name="usernametypes-list")
     * @Route("/identifier-types/", name="identifiers-list")
     * @Route("/residency-tracks/", name="residencytracks-list")
     * @Route("/fellowship-types/", name="fellowshiptypes-list")
//     * @Route("/research-labs/", name="researchlabs-list")
     * @Route("/location-types/", name="locationtypes-list")
     * @Route("/equipment/", name="equipments-list")
     * @Route("/equipment-types/", name="equipmenttypes-list")
     * @Route("/location-privacy-types/", name="locationprivacy-list")
     * @Route("/role-attributes/", name="roleattributes-list")
     * @Route("/buidlings/", name="buildings-list")
     * @Route("/rooms/", name="rooms-list")
     * @Route("/suites/", name="suites-list")
     * @Route("/floors/", name="floors-list")
     * @Route("/mailboxes/", name="mailboxes-list")
     * @Route("/percent-effort/", name="efforts-list")
     * @Route("/administrative-titles/", name="admintitles-list")
     * @Route("/academic-appointment-titles/", name="apptitles-list")
     * @Route("/training-completion-reasons/", name="completionreasons-list")
     * @Route("/training-degrees/", name="trainingdegrees-list")
     * @Route("/training-majors/", name="trainingmajors-list")
     * @Route("/training-minors/", name="trainingminors-list")
     * @Route("/training-honors/", name="traininghonors-list")
     * @Route("/fellowship-titles/", name="fellowshiptitles-list")
     * @Route("/residency-specialties/", name="residencyspecialtys-list")
     * @Route("/fellowship-subspecialties/", name="fellowshipsubspecialtys-list")
     * @Route("/institution-types/", name="institutiontypes-list")
     * @Route("/document-types/", name="documenttypes-list")
     * @Route("/medical-titles/", name="medicaltitles-list")
     * @Route("/medical-specialties/", name="medicalspecialties-list")
     * @Route("/employment-types/", name="employmenttypes-list")
     * @Route("/grant-source-organizations/", name="sourceorganizations-list")
     * @Route("/languages/", name="languages-list")
     * @Route("/locales/", name="locales-list")
     * @Route("/ranks-of-importance/", name="importances-list")
     * @Route("/authorship-roles/", name="authorshiproles-list")
     * @Route("/lecture-venues/", name="organizations-list")
     * @Route("/cities/", name="cities-list")
     * @Route("/link-types/", name="linktypes-list")
     * @Route("/sexes/", name="sexes-list")
     * @Route("/position-types/", name="positiontypes-list")
     * @Route("/organizational-group-types/", name="organizationalgrouptypes-list")
     * @Route("/profile-comment-group-types/", name="commentgrouptypes-list")
     * @Route("/comment-types/", name="commenttypes-list", options={"expose"=true})
     * @Route("/user-wrappers/", name="userwrappers-list")
     * @Route("/spot-purposes/", name="spotpurposes-list")
     * @Route("/medical-license-statuses/", name="medicalstatuses-list")
     * @Route("/certifying-board-organizations/", name="certifyingboardorganizations-list")
     * @Route("/training-types/", name="trainingtypes-list")
     * @Route("/job-titles/", name="joblists-list")
     * @Route("/fellowship-application-statuses/", name="fellappstatuses-list")
     * @Route("/fellowship-application-ranks/", name="fellappranks-list")
     * @Route("/fellowship-application-language-proficiencies/", name="fellapplanguageproficiency-list")
//     * @Route("/collaborations/", name="collaborations-list")
     * @Route("/collaboration-types/", name="collaborationtypes-list")
     * @Route("/permissions/", name="permission-list")
     * @Route("/permission-objects/", name="permissionobject-list")
     * @Route("/permission-actions/", name="permissionaction-list")
     * @Route("/sites/", name="sites-list")
     * @Route("/event-object-types/", name="eventobjecttypes-list")
     * @Route("/vacreq-availabilities/", name="vacreqavailabilities-list")
     *
     *
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:ListForm:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_OBSERVER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        }

        return $this->getList($request);
    }
    public function getList($request, $limit=50) {

        $type = $request->get('_route');

        //get object name: stain-list => stain
        $pieces = explode("-", $type);
        $pathbase = $pieces[0];

        $mapper= $this->classListMapper($pathbase);

        $repository = $this->getDoctrine()->getRepository($mapper['bundleName'].':'.$mapper['className']);
        $dql =  $repository->createQueryBuilder("ent");
        $dql->select('ent');
        $dql->groupBy('ent');

        $dql->leftJoin("ent.creator", "creator");
        $dql->leftJoin("ent.updatedby", "updatedby");

        $dql->addGroupBy('creator.username');
        $dql->addGroupBy('updatedby.username');

        $entityClass = $mapper['fullClassName'];   //"Oleg\\OrderformBundle\\Entity\\".$mapper['className'];

        //synonyms and original
        $dql->leftJoin("ent.synonyms", "synonyms");
        $dql->addGroupBy('synonyms.name');
        $dql->leftJoin("ent.original", "original");
        $dql->addGroupBy('original.name');

//        if( method_exists($entityClass,'getResearchlab') ) {
//            $dql->leftJoin("ent.researchlab", "researchlab");
//            $dql->leftJoin("researchlab.user", "user");
//            $dql->addSelect('COUNT(user) AS HIDDEN usercount');
//        }

        if( method_exists($entityClass,'getParent') ) {
            $dql->leftJoin("ent.parent", "parent");
            $dql->addGroupBy('parent.name');
        }

        if( method_exists($entityClass,'getOrganizationalGroupType') ) {
            $dql->leftJoin("ent.organizationalGroupType", "organizationalGroupType");
            $dql->addGroupBy('organizationalGroupType.name');
        }

        if( method_exists($entityClass,'getRoles') ) {
            $dql->leftJoin("ent.roles", "roles");
            $dql->addGroupBy('roles.name');
        }

        if( method_exists($entityClass,'getAttributes') ) {
            $dql->leftJoin("ent.attributes", "attributes");
            $dql->addGroupBy('attributes');
        }

        if( method_exists($entityClass,'getPermissionObjectList') ) {
            $dql->leftJoin("ent.permissionObjectList", "permissionObjectList");
            $dql->addGroupBy('permissionObjectList');
        }
        if( method_exists($entityClass,'getPermissionActionList') ) {
            $dql->leftJoin("ent.permissionActionList", "permissionActionList");
            $dql->addGroupBy('permissionActionList');
        }

        if( method_exists($entityClass,'getInstitution') ) {
            $dql->leftJoin("ent.institution", "institution");
            $dql->addGroupBy('institution');
        }

        if( method_exists($entityClass,'getInstitutions') ) {
            $dql->leftJoin("ent.institutions", "institutions");
            $dql->addGroupBy('institutions');
        }

        if( method_exists($entityClass,'getCollaborationType') ) {
            $dql->leftJoin("ent.collaborationType", "collaborationType");
            $dql->addGroupBy('collaborationType');
        }

        if( method_exists($entityClass,'getSites') ) {
            $dql->leftJoin("ent.sites", "sites");
            $dql->addGroupBy('sites.name');
        }

        if( method_exists($entityClass,'getFellowshipSubspecialty') ) {
            $dql->leftJoin("ent.fellowshipSubspecialty", "fellowshipSubspecialty");
            $dql->addGroupBy('fellowshipSubspecialty.name');
        }


        //$dql->orderBy("ent.createdate","DESC");
		
		//pass sorting parameters directly to query; Somehow, knp_paginator stoped correctly create pagination according to sorting parameters       		
//		$postData = $request->query->all();
//		if( isset($postData['sort']) ) {
//            $dql = $dql . " ORDER BY $postData[sort] $postData[direction]";
//        }

        $dqlParameters = array();
        $filterform = $this->createForm(new ListFilterType(), null);
        $filterform->bind($request);
        $search = $filterform['search']->getData();
        echo "search=".$search."<br>";

        if( $search ) {
            $dql->andWhere("ent.name LIKE :search OR ent.abbreviation LIKE :search OR ent.shortname LIKE :search OR ent.description LIKE :search");
            $dqlParameters['search'] = '%'.$search.'%';
        }

        //echo "dql=".$dql."<br>";

        $em = $this->getDoctrine()->getManager();
        $limit = 50;

        $query = $em->createQuery($dql);

        if( count($dqlParameters) > 0 ) {
            $query->setParameters( $dqlParameters );
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->get('page', 1), /*page number*/
            $limit                          /*limit per page*/
            //array('wrap-queries'=>true)   //this cause sorting impossible, but without it "site" sorting does not work (mssql: "There is no component aliased by [sites] in the given Query" )
            //array('defaultSortFieldName' => 'ent.orderinlist', 'defaultSortDirection' => 'asc')
        );
        //echo "list count=".count($entities)."<br>";
        //exit();

        ///////////// check if show "create a new entity" link //////////////
        $createNew = true;
        $reflectionClass = new \ReflectionClass($mapper['fullClassName']);
        $compositeReflection = new \ReflectionClass("Oleg\\UserdirectoryBundle\\Entity\\CompositeNodeInterface");
        if( $reflectionClass->isSubclassOf($compositeReflection) ) {
            $createNew = false;
            //echo "dont show create new link";
        } else {
            //echo "show create new link";
        }
        ///////////// EOF check if show "create a new entity" link //////////////

        return array(
            'entities' => $entities,
            'displayName' => $mapper['displayName'],
            'pathbase' => $pathbase,
            'withCreateNewEntityLink' => $createNew,
            'filterform' => $filterform->createView(),
            'routename' => $request->get('_route')
        );
    }

    /**
     * Creates a new entity.
     *
     * @Route("/source-systems/", name="sourcesystems_create")
     * @Route("/roles/", name="role_create")
     * @Route("/institutions/", name="institutions_create")
     * @Route("/states/", name="states_create")
     * @Route("/countries/", name="countries_create")
     * @Route("/board-certifications/", name="boardcertifications_create")
     * @Route("/employment-termination-reasons/", name="employmentterminations_create")
     * @Route("/event-log-event-types/", name="loggereventtypes_create")
     * @Route("/primary-public-user-id-types/", name="usernametypes_create")
     * @Route("/identifier-types/", name="identifiers_create")
     * @Route("/residency-tracks/", name="residencytracks_create")
     * @Route("/fellowship-types/", name="fellowshiptypes_create")
//     * @Route("/research-labs/", name="researchlabs_create")
     * @Route("/location-types/", name="locationtypes_create")
     * @Route("/equipment/", name="equipments_create")
     * @Route("/equipment-types/", name="equipmenttypes_create")
     * @Route("/location-privacy-types/", name="locationprivacy_create")
     * @Route("/role-attributes/", name="roleattributes_create")
     * @Route("/buidlings/", name="buildings_create")
     * @Route("/rooms/", name="rooms_create")
     * @Route("/suites/", name="suites_create")
     * @Route("/floors/", name="floors_create")
     * @Route("/mailboxes/", name="mailboxes_create")
     * @Route("/percent-effort/", name="efforts_create")
     * @Route("/administrative-titles/", name="admintitles_create")
     * @Route("/academic-appointment-titles/", name="apptitles_create")
     * @Route("/training-completion-reasons/", name="completionreasons_create")
     * @Route("/training-degrees/", name="trainingdegrees_create")
     * @Route("/training-majors/", name="trainingmajors_create")
     * @Route("/training-minors/", name="trainingminors_create")
     * @Route("/training-honors/", name="traininghonors_create")
     * @Route("/fellowship-titles/", name="fellowshiptitles_create")
     * @Route("/residency-specialties/", name="residencyspecialtys_create")
     * @Route("/fellowship-subspecialties/", name="fellowshipsubspecialtys_create")
     * @Route("/institution-types/", name="institutiontypes_create")
     * @Route("/document-types/", name="documenttypes_create")
     * @Route("/medical-titles/", name="medicaltitles_create")
     * @Route("/medical-specialties/", name="medicalspecialties_create")
     * @Route("/employment-types/", name="employmenttypes_create")
     * @Route("/grant-source-organizations/", name="sourceorganizations_create")
     * @Route("/languages/", name="languages_create")
     * @Route("/locales/", name="locales_create")
     * @Route("/ranks-of-importance/", name="importances_create")
     * @Route("/authorship-roles/", name="authorshiproles_create")
     * @Route("/lecture-venues/", name="organizations_create")
     * @Route("/cities/", name="cities_create")
     * @Route("/link-types/", name="linktypes_create")
     * @Route("/sexes/", name="sexes_create")
     * @Route("/position-types/", name="positiontypes_create")
     * @Route("/organizational-group-types/", name="organizationalgrouptypes_create")
     * @Route("/profile-comment-group-types/", name="commentgrouptypes_create")
     * @Route("/comment-types/", name="commenttypes_createt")
     * @Route("/user-wrappers/", name="userwrappers_create")
     * @Route("/spot-purposes/", name="spotpurposes_create")
     * @Route("/medical-license-statuses/", name="medicalstatuses_create")
     * @Route("/certifying-board-organizations/", name="certifyingboardorganizations_create")
     * @Route("/training-types/", name="trainingtypes_create")
     * @Route("/job-titles/", name="joblists_create")
     * @Route("/fellowship-application-statuses/", name="fellappstatuses_create")
     * @Route("/fellowship-application-ranks/", name="fellappranks_create")
     * @Route("/fellowship-application-language-proficiencies/", name="fellapplanguageproficiency_create")
//     * @Route("/collaborations/", name="collaborations_create")
     * @Route("/collaboration-types/", name="collaborationtypes_create")
     * @Route("/permissions/", name="permission_create")
     * @Route("/permission-objects/", name="permissionobject_create")
     * @Route("/permission-actions/", name="permissionaction_create")
     * @Route("/sites/", name="sites_create")
     * @Route("/event-object-types/", name="eventobjecttypes_create")
     * @Route("/vacreq-availabilities/", name="vacreqavailabilities_create")
     *
     * @Method("POST")
     * @Template("OlegUserdirectoryBundle:ListForm:new.html.twig")
     */
    public function createAction(Request $request)
    {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        }

        return $this->createList($request);
    }
    public function createList($request) {
        $routeName = $request->get('_route');

        $pieces = explode("_", $routeName);
        $pathbase = $pieces[0];

        $mapper= $this->classListMapper($pathbase);

        $entityClass = $mapper['fullClassName'];    //"Oleg\\OrderformBundle\\Entity\\".$mapper['className'];

        $entity = new $entityClass();

        $form = $this->createCreateForm($entity,$mapper,$pathbase,'new');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //the date from the form does not contain time, so set createdate with date and time.
            $entity->setCreatedate(new \DateTime());

            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setCreator($user);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl($pathbase.'_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'displayName' => $mapper['displayName'],
            'pathbase' => $pathbase
        );
    }


    /**
    * Creates a form to create an entity.
    * @param $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm($entity,$mapper,$pathbase,$cycle=null)
    {
        $options = array();

        if( $cycle ) {
            $options['cycle'] = $cycle;
        }

        //use $timezone = $user->getTimezone(); ?
        $user = $this->get('security.context')->getToken()->getUser();
        $options['user'] = $user;
        $options['entity'] = $entity;
        $options['em'] = $this->getDoctrine()->getManager();

        $newForm = new GenericListType($options,$mapper);

        $form = $this->createForm($newForm, $entity, array(
            'action' => $this->generateUrl($pathbase.'_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create','attr'=>array('class'=>'btn btn-warning')));

        return $form;
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/source-systems/new", name="sourcesystems_new")
     * @Route("/roles/new", name="role_new")
     * @Route("/institutions/new", name="institutions_new")
     * @Route("/states/new", name="states_new")
     * @Route("/countries/new", name="countries_new")
     * @Route("/board-certifications/new", name="boardcertifications_new")
     * @Route("/employment-termination-reasons/new", name="employmentterminations_new")
     * @Route("/event-log-event-types/new", name="loggereventtypes_new")
     * @Route("/primary-public-user-id-types/new", name="usernametypes_new")
     * @Route("/identifier-types/new", name="identifiers_new")
     * @Route("/residency-tracks/new", name="residencytracks_new")
     * @Route("/fellowship-types/new", name="fellowshiptypes_new")
//     * @Route("/research-labs/new", name="researchlabs_new")
     * @Route("/location-types/new", name="locationtypes_new")
     * @Route("/equipment/new", name="equipments_new")
     * @Route("/equipment-types/new", name="equipmenttypes_new")
     * @Route("/location-privacy-types/new", name="locationprivacy_new")
     * @Route("/role-attributes/new", name="roleattributes_new")
     * @Route("/buidlings/new", name="buildings_new")
     * @Route("/rooms/new", name="rooms_new")
     * @Route("/suites/new", name="suites_new")
     * @Route("/floors/new", name="floors_new")
     * @Route("/mailboxes/new", name="mailboxes_new")
     * @Route("/percent-effort/new", name="efforts_new")
     * @Route("/administrative-titles/new", name="admintitles_new")
     * @Route("/academic-appointment-titles/new", name="apptitles_new")
     * @Route("/training-completion-reasons/new", name="completionreasons_new")
     * @Route("/training-degrees/new", name="trainingdegrees_new")
     * @Route("/training-majors/new", name="trainingmajors_new")
     * @Route("/training-minors/new", name="trainingminors_new")
     * @Route("/training-honors/new", name="traininghonors_new")
     * @Route("/fellowship-titles/new", name="fellowshiptitles_new")
     * @Route("/residency-specialties/new", name="residencyspecialtys_new")
     * @Route("/fellowship-subspecialties/new", name="fellowshipsubspecialtys_new")
     * @Route("/institution-types/new", name="institutiontypes_new")
     * @Route("/document-types/new", name="documenttypes_new")
     * @Route("/medical-titles/new", name="medicaltitles_new")
     * @Route("/medical-specialties/new", name="medicalspecialties_new")
     * @Route("/employment-types/new", name="employmenttypes_new")
     * @Route("/grant-source-organizations/new", name="sourceorganizations_new")
     * @Route("/languages/new", name="languages_new")
     * @Route("/locales/new", name="locales_new")
     * @Route("/ranks-of-importance/new", name="importances_new")
     * @Route("/authorship-roles/new", name="authorshiproles_new")
     * @Route("/lecture-venues/new", name="organizations_new")
     * @Route("/cities/new", name="cities_new")
     * @Route("/link-types/new", name="linktypes_new")
     * @Route("/sexes/new", name="sexes_new")
     * @Route("/position-types/new", name="positiontypes_new")
     * @Route("/organizational-group-types/new", name="organizationalgrouptypes_new")
     * @Route("/profile-comment-group-types/new", name="commentgrouptypes_new")
     * @Route("/comment-types/new", name="commenttypes_new")
     * @Route("/user-wrappers/new", name="userwrappers_new")
     * @Route("/spot-purposes/new", name="spotpurposes_new")
     * @Route("/medical-license-statuses/new", name="medicalstatuses_new")
     * @Route("/certifying-board-organizations/new", name="certifyingboardorganizations_new")
     * @Route("/training-types/new", name="trainingtypes_new")
     * @Route("/job-titles/new", name="joblists_new")
     * @Route("/fellowship-application-statuses/new", name="fellappstatuses_new")
     * @Route("/fellowship-application-ranks/new", name="fellappranks_new")
     * @Route("/fellowship-application-language-proficiencies/new", name="fellapplanguageproficiency_new")
//     * @Route("/collaborations/new", name="collaborations_new")
     * @Route("/collaboration-types/new", name="collaborationtypes_new")
     * @Route("/permissions/new", name="permission_new")
     * @Route("/permission-objects/new", name="permissionobject_new")
     * @Route("/permission-actions/new", name="permissionaction_new")
     * @Route("/sites/new", name="sites_new")
     * @Route("/event-object-types/new", name="eventobjecttypes_new")
     * @Route("/vacreq-availabilities/new", name="vacreqavailabilities_new")
     *
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:ListForm:new.html.twig")
     */
    public function newAction(Request $request)
    {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        }

        return $this->newList($request);
    }
    public function newList($request,$pid=null) {
        $routeName = $request->get('_route');
        $pieces = explode("_", $routeName);
        $pathbase = $pieces[0];
        //echo "pathbase=".$pathbase."<br>";

        $em = $this->getDoctrine()->getManager();

        $mapper= $this->classListMapper($pathbase);

        $entityClass = $mapper['fullClassName'];    //"Oleg\\OrderformBundle\\Entity\\".$mapper['className'];

        $entity = new $entityClass();

        $user = $this->get('security.context')->getToken()->getUser();
        $entity->setCreatedate(new \DateTime());
        $entity->setType('user-added');
        $entity->setCreator($user);

        if( $pid ) {
            //echo "pid=".$pid."<br>";
            $parentNMapper = $this->getParentName($mapper['className']);
            $parent = $em->getRepository($parentNMapper['bundleName'].':'.$parentNMapper['className'])->find($pid);
            $entity->setParent($parent);
        }

        //get max orderinlist + 10
        $query = $em->createQuery('SELECT MAX(c.orderinlist) as maxorderinlist FROM '.$mapper['bundleName'].':'.$mapper['className'].' c');
        $nextorder = $query->getSingleResult()['maxorderinlist']+10;
        $entity->setOrderinlist($nextorder);

        $form   = $this->createCreateForm($entity,$mapper,$pathbase,'new');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'displayName' => $mapper['displayName'],
            'pathbase' => $pathbase
        );
    }

    /**
     * Finds and displays a entity.
     *
     * @Route("/source-systems/{id}", name="sourcesystems_show")
     * @Route("/roles/{id}", name="role_show")
     * @Route("/institutions/{id}", name="institutions_show", options={"expose"=true})
     * @Route("/states/{id}", name="states_show")
     * @Route("/countries/{id}", name="countries_show")
     * @Route("/board-certifications/{id}", name="boardcertifications_show")
     * @Route("/employment-termination-reasons/{id}", name="employmentterminations_show")
     * @Route("/event-log-event-types/{id}", name="loggereventtypes_show")
     * @Route("/primary-public-user-id-types/{id}", name="usernametypes_show")
     * @Route("/identifier-types/{id}", name="identifiers_show")
     * @Route("/residency-tracks/{id}", name="residencytracks_show")
     * @Route("/fellowship-types/{id}", name="fellowshiptypes_show")
//     * @Route("/research-labs/{id}", name="researchlabs_show")
     * @Route("/location-types/{id}", name="locationtypes_show")
     * @Route("/equipment/{id}", name="equipments_show")
     * @Route("/equipment-types/{id}", name="equipmenttypes_show")
     * @Route("/location-privacy-types/{id}", name="locationprivacy_show")
     * @Route("/role-attributes/{id}", name="roleattributes_show")
     * @Route("/buidlings/{id}", name="buildings_show")
     * @Route("/rooms/{id}", name="rooms_show")
     * @Route("/suites/{id}", name="suites_show")
     * @Route("/floors/{id}", name="floors_show")
     * @Route("/mailboxes/{id}", name="mailboxes_show")
     * @Route("/percent-effort/{id}", name="efforts_show")
     * @Route("/administrative-titles/{id}", name="admintitles_show")
     * @Route("/academic-appointment-titles/{id}", name="apptitles_show")
     * @Route("/training-completion-reasons/{id}", name="completionreasons_show")
     * @Route("/training-degrees/{id}", name="trainingdegrees_show")
     * @Route("/training-majors/{id}", name="trainingmajors_show")
     * @Route("/training-minors/{id}", name="trainingminors_show")
     * @Route("/training-honors/{id}", name="traininghonors_show")
     * @Route("/fellowship-titles/{id}", name="fellowshiptitles_show")
     * @Route("/residency-specialties/{id}", name="residencyspecialtys_show")
     * @Route("/fellowship-subspecialties/{id}", name="fellowshipsubspecialtys_show")
     * @Route("/institution-types/{id}", name="institutiontypes_show")
     * @Route("/document-types/{id}", name="documenttypes_show")
     * @Route("/medical-titles/{id}", name="medicaltitles_show")
     * @Route("/medical-specialties/{id}", name="medicalspecialties_show")
     * @Route("/employment-types/{id}", name="employmenttypes_show")
     * @Route("/grant-source-organizations/{id}", name="sourceorganizations_show")
     * @Route("/languages/{id}", name="languages_show")
     * @Route("/locales/{id}", name="locales_show")
     * @Route("/ranks-of-importance/{id}", name="importances_show")
     * @Route("/authorship-roles/{id}", name="authorshiproles_show")
     * @Route("/lecture-venues/{id}", name="organizations_show")
     * @Route("/cities/{id}", name="cities_show")
     * @Route("/link-types/{id}", name="linktypes_show")
     * @Route("/sexes/{id}", name="sexes_show")
     * @Route("/position-types/{id}", name="positiontypes_show")
     * @Route("/organizational-group-types/{id}", name="organizationalgrouptypes_show")
     * @Route("/profile-comment-group-types/{id}", name="commentgrouptypes_show")
     * @Route("/comment-types/{id}", name="commenttypes_show", options={"expose"=true})
     * @Route("/user-wrappers/{id}", name="userwrappers_show")
     * @Route("/spot-purposes/{id}", name="spotpurposes_show")
     * @Route("/medical-license-statuses/{id}", name="medicalstatuses_show")
     * @Route("/certifying-board-organizations/{id}", name="certifyingboardorganizations_show")
     * @Route("/training-types/{id}", name="trainingtypes_show")
     * @Route("/job-titles/{id}", name="joblists_show")
     * @Route("/fellowship-application-statuses/{id}", name="fellappstatuses_show")
     * @Route("/fellowship-application-ranks/{id}", name="fellappranks_show")
     * @Route("/fellowship-application-language-proficiencies/{id}", name="fellapplanguageproficiency_show")
//     * @Route("/collaborations/{id}", name="collaborations_show")
     * @Route("/collaboration-types/{id}", name="collaborationtypes_show")
     * @Route("/permissions/{id}", name="permission_show")
     * @Route("/permission-objects/{id}", name="permissionobject_show")
     * @Route("/permission-actions/{id}", name="permissionaction_show")
     * @Route("/sites/{id}", name="sites_show")
     * @Route("/event-object-types/{id}", name="eventobjecttypes_show")
     * @Route("/vacreq-availabilities/{id}", name="vacreqavailabilities_show")
     *
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:ListForm:show.html.twig")
     */
    public function showAction(Request $request,$id)
    {

        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_OBSERVER') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        }

        return $this->showList($request,$id);
    }
    public function showList($request,$id) {
        $routeName = $request->get('_route');
        $pieces = explode("_", $routeName);
        $pathbase = $pieces[0];
        //echo "pathbase=".$pathbase."<br>";
        //exit('show');

        $em = $this->getDoctrine()->getManager();

        $mapper= $this->classListMapper($pathbase);

        $entity = $em->getRepository($mapper['bundleName'].':'.$mapper['className'])->find($id);
        //echo "entity=".$entity."<br>";
        $form = $this->createEditForm($entity,$mapper,$pathbase,'edit',true);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$mapper['fullClassName'].' entity.');
        }

        //$deleteForm = $this->createDeleteForm($id,$pathbase);

        return array(
            'entity' => $entity,
            'edit_form' => $form->createView(),
            'delete_form' => null,  //$deleteForm->createView(),
            'displayName' => $mapper['displayName'],
            'pathbase' => $pathbase
        );
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/source-systems/{id}/edit", name="sourcesystems_edit")
     * @Route("/roles/{id}/edit", name="role_edit")
     * @Route("/institutions/{id}/edit", name="institutions_edit")
     * @Route("/states/{id}/edit", name="states_edit")
     * @Route("/countries/{id}/edit", name="countries_edit")
     * @Route("/board-certifications/{id}/edit", name="boardcertifications_edit")
     * @Route("/employment-termination-reasons/{id}/edit", name="employmentterminations_edit")
     * @Route("/event-log-event-types/{id}/edit", name="loggereventtypes_edit")
     * @Route("/primary-public-user-id-types/{id}/edit", name="usernametypes_edit")
     * @Route("/identifier-types/{id}/edit", name="identifiers_edit")
     * @Route("/residency-tracks/{id}/edit", name="residencytracks_edit")
     * @Route("/fellowship-types/{id}/edit", name="fellowshiptypes_edit")
//     * @Route("/research-labs/{id}/edit", name="researchlabs_edit")
     * @Route("/location-types/{id}/edit", name="locationtypes_edit")
     * @Route("/equipment/{id}/edit", name="equipments_edit")
     * @Route("/equipment-types/{id}/edit", name="equipmenttypes_edit")
     * @Route("/location-privacy-types/{id}/edit", name="locationprivacy_edit")
     * @Route("/role-attributes/{id}/edit", name="roleattributes_edit")
     * @Route("/buidlings/{id}/edit", name="buildings_edit")
     * @Route("/rooms/{id}/edit", name="rooms_edit")
     * @Route("/suites/{id}/edit", name="suites_edit")
     * @Route("/floors/{id}/edit", name="floors_edit")
     * @Route("/mailboxes/{id}/edit", name="mailboxes_edit")
     * @Route("/percent-effort/{id}/edit", name="efforts_edit")
     * @Route("/administrative-titles/{id}/edit", name="admintitles_edit")
     * @Route("/academic-appointment-titles/{id}/edit", name="apptitles_edit")
     * @Route("/training-completion-reasons/{id}/edit", name="completionreasons_edit")
     * @Route("/training-degrees/{id}/edit", name="trainingdegrees_edit")
     * @Route("/training-majors/{id}/edit", name="trainingmajors_edit")
     * @Route("/training-minors/{id}/edit", name="trainingminors_edit")
     * @Route("/training-honors/{id}/edit", name="traininghonors_edit")
     * @Route("/fellowship-titles/{id}/edit", name="fellowshiptitles_edit")
     * @Route("/residency-specialties/{id}/edit", name="residencyspecialtys_edit")
     * @Route("/fellowship-subspecialties/{id}/edit", name="fellowshipsubspecialtys_edit")
     * @Route("/institution-types/{id}/edit", name="institutiontypes_edit")
     * @Route("/document-types/{id}/edit", name="documenttypes_edit")
     * @Route("/medical-titles/{id}/edit", name="medicaltitles_edit")
     * @Route("/medical-specialties/{id}/edit", name="medicalspecialties_edit")
     * @Route("/employment-types/{id}/edit", name="employmenttypes_edit")
     * @Route("/grant-source-organizations/{id}/edit", name="sourceorganizations_edit")
     * @Route("/languages/{id}/edit", name="languages_edit")
     * @Route("/locales/{id}/edit", name="locales_edit")
     * @Route("/ranks-of-importance/{id}/edit", name="importances_edit")
     * @Route("/authorship-roles/{id}/edit", name="authorshiproles_edit")
     * @Route("/lecture-venues/{id}/edit", name="organizations_edit")
     * @Route("/cities/{id}/edit", name="cities_edit")
     * @Route("/link-types/{id}/edit", name="linktypes_edit")
     * @Route("/sexes/{id}/edit", name="sexes_edit")
     * @Route("/position-types/{id}/edit", name="positiontypes_edit")
     * @Route("/organizational-group-types/{id}/edit", name="organizationalgrouptypes_edit")
     * @Route("/profile-comment-group-types/{id}/edit", name="commentgrouptypes_edit")
     * @Route("/comment-types/{id}/edit", name="commenttypes_edit")
     * @Route("/user-wrappers/{id}/edit", name="userwrappers_edit")
     * @Route("/spot-purposes/{id}/edit", name="spotpurposes_edit")
     * @Route("/medical-license-statuses/{id}/edit", name="medicalstatuses_edit")
     * @Route("/certifying-board-organizations/{id}/edit", name="certifyingboardorganizations_edit")
     * @Route("/training-types/{id}/edit", name="trainingtypes_edit")
     * @Route("/job-titles/{id}/edit", name="joblists_edit")
     * @Route("/fellowship-application-statuses/{id}/edit", name="fellappstatuses_edit")
     * @Route("/fellowship-application-ranks/{id}/edit", name="fellappranks_edit")
     * @Route("/fellowship-application-language-proficiencies/{id}/edit", name="fellapplanguageproficiency_edit")
//     * @Route("/collaborations/{id}/edit", name="collaborations_edit")
     * @Route("/collaboration-types/{id}/edit", name="collaborationtypes_edit")
     * @Route("/permissions/{id}/edit", name="permission_edit")
     * @Route("/permission-objects/{id}/edit", name="permissionobject_edit")
     * @Route("/permission-actions/{id}/edit", name="permissionaction_edit")
     * @Route("/sites/{id}/edit", name="sites_edit")
     * @Route("/event-object-types/{id}/edit", name="eventobjecttypes_edit")
     * @Route("/vacreq-availabilities/{id}/edit", name="vacreqavailabilities_edit")
     *
     * @Method("GET")
     * @Template("OlegUserdirectoryBundle:ListForm:edit.html.twig")
     */
    public function editAction(Request $request,$id)
    {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        }

        return $this->editList($request,$id);
    }
    public function editList($request,$id) {
        $routeName = $request->get('_route');
        $pieces = explode("_", $routeName);
        $pathbase = $pieces[0];

        $em = $this->getDoctrine()->getManager();

        $mapper= $this->classListMapper($pathbase);

        $entity = $em->getRepository($mapper['bundleName'].':'.$mapper['className'])->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$mapper['fullClassName'].' entity.');
        }

        //add permissions
        //$this->addPermissions($entity);

        $editForm = $this->createEditForm($entity,$mapper,$pathbase,'edit');
        $deleteForm = $this->createDeleteForm($id,$pathbase);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'displayName' => $mapper['displayName'],
            'pathbase' => $pathbase
        );
    }

    private function addPermissions($entity) {
        if( method_exists($entity,'getPermissions') ) {
            //echo "add permission for ".$entity."<br>";
            $permission = new Permission();
            $entity->addPermission($permission);
        }
    }

    /**
    * Creates a form to edit an entity.
    * @param $entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm($entity,$mapper,$pathbase,$cycle,$disabled=false)
    {

        $options = array();

        $options['id'] = $entity->getId();

        if( $cycle ) {
            $options['cycle'] = $cycle;
        }

        //use $timezone = $user->getTimezone(); ?
        $user = $this->get('security.context')->getToken()->getUser();
        $options['user'] = $user;
        $options['entity'] = $entity;
        $options['em'] = $this->getDoctrine()->getManager();

        $newForm = new GenericListType($options,$mapper);

        $form = $this->createForm($newForm, $entity, array(
            'action' => $this->generateUrl($pathbase.'_show', array('id' => $entity->getId())),
            'method' => 'PUT',
            'disabled' => $disabled
        ));

        if( !$disabled ) {
            $form->add('submit', 'submit', array('label' => 'Update', 'attr'=>array('class'=>'btn btn-warning')));
        }

        return $form;
    }
    /**
     * Edits an existing entity.
     *
     * @Route("/source-systems/{id}", name="sourcesystems_update")
     * @Route("/roles/{id}", name="role_update")
     * @Route("/institutions/{id}", name="institutions_update")
     * @Route("/states/{id}", name="states_update")
     * @Route("/countries/{id}", name="countries_update")
     * @Route("/board-certifications/{id}", name="boardcertifications_update")
     * @Route("/employment-termination-reasons/{id}", name="employmentterminations_update")
     * @Route("/event-log-event-types/{id}", name="loggereventtypes_update")
     * @Route("/primary-public-user-id-types/{id}", name="usernametypes_update")
     * @Route("/identifier-types/{id}", name="identifiers_update")
     * @Route("/residency-tracks/{id}", name="residencytracks_update")
     * @Route("/fellowship-types/{id}", name="fellowshiptypes_update")
//     * @Route("/research-labs/{id}", name="researchlabs_update")
     * @Route("/location-types/{id}", name="locationtypes_update")
     * @Route("/equipment/{id}", name="equipments_update")
     * @Route("/equipment-types/{id}", name="equipmenttypes_update")
     * @Route("/location-privacy-types/{id}", name="locationprivacy_update")
     * @Route("/role-attributes/{id}", name="roleattributes_update")
     * @Route("/buidlings/{id}", name="buildings_update")
     * @Route("/rooms/{id}", name="rooms_update")
     * @Route("/suites/{id}", name="suites_update")
     * @Route("/floors/{id}", name="floors_update")
     * @Route("/mailboxes/{id}", name="mailboxes_update")
     * @Route("/percent-effort/{id}", name="efforts_update")
     * @Route("/administrative-titles/{id}", name="admintitles_update")
     * @Route("/academic-appointment-titles/{id}", name="apptitles_update")
     * @Route("/training-completion-reasons/{id}", name="completionreasons_update")
     * @Route("/training-degrees/{id}", name="trainingdegrees_update")
     * @Route("/training-majors/{id}", name="trainingmajors_update")
     * @Route("/training-minors/{id}", name="trainingminors_update")
     * @Route("/training-honors/{id}", name="traininghonors_update")
     * @Route("/fellowship-titles/{id}", name="fellowshiptitles_update")
     * @Route("/residency-specialties/{id}", name="residencyspecialtys_update")
     * @Route("/fellowship-subspecialties/{id}", name="fellowshipsubspecialtys_update")
     * @Route("/institution-types/{id}", name="institutiontypes_update")
     * @Route("/document-types/{id}", name="documenttypes_update")
     * @Route("/medical-titles/{id}", name="medicaltitles_update")
     * @Route("/medical-specialties/{id}", name="medicalspecialties_update")
     * @Route("/employment-types/{id}", name="employmenttypes_update")
     * @Route("/grant-source-organizations/{id}", name="sourceorganizations_update")
     * @Route("/languages/{id}", name="languages_update")
     * @Route("/locales/{id}", name="locales_update")
     * @Route("/ranks-of-importance/{id}", name="importances_update")
     * @Route("/authorship-roles/{id}", name="authorshiproles_update")
     * @Route("/lecture-venues/{id}", name="organizations_update")
     * @Route("/cities/{id}", name="cities_update")
     * @Route("/link-types/{id}", name="linktypes_update")
     * @Route("/sexes/{id}", name="sexes_update")
     * @Route("/position-types/{id}", name="positiontypes_update")
     * @Route("/organizational-group-types/{id}", name="organizationalgrouptypes_update")
     * @Route("/profile-comment-group-types/{id}", name="commentgrouptypes_update")
     * @Route("/comment-types/{id}", name="commenttypes_update")
     * @Route("/user-wrappers/{id}", name="userwrappers_update")
     * @Route("/spot-purposes/{id}", name="spotpurposes_update")
     * @Route("/medical-license-statuses/{id}", name="medicalstatuses_update")
     * @Route("/certifying-board-organizations/{id}", name="certifyingboardorganizations_update")
     * @Route("/training-types/{id}", name="trainingtypes_update")
     * @Route("/job-titles/{id}", name="joblists_update")
     * @Route("/fellowship-application-statuses/{id}", name="fellappstatuses_update")
     * @Route("/fellowship-application-ranks/{id}", name="fellappranks_update")
     * @Route("/fellowship-application-language-proficiencies/{id}", name="fellapplanguageproficiency_update")
//     * @Route("/collaborations/{id}", name="collaborations_update")
     * @Route("/collaboration-types/{id}", name="collaborationtypes_update")
     * @Route("/permissions/{id}", name="permission_update")
     * @Route("/permission-objects/{id}", name="permissionobject_update")
     * @Route("/permission-actions/{id}", name="permissionaction_update")
     * @Route("/sites/{id}", name="sites_update")
     * @Route("/event-object-types/{id}", name="eventobjecttypes_update")
     * @Route("/vacreq-availabilities/{id}", name="vacreqavailabilities_update")
     *
     * @Method("PUT")
     * @Template("OlegUserdirectoryBundle:ListForm:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        if( false === $this->get('security.context')->isGranted('ROLE_USERDIRECTORY_EDITOR') ) {
            return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        }

        return $this->updateList($request, $id);
    }
    public function updateList($request, $id) {
        $routeName = $request->get('_route');
        $pieces = explode("_", $routeName);
        $pathbase = $pieces[0];

        $em = $this->getDoctrine()->getManager();

        $mapper= $this->classListMapper($pathbase);

        $entity = $em->getRepository($mapper['bundleName'].':'.$mapper['className'])->find($id);

        //save array of synonyms
        $beforeformSynonyms = clone $entity->getSynonyms();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find '.$mapper['fullClassName'].' entity.');
        }

        //remove permissions: original permissions. Used for roles
        if( method_exists($entity,'getPermissions') ) {
            $originalPermissions = array();
            foreach( $entity->getPermissions() as $permission ) {
                $originalPermissions[] = $permission;
            }
        }

        $deleteForm = $this->createDeleteForm($id,$pathbase);
        $editForm = $this->createEditForm($entity,$mapper,$pathbase,'edit_put_list');
        $editForm->handleRequest($request);

        if( $editForm->isValid() ) {

            //make sure to keep creator and creation date from original entity, according to the requirements (Issue#250):
            //For "Creation Date", "Creator" these variables should not be modifiable via the form even if the user unlocks these fields in the browser.
            $originalEntity = $em->getRepository($mapper['bundleName'].':'.$mapper['className'])->find($id);
            $entity->setCreator($originalEntity->getCreator());
            $entity->setCreatedate($originalEntity->getCreatedate());

            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setUpdatedby($user);
            //$entity->setUpdatedon(new \DateTime());
            $entity->setUpdateAuthorRoles($user->getRoles());

            //take care of self-referencing: remove
            if( count($beforeformSynonyms) > count($entity->getSynonyms()) ) {
                foreach( $beforeformSynonyms as $syn ) {
                    $syn->setOriginal(NULL);
                }
            }

            //take care of self-referencing: add
            foreach( $entity->getSynonyms() as $syn ) {
                $syn->setOriginal($entity);
            }

            /////////// remove permissions. Used for roles ///////////
            if( method_exists($entity,'getPermissions') ) {

                /////////////// Process Removed Collections ///////////////
                $removedCollections = array();

                $removedInfo = $this->removeCollection($originalPermissions,$entity->getPermissions(),$entity);
                if( $removedInfo ) {
                    $removedCollections[] = $removedInfo;
                }
                /////////////// EOF Process Removed Collections ///////////////

                /////////////// Add event log on edit (edit or add collection) ///////////////
                /////////////// Must run before removeCollection() function which flash DB. When DB is flashed getEntityChangeSet() will not work ///////////////
                $changedInfoArr = $this->setEventLogChanges($entity);
                //exit('1');
                //set Edit event log for removed collection and changed fields or added collection
                if( count($changedInfoArr) > 0 || count($removedCollections) > 0 ) {
                    $event = "Permission of the Role ".$entity->getId()." has been changed by ".$user.":"."<br>";
                    $event = $event . implode("<br>", $changedInfoArr);
                    $event = $event . "<br>" . implode("<br>", $removedCollections);
                    $userSecUtil = $this->get('user_security_utility');
                    //echo "event=".$event."<br>";
                    //print_r($removedCollections);
                    //exit();
                    $userSecUtil->createUserEditEvent($this->container->getParameter('employees.sitename'),$event,$user,$entity,$request,'Role Permission Updated');
                }
                //exit();

//                foreach( $originalPermissions as $originalPermission ) {
//                    if( false === $entity->getPermissions()->contains($originalPermission) ) {
//                        // remove the Task from the Tag
//                        $entity->removePermission($originalPermission);
//
//                        // if it was a many-to-one relationship, remove the relationship like this
//                        $originalPermission->setRole(null);
//
//                        $em->persist($originalPermission);
//
//                        // if you wanted to delete the Tag entirely, you can also do that
//                        $em->remove($originalPermission);
//                    }
//                }
            }
            /////////// EOF remove permissions. Used for roles ///////////

            $em->flush();

            return $this->redirect($this->generateUrl($pathbase.'_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'displayName' => $mapper['displayName'],
            'pathbase' => $pathbase
        );
    }


    ////////////////////////////////////
    public function setEventLogChanges($entity) {

        $em = $this->getDoctrine()->getManager();

        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets(); // do not compute changes if inside a listener

        $eventArr = array();

        //log simple fields
        $changeset = $uow->getEntityChangeSet($entity);
        $eventArr = $this->addChangesToEventLog( $eventArr, $changeset );

        //permission list
        foreach( $entity->getPermissions() as $subentity ) {
            //echo "subentity=".$subentity."<br>";
            $changeset = $uow->getEntityChangeSet($subentity);
            $text = "("."permission ".$this->getEntityId($subentity).")";
            //print_r($changeset);
            $eventArr = $this->addChangesToEventLog( $eventArr, $changeset, $text );

            //add current object state
            $eventArr[] = "Final state: " . $subentity;
        }

        return $eventArr;
    }
    public function removeCollection($originalArr,$currentArr,$entity) {
        $em = $this->getDoctrine()->getManager();
        $removeArr = array();

        foreach( $originalArr as $element ) {
            if( false === $currentArr->contains($element) ) {
                $removeArr[] = "<strong>"."Removed: ".$element." ".$this->getEntityId($element)."</strong>";
                $entity->removePermission($element);
                $element->setRole(NULL);
                $em->persist($element);
                $em->remove($element);
            }
        } //foreach

        return implode("<br>", $removeArr);
    }
    public function addChangesToEventLog( $eventArr, $changeset, $text="" ) {

        $changeArr = array();

        //process $changeset: author, subjectuser, oldvalue, newvalue
        foreach( $changeset as $key => $value ) {
            if( $value[0] != $value[1] ) {

                if( is_object($key) ) {
                    //if $key is object then skip it, because we don't want to have non-informative record such as: credentials(stateLicense New): old value=, new value=Credentials
                    continue;
                }

                $field = $key;

                $oldValue = $value[0];
                $newValue = $value[1];

                if( $oldValue instanceof \DateTime ) {
                    $oldValue = $this->convertDateTimeToStr($value[0]);
                }
                if( $newValue instanceof \DateTime ) {
                    $newValue = $this->convertDateTimeToStr($value[1]);
                }

                if( is_array($oldValue) ) {
                    $oldValue = implode(",",$oldValue);
                }
                if( is_array($newValue) ) {
                    $newValue = implode(",",$newValue);
                }

                $event = "<strong>".$field.$text."</strong>".": "."old value=".$oldValue.", new value=".$newValue;
                //echo "event=".$event."<br>";
                //exit();

                $changeArr[] = $event;
            }
        }

        if( count($changeArr) > 0 ) {
            $eventArr[] = implode("<br>", $changeArr);
        }

        return $eventArr;

    }

    public function convertDateTimeToStr($datetime) {
        $transformer = new DateTimeToStringTransformer(null,null,'m/d/Y');
        $dateStr = $transformer->transform($datetime);
        return $dateStr;
    }
    public function getEntityId($entity) {
        if( $entity->getId() ) {
            return "ID=".$entity->getId();
        }
        return "New";
    }
    ////////////////////////////////////








    //////////////////////// Tree //////////////////////////////
//    /**
//     * Displays a form to create a new entity with parent.
//     *
//     * @Route("/department/new/parent/{pid}", name="departments_new_with_parent")
//     * @Route("/division/new/parent/{pid}", name="divisions_new_with_parent")
//     * @Route("/service/new/parent/{pid}", name="services_new_with_parent")
//     * @Route("/service/new/parent/{pid}", name="services_new_with_parent")
//     * @Method("GET")
//     * @Template("OlegUserdirectoryBundle:ListForm:new.html.twig")
//     */
//    public function newNodeWithParentAction(Request $request,$pid)
//    {
//        return $this->newList($request,$pid);
//    }

    public function getParentName( $className ) {

        //echo "className=".$className."<br>";

        switch( $className ) {
//            case "Department":
//                $parentClassName = "Institution";
//                break;
//            case "Division":
//                $parentClassName = "Department";
//                break;
//            case "Service":
//                $parentClassName = "Division";
//                break;
            case "FellowshipSubspecialty":
                $parentClassName = "ResidencySpecialty";
                break;
            default:
                //$parentClassName = null;
                return null;
        }

        $res = array();
        $res['className'] = $parentClassName;
        $res['fullClassName'] = "Oleg\\UserdirectoryBundle\\Entity\\".$className;
        $res['bundleName'] = "OlegUserdirectoryBundle";

        return $res;
    }
    //////////////////////// EOF tree //////////////////////////////











    public function classListMapper( $route ) {

        $labels = null;

        $bundleName = "UserdirectoryBundle";

        switch( $route ) {

            case "sourcesystems":
                $className = "SourceSystemList";
                $displayName = "Systems";
                break;
            case "role":
                $className = "Roles";
                $displayName = "Roles";
                //$labels = array('description'=>'Explanation of Capabilities:');
                break;
            case "institutions":
                $className = "Institution";
                $displayName = "Institutions";
                break;
            case "states":
                $className = "States";
                $displayName = "States";
                break;
            case "countries":
                $className = "Countries";
                $displayName = "Countries";
                break;
            case "boardcertifications":
                $className = "BoardCertifiedSpecialties";
                $displayName = "Pathology Board Certified Specialties";
                break;
            case "employmenttypes":
                $className = "EmploymentType";
                $displayName = "Employment Types";
                break;
            case "employmentterminations":
                $className = "EmploymentTerminationType";
                $displayName = "Employment Types of Termination";
                break;
            case "loggereventtypes":
                $className = "EventTypeList";
                $displayName = "Event Log Types";
                break;
            case "usernametypes":
                $className = "UsernameType";
                $displayName = "Primary Public User ID Types";
                break;
            case "identifiers":
                $className = "IdentifierTypeList";
                $displayName = "Identifier Types";
                break;
            case "residencytracks":
                $className = "ResidencyTrackList";
                $displayName = "Residency Tracks";
                break;
            case "fellowshiptypes":
                $className = "FellowshipTypeList";
                $displayName = "Fellowship Types";
                break;
//            case "researchlabs":
//                $className = "ResearchLab";
//                $displayName = "Research Labs";
//                break;
            case "locationtypes":
                $className = "LocationTypeList";
                $displayName = "Location Types";
                break;
            case "equipments":
                $className = "Equipment";
                $displayName = "Equipment";
                break;
            case "equipmenttypes":
                $className = "EquipmentType";
                $displayName = "Equipment Types";
                break;
            case "locationprivacy":
                $className = "LocationPrivacyList";
                $displayName = "Location Privacy Types";
                break;
            case "roleattributes":
                $className = "RoleAttributeList";
                $displayName = "Role Attributes";
                //$labels = array('description'=>'Explanation of Capabilities:');
                break;
            case "buildings":
                $className = "BuildingList";
                $displayName = "Buildings";
                break;
            case "rooms":
                $className = "RoomList";
                $displayName = "Rooms";
                break;
            case "suites":
                $className = "SuiteList";
                $displayName = "Suites";
                break;
            case "floors":
                $className = "FloorList";
                $displayName = "Floors";
                break;
            case "mailboxes":
                $className = "MailboxList";
                $displayName = "Mailboxes";
                break;
            case "efforts":
                $className = "EffortList";
                $displayName = "Percent Efforts";
                break;
            case "admintitles":
                $className = "AdminTitleList";
                $displayName = "Administrative Titles";
                break;
            case "apptitles":
                $className = "AppTitleList";
                $displayName = "Academic Appointment Titles";
                break;
            case "completionreasons":
                $className = "CompletionReasonList";
                $displayName = "Training Completion Reasons";
                break;
            case "trainingdegrees":
                $className = "TrainingDegreeList";
                $displayName = "Training Degrees";
                break;
            case "trainingmajors":
                $className = "MajorTrainingList";
                $displayName = "Training Majors";
                break;
            case "trainingminors":
                $className = "MinorTrainingList";
                $displayName = "Training Minors";
                break;
            case "traininghonors":
                $className = "HonorTrainingList";
                $displayName = "Training Honors";
                break;
            case "fellowshiptitles":
                $className = "FellowshipTitleList";
                $displayName = "Professional Fellowship Titles";
                break;
            case "residencyspecialtys":
                $className = "ResidencySpecialty";
                $displayName = "Residency Specialties";
                break;
            case "fellowshipsubspecialtys":
                $className = "FellowshipSubspecialty";
                $displayName = "Fellowship Subspecialties";
                break;
            case "institutiontypes":
                $className = "InstitutionType";
                $displayName = "Institution Types";
                break;
            case "documenttypes":
                $className = "DocumentTypeList";
                $displayName = "Document Types";
                break;
            case "medicaltitles":
                $className = "MedicalTitleList";
                $displayName = "Medical Titles";
                break;
            case "medicalspecialties":
                $className = "MedicalSpecialties";
                $displayName = "Medical Specialties";
                break;
            case "sourceorganizations":
                $className = "SourceOrganization";
                $displayName = "Grant Source Organizations (Sponsors)";
                break;
            case "languages":
                $className = "LanguageList";
                $displayName = "Languages";
                break;
            case "locales":
                $className = "LocaleList";
                $displayName = "Locales";
                break;
            case "importances":
                $className = "ImportanceList";
                $displayName = "Ranks of Importance";
                break;
            case "authorshiproles":
                $className = "AuthorshipRoles";
                $displayName = "Authorship Roles";
                break;
            case "organizations":
                $className = "OrganizationList";
                $displayName = "Lecture Venues";
                break;
            case "cities":
                $className = "CityList";
                $displayName = "City";
                break;
            case "linktypes":
                $className = "LinkTypeList";
                $displayName = "Link Types";
                break;
            case "sexes":
                $className = "SexList";
                $displayName = "Sexes";
                break;
            case "positiontypes":
                $className = "PositionTypeList";
                $displayName = "Position Types";
                break;
            case "organizationalgrouptypes":
                $className = "OrganizationalGroupType";
                $displayName = "Organizational Group Types";
                break;
            case "commenttypes":
                $className = "CommentTypeList";
                $displayName = "Comment Types";
                break;
            case "commentgrouptypes":
                $className = "CommentGroupType";
                $displayName = "Profile Comment Group Types";
                break;
            case "userwrappers":
                $className = "UserWrapper";
                $displayName = "User Wrappers";
                break;
            case "spotpurposes":
                $className = "SpotPurpose";
                $displayName = "Spot Purposes";
                break;
            case "medicalstatuses":
                $className = "MedicalLicenseStatus";
                $displayName = "Medical License Statuses";
                break;
            case "certifyingboardorganizations":
                $className = "CertifyingBoardOrganization";
                $displayName = "Certifying Board Organizations";
                break;
            case "trainingtypes":
                $className = "TrainingTypeList";
                $displayName = "Training Types";
                break;
            case "joblists":
                $className = "JobTitleList";
                $displayName = "Job or Experience Title";
                break;
            case "fellappstatuses":
                $className = "FellAppStatus";
                $displayName = "Fellowship Application Statuses";
                $bundleName = "FellAppBundle";
                break;
            case "fellappranks":
                $className = "FellAppRank";
                $displayName = "Fellowship Application Ranks";
                $bundleName = "FellAppBundle";
                break;
            case "fellapplanguageproficiency":
                $className = "LanguageProficiency";
                $displayName = "Fellowship Application Language Proficiencies";
                $bundleName = "FellAppBundle";
                break;
//            case "collaborations":
//                $className = "Collaboration";
//                $displayName = "Collaborations";
//                break;
            case "collaborationtypes":
                $className = "CollaborationTypeList";
                $displayName = "Collaboration Types";
                break;
            case "permission":
                $className = "PermissionList";
                $displayName = "Permissions List";
                break;
            case "permissionobject":
                $className = "PermissionObjectList";
                $displayName = "Permission Objects List";
                break;
            case "permissionaction":
                $className = "PermissionActionList";
                $displayName = "Permission Actions List";
                break;
            case "sites":
                $className = "SiteList";
                $displayName = "Sites List";
                break;
            case "eventobjecttypes":
                $className = "EventObjectTypeList";
                $displayName = "Event Log Object Types";
                break;
            case "vacreqavailabilities":
                $className = "VacReqAvailabilityList";
                $displayName = "Availability List";
                $bundleName = "VacReqBundle";
                break;

            default:
                $className = null;
                $displayName = null;
                $labels = null;
        }

        //echo "className=".$className.", displayName=".$displayName."<br>";

        $res = array();
        $res['className'] = $className;
        $res['fullClassName'] = "Oleg\\".$bundleName."\\Entity\\".$className;
        $res['bundleName'] = "Oleg".$bundleName;
        $res['displayName'] = $displayName;
        //$res['labels'] = $labels;

        //check parent name
        $parentMapper = $this->getParentName($className);
        if( $parentMapper ) {
            $res['parentClassName'] = $parentMapper['className'];
        }

        return $res;
    }



    /////////////////// DELETE IS NOT USED /////////////////////////
    /**
     * Deletes a entity.
     *
     * @Route("/source-systems/{id}", name="sourcesystems_delete")
     * @Route("/roles/{id}", name="role_delete")
     * @Route("/institutions/{id}", name="institutions_delete")
     * @Route("/states/{id}", name="states_delete")
     * @Route("/countries/{id}", name="countries_delete")
     * @Route("/board-certifications/{id}", name="boardcertifications_delete")
     * @Route("/employment-termination-reasons/{id}", name="employmentterminations_delete")
     * @Route("/event-log-event-types/{id}", name="loggereventtypes_delete")
     * @Route("/primary-public-user-id-types/{id}", name="usernametypes_delete")
     * @Route("/identifier-types/{id}", name="identifiers_delete")
     * @Route("/residency-tracks/{id}", name="residencytracks_delete")
     * @Route("/fellowship-types/{id}", name="fellowshiptypes_delete")
//     * @Route("/research-labs/{id}", name="researchlabs_delete")
     * @Route("/location-types/{id}", name="locationtypes_delete")
     * @Route("/equipment/{id}", name="equipments_delete")
     * @Route("/equipment-types/{id}", name="equipmenttypes_delete")
     * @Route("/location-privacy-types/{id}", name="locationprivacy_delete")
     * @Route("/role-attributes/{id}", name="roleattributes_delete")
     * @Route("/buidlings/{id}", name="buildings_delete")
     * @Route("/rooms/{id}", name="rooms_delete")
     * @Route("/suites/{id}", name="suites_delete")
     * @Route("/floors/{id}", name="floors_delete")
     * @Route("/mailboxes/{id}", name="mailboxes_delete")
     * @Route("/percent-effort/{id}", name="efforts_delete")
     * @Route("/administrative-titles/{id}", name="admintitles_delete")
     * @Route("/academic-appointment-titles/{id}", name="apptitles_delete")
     * @Route("/training-completion-reasons/{id}", name="completionreasons_delete")
     * @Route("/training-degrees/{id}", name="trainingdegrees_delete")
     * @Route("/training-majors/{id}", name="trainingmajors_delete")
     * @Route("/training-minors/{id}", name="trainingminors_delete")
     * @Route("/training-honors/{id}", name="traininghonors_delete")
     * @Route("/fellowship-titles/{id}", name="fellowshiptitles_delete")
     * @Route("/residency-specialties/{id}", name="residencyspecialtys_delete")
     * @Route("/fellowship-subspecialties/{id}", name="fellowshipsubspecialtys_delete")
     * @Route("/institution-types/{id}", name="institutiontypes_delete")
     * @Route("/document-types/{id}", name="documenttypes_delete")
     * @Route("/medical-titles/{id}", name="medicaltitles_delete")
     * @Route("/medical-specialties/{id}", name="medicalspecialties_delete")
     * @Route("/employment-types/{id}", name="employmenttypes_delete")
     * @Route("/grant-source-organizations/{id}", name="sourceorganizations_delete")
     * @Route("/languages/{id}", name="languages_delete")
     * @Route("/locales/{id}", name="locales_delete")
     * @Route("/ranks-of-importance/{id}", name="importances_delete")
     * @Route("/authorship-roles/{id}", name="authorshiproles_delete")
     * @Route("/lecture-venues/{id}", name="organizations_delete")
     * @Route("/cities/{id}", name="cities_delete")
     * @Route("/link-types/{id}", name="linktypes_delete")
     * @Route("/sexes/{id}", name="sexes_delete")
     * @Route("/position-types/{id}", name="positiontypes_delete")
     * @Route("/organizational-group-types/{id}", name="organizationalgrouptypes_delete")
     * @Route("/profile-comment-group-types/{id}", name="commentgrouptypes_delete")
     * @Route("/comment-types/{id}", name="commenttypes_delete")
     * @Route("/user-wrappers/{id}", name="userwrappers_delete")
     * @Route("/spot-purposes/{id}", name="spotpurposes_delete")
     * @Route("/medical-license-statuses/{id}", name="medicalstatuses_delete")
     * @Route("/certifying-board-organizations/{id}", name="certifyingboardorganizations_delete")
     * @Route("/training-types/{id}", name="trainingtypes_delete")
     * @Route("/job-titles/{id}", name="joblists_delete")
     * @Route("/fellowship-application-statuses/{id}", name="fellappstatuses_delete")
     * @Route("/fellowship-application-ranks/{id}", name="fellappranks_delete")
     * @Route("/fellowship-application-language-proficiencies/{id}", name="fellapplanguageproficiency_delete")
//     * @Route("/collaborations/{id}", name="collaborations_delete")
     * @Route("/collaboration-types/{id}", name="collaborationtypes_delete")
     * @Route("/permissions/{id}", name="permission_delete")
     * @Route("/permission-objects/{id}", name="permissionobject_delete")
     * @Route("/permission-actions/{id}", name="permissionaction_delete")
     * @Route("/sites/{id}", name="sites_delete")
     * @Route("/event-object-types/{id}", name="eventobjecttypes_delete")
     * @Route("/vacreq-availabilities/{id}", name="vacreqavailabilities_delete")
     *
     *
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {

        return $this->redirect( $this->generateUrl($this->container->getParameter('employees.sitename').'-order-nopermission') );
        //return $this->deleteList($request, $id);
    }
    public function deleteList($request, $id) {

        $routeName = $request->get('_route');
        $pieces = explode("_", $routeName);
        $pathbase = $pieces[0];

        $mapper= $this->classListMapper($pathbase);

        $form = $this->createDeleteForm($id,$pathbase);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($mapper['bundleName'].':'.$mapper['className'])->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find '.$mapper['fullClassName'].' entity.');
            }

            $em->remove($entity);
            $em->flush();
        } else {
            //
        }

        return $this->redirect($this->generateUrl($pathbase));
    }

    /**
     * Creates a form to delete a entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id,$pathbase)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($pathbase.'_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete','attr'=>array('class'=>'btn btn-danger')))
            ->getForm()
        ;
    }
    /////////////////// DELETE IS NOT USED /////////////////////////

}
