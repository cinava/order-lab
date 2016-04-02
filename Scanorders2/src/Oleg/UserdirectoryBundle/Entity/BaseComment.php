<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oli2002
 * Date: 10/24/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Oleg\UserdirectoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\MappedSuperclass
 */
abstract class BaseComment extends BaseUserAttributes {


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="CommentTypeList", cascade={"persist"})
     **/
    private $commentType;

    /**
     * This field is required for orderby functionality
     * @ORM\Column(type="string", nullable=true)
     **/
    private $commentTypeStr;

//    /**
//     * @ORM\ManyToOne(targetEntity="CommentSubTypeList", cascade={"persist"})
//     **/
//    private $commentSubType;





    public function __construct($author=null) {
        parent::__construct($author);
    }




    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

//    /**
//     * @param mixed $commentSubType
//     */
//    public function setCommentSubType($commentSubType)
//    {
//        $this->commentSubType = $commentSubType;
//    }
//    public function setCommentSubTypeList($commentSubType)
//    {
//        $this->setCommentSubType($commentSubType);
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getCommentSubType()
//    {
//        return $this->commentSubType;
//    }

    /**
     * @param mixed $commentType
     */
    public function setCommentType($commentType)
    {
        $this->commentType = $commentType;

        //set commentTypeStr
        if( $commentType && $commentType->getName()."" != "" ) {
            $this->setCommentTypeStr($commentType->getName()."");
        }

    }
    public function setCommentTypeList($commentType)
    {
        $this->setCommentType($commentType);
    }

    /**
     * @return mixed
     */
    public function getCommentType()
    {
        return $this->commentType;
    }

    /**
     * @param mixed $commentTypeStr
     */
    public function setCommentTypeStr($commentTypeStr)
    {
        $this->commentTypeStr = $commentTypeStr;
    }

    /**
     * @return mixed
     */
    public function getCommentTypeStr()
    {
        return $this->commentTypeStr;
    }




    /**
     * Add document
     *
     * @param \Oleg\UserdirectoryBundle\Entity\Document $document
     * @return Comment
     */
    public function addDocument($document)
    {
//        if( $document == null ) {
//            $document = new Document($this->getAuthor());
//        }

        if( $document && !$this->documents->contains($document) ) {
            $this->documents->add($document);
            $document->createUseObject($this);
        }

        return $this;
    }
    /**
     * Remove document
     *
     * @param \Oleg\UserdirectoryBundle\Entity\Document $document
     */
    public function removeDocument($document)
    {
        $this->documents->removeElement($document);
        $document->clearUseObject();
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    public function setDocuments($documents)
    {
        $this->documents = new ArrayCollection();
        foreach( $documents as $document ) {
            $this->addDocument($document);
        }
    }

}