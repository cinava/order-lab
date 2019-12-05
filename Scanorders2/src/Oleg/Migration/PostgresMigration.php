<?php

/**
 * Created by PhpStorm.
 * User: ch3
 * Date: 11/26/2019
 * Time: 3:31 PM
 */

namespace Oleg\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

//In VersionYYYYMMDDHHMM.php
//1) Add "use Oleg\Migration\PostgresMigration;"
//2) Rename after extends "AbstractMigration" to "PostgresMigration"
//3) Rename [addSql] to [processSql] (sed -i -e "s/addSql/processSql/g" Version....php)
class PostgresMigration extends AbstractMigration implements ContainerAwareInterface
{

    private $container;
    private $indexArr = array();
    private $counter = 0;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema){}
    public function down(Schema $schema){}

    //TODO: check if index exists in schema
    //https://www.doctrine-project.org/projects/doctrine-dbal/en/2.9/reference/schema-manager.html
    public function indexExists($sql) {

        $sqlIndex = null;
        $sqlArr = explode(" ",$sql);
        $newline = "\n";

        //Case: DROP INDEX idx_d267b39c33f7837
        if( strpos($sql, 'DROP INDEX ') !== false ) {
            if( count($sqlArr) == 3 ) {
                //We need the index 3
                $sqlIndex = $sqlArr[2];
            }
        }

        //ALTER INDEX idx_15b668721aca1422 RENAME TO IDX_5AFC0F4BCD46F646
        if( strpos($sql, 'ALTER INDEX ') !== false && strpos($sql, ' RENAME TO ') !== false ) {
            if( count($sqlArr) == 6 ) {
                //We need the index 3
                $sqlIndex = $sqlArr[2];
            }
        }

        //CREATE UNIQUE INDEX UNIQ_D267B39C33F7837 ON calllog_calllogentrymessage_document (document_id)
        //CREATE UNIQUE INDEX UNIQ_22984163C33F7837 ON fellapp_reference_document (document_id)
        if( strpos($sql, 'CREATE UNIQUE INDEX ') !== false && strpos($sql, ' ON ') !== false ) {
            if( count($sqlArr) >= 7 ) {
                //We need the index 4
                $sqlIndex = $sqlArr[3];
            }
        }

        //ALTER TABLE transres_siteparameters ADD testuser INT DEFAULT NULL
        if( strpos($sql, 'ALTER TABLE ') !== false && strpos($sql, ' ADD ') !== false ) {
            //No index to verify, just add not existing column => return TRUE
            return TRUE;
        }

        //'ALTER TABLE transres_siteparameters ALTER id DROP DEFAULT'
        if( strpos($sql, 'ALTER TABLE ') !== false && strpos($sql, ' ALTER ') !== false && strpos($sql, ' DROP ') !== false ) {
            //No index to verify, just DROP parameter on existing column => return TRUE
            return TRUE;
        }

        //ALTER TABLE transres_siteparameters ADD CONSTRAINT FK_74EBD22819B7BC4A FOREIGN KEY (testUser) REFERENCES user_fosuser (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        if( strpos($sql, 'ALTER TABLE ') !== false && strpos($sql, ' ADD CONSTRAINT ') !== false && strpos($sql, ' FOREIGN KEY ') !== false ) {
            if( count($sqlArr) >= 12 ) {
                //We need the index 6
                $sqlIndex = $sqlArr[5];
            }
        }

        //CREATE INDEX IDX_74EBD22819B7BC4A ON transres_siteparameters (testUser)
        if( strpos($sql, 'CREATE INDEX ') !== false && strpos($sql, ' ON ') ) {
            if( count($sqlArr) >= 6 ) {
                //We need the index 3
                $sqlIndex = $sqlArr[2];
            }
        }

        //Skip:
        //ALTER TABLE calllog_calllogentrymessage_document ADD PRIMARY KEY (message_id, document_id)

        //index from sql not found => assume it does not exist
        if( !$sqlIndex ) {
            if( preg_match('/[0-9]/', $sql) ) {
                //echo 'Contains at least one number';
                echo $this->counter.": !!!Index not found in ".$sql.$newline;
            }

            return false;
        }

        //echo "Index=".$sqlIndex."; sql=".$sql.$newline;

        return $this->indexExistsSimple($sqlIndex);
    }
    public function indexExistsSimple($sqlIndex) {
        $newline = "\n";
        foreach( $this->indexArr as $index=>$table ) {
            //echo $index->getName() . ': ' . ($index->isUnique() ? 'unique' : 'not unique') . "\n";
            if( strtolower($sqlIndex) == strtolower($index) ) {
                echo $this->counter.": Found index=".$sqlIndex." (".$table.").".$newline;
                return true;
            }
        }
        echo $this->counter.": NotFound index=".$sqlIndex." (".$table.").".$newline;
        return false;
    }

    public function createIndexArr() {
        $newline = "\n";
        $em = $this->container->get('doctrine.orm.entity_manager');
        $sm = $em->getConnection()->getSchemaManager();
        $tables = $sm->listTables();
        //ALTER INDEX idx_15b668721aca1422 RENAME TO IDX_5AFC0F4BCD46F646
        foreach ($tables as $table) {
            $indexes = $sm->listTableIndexes($table->getName());
            foreach ($indexes as $index) {
                //echo $index->getName() . ': ' . ($index->isUnique() ? 'unique' : 'not unique') . "\n";
                $this->indexArr[$index->getName()] = $table->getName();
            }
        }
        echo "Found " . count($this->indexArr) . " indexes in " . count($tables) . " tables" . $newline;
    }


    public function processSql($sql) {
        $this->counter++;
        $this->processComplexSql($sql,TRUE);

        //testing
        //CREATE UNIQUE INDEX UNIQ_D267B39C33F7837 ON calllog_calllogentrymessage_document (document_id)
        //if( $sql == "CREATE UNIQUE INDEX UNIQ_D267B39C33F7837 ON calllog_calllogentrymessage_document (document_id)" ) {
        //    exit("Testing exit sql=".$sql);
        //}
//        if( strpos($sql, 'UNIQ_22984163C33F7837') !== FALSE ) {
//            exit("Testing exit sql=".$sql);
//        }
    }
