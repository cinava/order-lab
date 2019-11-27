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

//In VersionYYYYMMDDHHMM.php
//1) Add "use Oleg\Migration\PostgresMigration;"
//2) Rename after extends "AbstractMigration" to "PostgresMigration"
//3) Rename addSql to processSql
class PostgresMigration extends AbstractMigration
{

    public function up(Schema $schema)
    {

    }

    public function down(Schema $schema)
    {

    }

    public function indexExists($sql,$schema) {

        $index = null;

        //DROP INDEX idx_d267b39c33f7837
        $sqlArr = explode(" ",$sql);

        if( strpos($sql, 'DROP INDEX ') !== false ) {
            if( count($sqlArr) == 3 ) {
                $index = $sqlArr[2];
            }
        }

        //ALTER INDEX idx_15b668721aca1422 RENAME TO IDX_5AFC0F4BCD46F646

        //ALTER TABLE calllog_calllogentrymessage_document ADD PRIMARY KEY (message_id, document_id)

        if (!$schema->getTable('your_table')->hasColumn('your_column')) {
            // do your code
        }
    }

    //TODO: Skip a statement in a Doctrine migration if a index is present
    //https://stackoverflow.com/questions/49897499/skip-a-statement-in-a-doctrine-migration-if-a-column-is-present
    public function processSql( $schema, $sql ) {
        //wrapper for processSql

        //An exception occurred while executing 'DROP INDEX "primary"':
        if( $sql == 'DROP INDEX "primary"' ) {
            return false;
        }
//        if( strpos($sql, 'DROP INDEX ') !== false && strpos($sql, 'primary') !== false ) {
//            return false;
//        }

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
                echo "ignore ".$sql;
                return false;
            }
        }

        //Drop index DROP INDEX IDX_22984163C33F7837
        if( strpos($sql, 'DROP INDEX IDX_') !== false ) {
            return false;
        }

        $this->addSql($sql);
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
                echo "ignore ".$sql;
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