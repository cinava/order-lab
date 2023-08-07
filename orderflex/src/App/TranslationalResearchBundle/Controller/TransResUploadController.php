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
 * User: oli2002
 * Date: 10/15/14
 * Time: 11:57 AM
 */

namespace App\TranslationalResearchBundle\Controller;

use App\UserdirectoryBundle\Util\LargeFileDownloader;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\Routing\Annotation\Route;

use App\UserdirectoryBundle\Controller\UploadController;
use Symfony\Component\HttpFoundation\Response;


class TransResUploadController extends UploadController {

    #[Route(path: '/file-delete', name: 'translationalresearch_file_delete', methods: ['GET', 'POST', 'DELETE'])]
    public function deleteFileAction(Request $request) {
        return $this->deleteFileMethod($request);
    }

    /**
     * $id - document id
     */
    #[Route(path: '/file-download/{id}/{eventtype}', name: 'translationalresearch_file_download', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function downloadFileAction(Request $request,$id,$eventtype=null) {
        return $this->downloadFileMethod($request,$id,$this->getParameter('translationalresearch.sitename'),$eventtype);
    }


    /**
     * $id - document id
     */
    #[Route(path: '/file-view/{id}/{viewType}/{eventtype}', name: 'translationalresearch_file_view', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function viewFileAction(Request $request,$id,$eventtype=null, $viewType=null) {
        //exit("viewType=".$viewType);
        return $this->viewFileMethod($request,$id,$this->getParameter('translationalresearch.sitename'),$eventtype,$viewType);
    }


} 