//    public function processSimpleSql($sql) {
//        $this->processComplexSql($sql,null);
//    }

    //TODO: Skip a statement in a Doctrine migration if a index is present
    //https://stackoverflow.com/questions/49897499/skip-a-statement-in-a-doctrine-migration-if-a-column-is-present
    public function processComplexSql($sql,$useSchema=FALSE) {
        //wrapper for processSql

        $newline = "\n";

        //Always skip: An exception occurred while executing 'DROP INDEX "primary"':
        if( $sql == 'DROP INDEX "primary"' ) {
            echo $this->counter.":###Ignore1 ".$sql.$newline;
            return FALSE;
        }
//        if( strpos($sql, 'DROP INDEX ') !== false && strpos($sql, 'primary') !== false ) {
//            return false;
//        }

        //Always skip: Primary keys are already exists
        if( strpos($sql, ' ADD PRIMARY KEY ') !== FALSE ) {
            echo $this->counter.":###Ignore2 ".$sql.$newline;
            return FALSE;
        }

        if( $useSchema ) {

            if( count($this->indexArr) == 0 ) {
                $this->createIndexArr();
            }

            //if( strpos($sql, 'CREATE UNIQUE INDEX ') !== FALSE ) {
            if(
                $this->SpecialCaseExists($sql,'CREATE UNIQUE INDEX ') ||
                $this->SpecialCaseExists($sql,' ADD CONSTRAINT ') ||
                $this->SpecialCaseExists($sql,'CREATE INDEX ')
            ) {
                //exception SQL: if index does not exists => create index
                if( $this->indexExists($sql) === FALSE ) {
                    //index does not exists => ok create index
                } else {
                    echo $this->counter.":############Ignore3a " . $sql . $newline;
                    return FALSE;
                }
            } else {
                //All others SQL
                if( $this->indexExists($sql) === FALSE ) {
                    //echo $this->counter.":###Ignore3b " . $sql . $newline;
                    return FALSE;
                }
            }

        } else {
            //ALTER INDEX idx_e573a753bdd0acfa RENAME TO IDX_6BE23A97726D9566
            if (strpos($sql, ' RENAME TO IDX_') !== FALSE) {
                //has string
                //it's ok to rename with 5 zeros '00000': ALTER INDEX idx_c6f1cf80537a132900000 RENAME TO IDX_C6F1CF80537A1329
                if (strpos($sql, '00000') !== FALSE) {
                    //has 00000
                    $this->addSql($sql);
                    return FALSE;
                } else {
                    //does not have 00000
                    echo $this->counter.":###Ignore4 " . $sql . $newline;
                    return FALSE;
                }
            }

            //DROP INDEX IDX_22984163C33F7837
            if (strpos($sql, 'DROP INDEX IDX_') !== false) {
                return false;
            }
        }

        echo $this->counter.": Process sql=".$sql.$newline;
        $this->addSql($sql);
    }

    public function SpecialCaseExists($sql,$sqlSubstring) {
        if( strpos($sql, $sqlSubstring) !== FALSE ) {
            return TRUE;
        }
        return FALSE;
    }












    public function indexExistsSimpleFromScratch($sqlIndex) {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $sm = $em->getConnection()->getSchemaManager();

        $tables = $sm->listTables();
        //ALTER INDEX idx_15b668721aca1422 RENAME TO IDX_5AFC0F4BCD46F646
        foreach ($tables as $table) {
            $indexes = $sm->listTableIndexes($table->getName());
            foreach ($indexes as $index) {
                //echo $index->getName() . ': ' . ($index->isUnique() ? 'unique' : 'not unique') . "\n";
                if( strtolower($sqlIndex) == strtolower($index) ) {
                    return true;
                }
            }
        }
        return false;
    }

    //addSql($sql, array $params = Array, array $types = Array)
    //public function processSql( $sql, array $params = [], array $types = [] ) {

    public function processSql_old( $sql ) {
        //wrapper for addSql

        //An exception occurred while executing 'DROP INDEX "primary"':
//        if( $sql == 'DROP INDEX "primary"' ) {
//            return false;
//        }
        if( strpos($sql, 'DROP INDEX ') !== false && strpos($sql, 'primary') !== false ) {
            return false;
        }

        if( strpos($sql, ' ADD PRIMARY KEY ') !== false ) {
            return false;
        }

        //'ALTER INDEX idx_e573a753bdd0acfa RENAME TO IDX_6BE23A97726D9566'
        if( strpos($sql, ' RENAME TO IDX_') !== false ) {
            //has string
            //it's ok to rename with 5 zeros '00000': ALTER INDEX idx_c6f1cf80537a132900000 RENAME TO IDX_C6F1CF80537A1329
            if( strpos($sql, '00000') !== false ) {
                //has 00000
                $this->addSql($sql);
                return false;
            } else {
                //does not have 00000
                echo "###Ignore ".$sql;
                return false;
            }
        }

        //Drop index DROP INDEX IDX_22984163C33F7837
        if( strpos($sql, 'DROP INDEX IDX_') !== false ) {
            return false;
        }

        $this->addSql($sql);
    }



}