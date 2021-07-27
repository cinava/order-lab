<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use App\Migration\PostgresMigration;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210727211516 extends PostgresMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellapp_coverletter ADD PRIMARY KEY (fellApp_id, coverLetter_id)');
        $this->processSql('ALTER INDEX idx_a95c4269b3e07c1d RENAME TO IDX_263579C13111FEBE');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellapp_cv ADD PRIMARY KEY (fellApp_id, cv_id)');
        $this->processSql('ALTER INDEX idx_3429326acfe419e2 RENAME TO IDX_3385375CCFE419E2');
        $this->processSql('ALTER INDEX idx_14f0c7a72a90cb41 RENAME TO IDX_7A4E80292D3CCE77');
        $this->processSql('ALTER INDEX idx_2e24ab552a90cb41 RENAME TO IDX_F1D6BF172D3CCE77');
        $this->processSql('ALTER INDEX idx_badd2c672a90cb41 RENAME TO IDX_F5802FB72D3CCE77');
        $this->processSql('ALTER INDEX idx_f844cf582a90cb41 RENAME TO IDX_328CB2B62D3CCE77');
        $this->processSql('ALTER INDEX idx_49db28d62a90cb41 RENAME TO IDX_FD0966152D3CCE77');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellapp_document ADD PRIMARY KEY (fellApp_id, document_id)');
        $this->processSql('ALTER INDEX idx_5183dcd7c33f7837 RENAME TO IDX_49B2072FC33F7837');
        $this->processSql('ALTER INDEX idx_5e7a41de2a90cb41 RENAME TO IDX_ED040A2B2D3CCE77');
        $this->processSql('ALTER INDEX idx_128512462a90cb41 RENAME TO IDX_AB4C9BE2D3CCE77');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellapp_avatar ADD PRIMARY KEY (fellApp_id, avatar_id)');
        $this->processSql('ALTER INDEX idx_688529cc86383b10 RENAME TO IDX_27D82A1C86383B10');
        $this->processSql('ALTER INDEX idx_544c3f747e4972b4 RENAME TO IDX_4EB06F7FB32D0A6C');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellowshipapplication_examination ADD PRIMARY KEY (fellowshipApplication_id, examination_id)');
        $this->processSql('ALTER INDEX idx_602a117bdad0cfbf RENAME TO IDX_62CF1DE3DAD0CFBF');
        $this->processSql('ALTER INDEX idx_dfc03c307e4972b4 RENAME TO IDX_C53C6C3BB32D0A6C');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellowshipapplication_citizenship ADD PRIMARY KEY (fellowshipApplication_id, citizenship_id)');
        $this->processSql('ALTER INDEX idx_ac0f1100c9709c85 RENAME TO IDX_AEEA1D98C9709C85');
        $this->processSql('ALTER INDEX idx_8beaa2047e4972b4 RENAME TO IDX_BA8165E2B32D0A6C');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_fellowshipapplication_boardcertification ADD PRIMARY KEY (fellowshipApplication_id, boardCertification_id)');
        $this->processSql('ALTER INDEX idx_7b3ba6a5c2b1f452 RENAME TO IDX_299D9554D58E8F2F');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_googleformconfig_fellowshipsubspecialty ADD PRIMARY KEY (googleformconfig_id, fellowshipsubspecialty_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE fellapp_reference_document ADD PRIMARY KEY (reference_id, document_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE calllog_calllogentrymessage_document ADD PRIMARY KEY (message_id, document_id)');
        $this->processSql('ALTER INDEX idx_15b668721aca1422 RENAME TO IDX_5AFC0F4BCD46F646');
        $this->processSql('ALTER INDEX idx_156240fb3d3c30d3 RENAME TO IDX_DADF79673D3C30D3');
        $this->processSql('ALTER INDEX encounter_unique00000 RENAME TO encounter_unique');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_encounter ADD PRIMARY KEY (message_id, encounter_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_accession ADD PRIMARY KEY (message_id, accession_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_block ADD PRIMARY KEY (message_id, block_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_imaging ADD PRIMARY KEY (message_id, imaging_id)');
        $this->processSql('ALTER INDEX idx_6700c13e537a1329 RENAME TO IDX_E5F1439D537A1329');
        $this->processSql('ALTER INDEX idx_79d11d14537a1329 RENAME TO IDX_FB209FB7537A1329');
        $this->processSql('ALTER INDEX idx_3ec324c3537a1329 RENAME TO IDX_BC32A660537A1329');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_editors ADD PRIMARY KEY (message_id, editorInfo_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_input ADD PRIMARY KEY (message_id, input_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_associations ADD PRIMARY KEY (message_id, association_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_message_destination ADD PRIMARY KEY (message_id, destination_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_messagecategory_formnode ADD PRIMARY KEY (messageCategory_id, formNode_id)');
        $this->processSql('ALTER INDEX idx_4aae944a88dbad51 RENAME TO IDX_9160929476694CD');
        $this->processSql('ALTER INDEX idx_8e61413f88dbad51 RENAME TO IDX_9FD813AB476694CD');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_partpaper_document ADD PRIMARY KEY (partpaper_id, document_id)');
        $this->processSql('ALTER INDEX part_unique00000 RENAME TO part_unique');
        $this->processSql('ALTER INDEX idx_f6616091373182ea RENAME TO IDX_70FDEF46F88CBB76');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE scan_persitesettings_institution ADD PRIMARY KEY (perSiteSettings_id, institution_id)');
        $this->processSql('ALTER INDEX idx_d956bfe410405986 RENAME TO IDX_3D7C119510405986');
        $this->processSql('ALTER INDEX idx_d58d7e02bcfb922f RENAME TO IDX_D84E5574B2A22366');
        $this->processSql('ALTER INDEX idx_a24210c9bcfb922f RENAME TO IDX_B57D6BB4B2A22366');
        $this->processSql('ALTER INDEX procedure_unique00000 RENAME TO procedure_unique');
        $this->processSql('ALTER INDEX idx_b7ab567ba66bd30d RENAME TO IDX_F8E1314271E73169');
        $this->processSql('ALTER INDEX idx_f64015367909e1ed RENAME TO IDX_39FD2CAA7909E1ED');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_antibody_document ADD PRIMARY KEY (request_id, document_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_invoice_document ADD PRIMARY KEY (invoice_id, document_id)');
        $this->processSql('ALTER TABLE transres_project ADD expirationNotifyCounter INT DEFAULT NULL');
        $this->processSql('ALTER TABLE transres_project ADD expiredNotifyCounter INT DEFAULT NULL');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_principalinvestigator ADD PRIMARY KEY (project_id, principalinvestigator_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_coinvestigator ADD PRIMARY KEY (project_id, coinvestigator_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_pathologist ADD PRIMARY KEY (project_id, pathologist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_contact ADD PRIMARY KEY (project_id, contact_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_document ADD PRIMARY KEY (project_id, document_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_irbapprovalletter ADD PRIMARY KEY (project_id, irbApprovalLetters_id)');
        $this->processSql('ALTER INDEX idx_f11d06226072379a RENAME TO IDX_3BD57BCC72698C7A');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_project_humantissueform ADD PRIMARY KEY (project_id, humanTissueForm_id)');
        $this->processSql('ALTER INDEX idx_117b33b4debe2636 RENAME TO IDX_FE149F5AA27D545F');
        $this->processSql('ALTER INDEX idx_22b6422d166d1f9c RENAME TO IDX_C3A8494B166D1F9C');
        $this->processSql('ALTER INDEX idx_c4c1047e166d1f9c RENAME TO IDX_B7C3DE2166D1F9C');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_request_principalinvestigator ADD PRIMARY KEY (request_id, principalinvestigator_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_request_document ADD PRIMARY KEY (request_id, document_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_request_packingslippdf ADD PRIMARY KEY (request_id, packingSlipPdf_id)');
        $this->processSql('ALTER INDEX idx_f2120a63ff061bbc RENAME TO IDX_5E2751FB7F71D5A');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_request_oldpackingslippdf ADD PRIMARY KEY (request_id, oldPackingSlipPdf_id)');
        $this->processSql('ALTER INDEX idx_2a7da8f1d8164bd RENAME TO IDX_10E9AF556BD350A1');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_request_antibody ADD PRIMARY KEY (request_id, antibody_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_request_businesspurpose ADD PRIMARY KEY (request_id, businessPurpose_id)');
        $this->processSql('ALTER INDEX idx_45e86e982b3ab653 RENAME TO IDX_8A5557046467B583');
        $this->processSql('ALTER INDEX siteparameters_unique00000 RENAME TO siteParameters_unique');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_transressiteparameters_transreslogo ADD PRIMARY KEY (transResSiteParameter_id, transresLogo_id)');
        $this->processSql('ALTER INDEX idx_77e35b187408ff6 RENAME TO IDX_F898B8B948FDB66A');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE transres_transressiteparameters_transrespackingsliplogo ADD PRIMARY KEY (transResSiteParameter_id, transresPackingSlipLogo_id)');
        $this->processSql('ALTER INDEX idx_e1d041ec7a315316 RENAME TO IDX_7ECB11F76E565D6D');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_accountrequest_institution ADD PRIMARY KEY (request_id, institution_id)');
        $this->processSql('ALTER INDEX idx_465c3939834995b1 RENAME TO IDX_BF2A5B6F834995B1');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_documentcontainer_document ADD PRIMARY KEY (documentcontainer_id, document_id)');
        $this->processSql('ALTER INDEX idx_fb465c67c5b7a34a RENAME TO IDX_F172E05AB974D123');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_fellowshipsubspecialty_coordinator ADD PRIMARY KEY (fellowshipSubspecialty_id, coordinator_id)');
        $this->processSql('ALTER INDEX idx_8e5bd5b6e7877946 RENAME TO IDX_708D2BCCE7877946');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_fellowshipsubspecialty_director ADD PRIMARY KEY (fellowshipSubspecialty_id, director_id)');
        $this->processSql('ALTER INDEX idx_68324fb8899fb366 RENAME TO IDX_FFFA6A60899FB366');
        $this->processSql('ALTER INDEX idx_e8b806bc8e87796 RENAME TO IDX_166EF8C6802D4908');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_users_researchlabs ADD PRIMARY KEY (user_id, researchlab_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_users_grants ADD PRIMARY KEY (user_id, grant_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_users_publications ADD PRIMARY KEY (user_id, publication_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_users_books ADD PRIMARY KEY (user_id, book_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_collaborationinstitution_collaboration ADD PRIMARY KEY (collaborationInstitution_id, collaboration_id)');
        $this->processSql('ALTER INDEX idx_33047118ef1544ce RENAME TO IDX_832A3FC4EF1544CE');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_location_assistant ADD PRIMARY KEY (location_id, assistant_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_logger_institutions ADD PRIMARY KEY (logger_id, institution_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_medicaltitle_medicalspeciality ADD PRIMARY KEY (medicaltitle_id, medicalspeciality_id)');
        $this->processSql('ALTER INDEX idx_759d120b38d5860e RENAME TO IDX_D1ADC86DC1A3E458');
        $this->processSql('ALTER INDEX idx_3681a952ca5ecd96 RENAME TO IDX_52955BA8DC6F0D8');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_organizationalgroupdefault_permittedinstitutionalphiscope ADD PRIMARY KEY (permittedInstitutionalPHIScope_id, institution_id)');
        $this->processSql('ALTER INDEX idx_205b297510405986 RENAME TO IDX_78626FE310405986');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_organizationalgroupdefault_language ADD PRIMARY KEY (organizationalgroupdefault_id, languagelist_id)');
        $this->processSql('ALTER INDEX idx_a4cc6e35d88ec86e RENAME TO IDX_243B3090D88EC86E');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_organizationalgroupdefault_locationtype ADD PRIMARY KEY (organizationalgroupdefault_id, locationtypelist_id)');
        $this->processSql('ALTER INDEX idx_fc2629a3d296b97 RENAME TO IDX_A5107C4D3D296B97');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_permission_institution ADD PRIMARY KEY (permission_id, institution_id)');
        $this->processSql('ALTER INDEX idx_d4bb041eb9556f54 RENAME TO IDX_8CD97CB3C5961D3D');
        $this->processSql('ALTER INDEX platformlist_unique00000 RENAME TO platformlist_unique');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_userpreferences_languages ADD PRIMARY KEY (userpreferences_id, languagelist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_preferences_institutions ADD PRIMARY KEY (preferences_id, institution_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_roles_attributes ADD PRIMARY KEY (roles_id, roleattributelist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_rooms_floors ADD PRIMARY KEY (roomlist_id, floorlist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_rooms_buildings ADD PRIMARY KEY (roomlist_id, buildinglist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_sites_lowestroles ADD PRIMARY KEY (site_id, role_id)');
        $this->processSql('ALTER INDEX idx_64afd0fed60322ac RENAME TO IDX_A56EFFFAD60322AC');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_site_document ADD PRIMARY KEY (site_id, document_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_siteparameter_platformlogo ADD PRIMARY KEY (siteParameter_id, platformLogo_id)');
        $this->processSql('ALTER INDEX idx_89f2aef6ea5be894 RENAME TO IDX_29C001C825E6D108');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_siteparameter_emailcriticalerrorexceptionuser ADD PRIMARY KEY (siteparameter_id, exceptionuser_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_suites_floors ADD PRIMARY KEY (suitelist_id, floorlist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_suites_buildings ADD PRIMARY KEY (suitelist_id, buildinglist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_trainings_majors ADD PRIMARY KEY (training_id, majortraininglist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_trainings_minors ADD PRIMARY KEY (training_id, minortraininglist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_trainings_honors ADD PRIMARY KEY (training_id, honortraininglist_id)');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE user_userpositions_positiontypes ADD PRIMARY KEY (userposition_id, positiontypelist_id)');
        $this->processSql('ALTER INDEX idx_a29abe84aae046c8 RENAME TO IDX_61BE9D18AAE046C8');
        $this->processSql('DROP INDEX "primary"');
        $this->processSql('ALTER TABLE vacreq_settings_user ADD PRIMARY KEY (settings_id, emailuser_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->processSql('CREATE SCHEMA public');
        $this->processSql('ALTER INDEX idx_7a4e80292d3cce77 RENAME TO IDX_14F0C7A72A90CB41');
        $this->processSql('DROP INDEX pk__fellapp___8efd92876c0d4efa');
        $this->processSql('ALTER TABLE fellapp_fellApp_document ADD PRIMARY KEY (document_id, fellapp_id)');
        $this->processSql('ALTER INDEX idx_49b2072fc33f7837 RENAME TO IDX_5183DCD7C33F7837');
        $this->processSql('DROP INDEX pk__fellapp___09b4914b61e7e62e');
        $this->processSql('ALTER TABLE fellapp_fellApp_avatar ADD PRIMARY KEY (avatar_id, fellapp_id)');
        $this->processSql('ALTER INDEX idx_27d82a1c86383b10 RENAME TO IDX_688529CC86383B10');
        $this->processSql('DROP INDEX pk__fellapp___4ab74df40c4b8cc9');
        $this->processSql('ALTER TABLE fellapp_fellowshipApplication_boardCertification ADD PRIMARY KEY (boardcertification_id, fellowshipapplication_id)');
        $this->processSql('ALTER INDEX idx_299d9554d58e8f2f RENAME TO IDX_7B3BA6A5C2B1F452');
        $this->processSql('ALTER INDEX idx_328cb2b62d3cce77 RENAME TO IDX_F844CF582A90CB41');
        $this->processSql('DROP INDEX pk__fellapp___2bad743366caf130');
        $this->processSql('ALTER TABLE fellapp_fellApp_cv ADD PRIMARY KEY (cv_id, fellapp_id)');
        $this->processSql('ALTER INDEX idx_3385375ccfe419e2 RENAME TO IDX_3429326ACFE419E2');
        $this->processSql('ALTER INDEX idx_4eb06f7fb32d0a6c RENAME TO IDX_544C3F747E4972B4');
        $this->processSql('DROP INDEX pk__fellapp___88a87edecd56af33');
        $this->processSql('ALTER TABLE fellapp_fellowshipApplication_examination ADD PRIMARY KEY (examination_id, fellowshipapplication_id)');
        $this->processSql('ALTER INDEX idx_62cf1de3dad0cfbf RENAME TO IDX_602A117BDAD0CFBF');
        $this->processSql('ALTER INDEX idx_ab4c9be2d3cce77 RENAME TO IDX_128512462A90CB41');
        $this->processSql('DROP INDEX pk__fellapp___a7ce995aacd4854f');
        $this->processSql('ALTER TABLE fellapp_fellowshipApplication_citizenship ADD PRIMARY KEY (citizenship_id, fellowshipapplication_id)');
        $this->processSql('ALTER INDEX idx_aeea1d98c9709c85 RENAME TO IDX_AC0F1100C9709C85');
        $this->processSql('ALTER INDEX idx_ba8165e2b32d0a6c RENAME TO IDX_8BEAA2047E4972B4');
        $this->processSql('ALTER INDEX idx_c53c6c3bb32d0a6c RENAME TO IDX_DFC03C307E4972B4');
        $this->processSql('DROP INDEX pk__calllog___d2d9006cd26843c2');
        $this->processSql('ALTER TABLE calllog_calllogentrymessage_document ADD PRIMARY KEY (document_id, message_id)');
        $this->processSql('ALTER INDEX idx_ed040a2b2d3cce77 RENAME TO IDX_5E7A41DE2A90CB41');
        $this->processSql('ALTER INDEX idx_f1d6bf172d3cce77 RENAME TO IDX_2E24AB552A90CB41');
        $this->processSql('ALTER INDEX idx_f5802fb72d3cce77 RENAME TO IDX_BADD2C672A90CB41');
        $this->processSql('ALTER INDEX idx_fd0966152d3cce77 RENAME TO IDX_49DB28D62A90CB41');
        $this->processSql('ALTER INDEX idx_9160929476694cd RENAME TO IDX_4AAE944A88DBAD51');
        $this->processSql('ALTER INDEX idx_9fd813ab476694cd RENAME TO IDX_8E61413F88DBAD51');
        $this->processSql('ALTER INDEX idx_b57d6bb4b2a22366 RENAME TO IDX_A24210C9BCFB922F');
        $this->processSql('ALTER INDEX encounter_unique RENAME TO encounter_unique00000');
        $this->processSql('ALTER INDEX idx_dadf79673d3c30d3 RENAME TO IDX_156240FB3D3C30D3');
        $this->processSql('DROP INDEX pk__scan_mes__e7607da6e5168bbc');
        $this->processSql('ALTER TABLE scan_message_encounter ADD PRIMARY KEY (encounter_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__159b6203a22c0690');
        $this->processSql('ALTER TABLE scan_message_accession ADD PRIMARY KEY (accession_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__c1d888a170878e78');
        $this->processSql('ALTER TABLE scan_message_block ADD PRIMARY KEY (block_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__40cebaf52dc1a81e');
        $this->processSql('ALTER TABLE scan_message_associations ADD PRIMARY KEY (association_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__338f038ae84631cf');
        $this->processSql('ALTER TABLE scan_message_editors ADD PRIMARY KEY (editorinfo_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__0eef7bdf3ae540b7');
        $this->processSql('ALTER TABLE scan_message_destination ADD PRIMARY KEY (destination_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__4e177218167dde59');
        $this->processSql('ALTER TABLE scan_messageCategory_formNode ADD PRIMARY KEY (formnode_id, messagecategory_id)');
        $this->processSql('DROP INDEX pk__scan_mes__44f76bebfb8170ef');
        $this->processSql('ALTER TABLE scan_message_imaging ADD PRIMARY KEY (imaging_id, message_id)');
        $this->processSql('DROP INDEX pk__scan_mes__6b87801903e45e2f');
        $this->processSql('ALTER TABLE scan_message_input ADD PRIMARY KEY (input_id, message_id)');
        $this->processSql('ALTER INDEX idx_e5f1439d537a1329 RENAME TO IDX_6700C13E537A1329');
        $this->processSql('ALTER INDEX idx_fb209fb7537a1329 RENAME TO IDX_79D11D14537A1329');
        $this->processSql('DROP INDEX pk__scan_par__557c9e9ef2622a88');
        $this->processSql('ALTER TABLE scan_partpaper_document ADD PRIMARY KEY (document_id, partpaper_id)');
        $this->processSql('ALTER INDEX part_unique RENAME TO part_unique00000');
        $this->processSql('ALTER INDEX idx_70fdef46f88cbb76 RENAME TO IDX_F6616091373182EA');
        $this->processSql('DROP INDEX pk__scan_per__666b204c33f2b2f0');
        $this->processSql('ALTER TABLE scan_perSiteSettings_institution ADD PRIMARY KEY (institution_id, persitesettings_id)');
        $this->processSql('ALTER INDEX idx_3d7c119510405986 RENAME TO IDX_D956BFE410405986');
        $this->processSql('ALTER INDEX idx_f8e1314271e73169 RENAME TO IDX_B7AB567BA66BD30D');
        $this->processSql('ALTER INDEX procedure_unique RENAME TO procedure_unique00000');
        $this->processSql('ALTER INDEX idx_d84e5574b2a22366 RENAME TO IDX_D58D7E02BCFB922F');
        $this->processSql('ALTER INDEX idx_39fd2caa7909e1ed RENAME TO IDX_F64015367909E1ED');
        $this->processSql('DROP INDEX pk__transres__0b11e36ce7ca5d70');
        $this->processSql('ALTER TABLE transres_project_coinvestigator ADD PRIMARY KEY (coinvestigator_id, project_id)');
        $this->processSql('DROP INDEX pk__transres__19312a38df2593d2');
        $this->processSql('ALTER TABLE transres_project_irbApprovalLetter ADD PRIMARY KEY (irbapprovalletters_id, project_id)');
        $this->processSql('ALTER INDEX idx_3bd57bcc72698c7a RENAME TO IDX_F11D06226072379A');
        $this->processSql('DROP INDEX pk__transres__cc5d79b7365852fa');
        $this->processSql('ALTER TABLE transres_project_contact ADD PRIMARY KEY (contact_id, project_id)');
        $this->processSql('ALTER TABLE transres_project DROP expirationNotifyCounter');
        $this->processSql('ALTER TABLE transres_project DROP expiredNotifyCounter');
        $this->processSql('DROP INDEX pk__transres__651ff0956882d397');
        $this->processSql('ALTER TABLE transres_project_document ADD PRIMARY KEY (document_id, project_id)');
        $this->processSql('DROP INDEX pk__transres__2ceb93c3334224d5');
        $this->processSql('ALTER TABLE transres_invoice_document ADD PRIMARY KEY (document_id, invoice_id)');
        $this->processSql('DROP INDEX pk__transres__63a44e26c1580c92');
        $this->processSql('ALTER TABLE transres_project_pathologist ADD PRIMARY KEY (pathologist_id, project_id)');
        $this->processSql('DROP INDEX pk__transres__03e9258de8f3c1ee');
        $this->processSql('ALTER TABLE transres_project_humanTissueForm ADD PRIMARY KEY (humantissueform_id, project_id)');
        $this->processSql('ALTER INDEX idx_fe149f5aa27d545f RENAME TO IDX_117B33B4DEBE2636');
        $this->processSql('DROP INDEX pk__transres__2b00b9da0588eb04');
        $this->processSql('ALTER TABLE transres_request_oldPackingSlipPdf ADD PRIMARY KEY (oldpackingslippdf_id, request_id)');
        $this->processSql('ALTER INDEX idx_10e9af556bd350a1 RENAME TO IDX_2A7DA8F1D8164BD');
        $this->processSql('DROP INDEX pk__user_acc__f69d38a6ebc64f4d');
        $this->processSql('ALTER TABLE user_accountrequest_institution ADD PRIMARY KEY (institution_id, request_id)');
        $this->processSql('DROP INDEX pk__transres__c1b5d78589dcf2e9');
        $this->processSql('ALTER TABLE transres_request_document ADD PRIMARY KEY (document_id, request_id)');
        $this->processSql('DROP INDEX pk__transres__88f7f6574d8245ca');
        $this->processSql('ALTER TABLE transres_transResSiteParameters_transresLogo ADD PRIMARY KEY (transreslogo_id, transressiteparameter_id)');
        $this->processSql('ALTER INDEX idx_f898b8b948fdb66a RENAME TO IDX_77E35B187408FF6');
        $this->processSql('DROP INDEX pk__transres__29a021d4af61d6c6');
        $this->processSql('ALTER TABLE transres_transResSiteParameters_transresPackingSlipLogo ADD PRIMARY KEY (transrespackingsliplogo_id, transressiteparameter_id)');
        $this->processSql('ALTER INDEX idx_7ecb11f76e565d6d RENAME TO IDX_E1D041EC7A315316');
        $this->processSql('DROP INDEX pk__transres__87e3c6fcd2c2530c');
        $this->processSql('ALTER TABLE transres_request_packingSlipPdf ADD PRIMARY KEY (packingslippdf_id, request_id)');
        $this->processSql('ALTER INDEX idx_5e2751fb7f71d5a RENAME TO IDX_F2120A63FF061BBC');
        $this->processSql('DROP INDEX pk__transres__b8a8b8ef0400bab2');
        $this->processSql('ALTER TABLE transres_request_businessPurpose ADD PRIMARY KEY (businesspurpose_id, request_id)');
        $this->processSql('ALTER INDEX idx_8a5557046467b583 RENAME TO IDX_45E86E982B3AB653');
        $this->processSql('DROP INDEX pk__transres__565ed6eabd7c780d');
        $this->processSql('ALTER TABLE transres_request_principalinvestigator ADD PRIMARY KEY (principalinvestigator_id, request_id)');
        $this->processSql('ALTER INDEX idx_b7c3de2166d1f9c RENAME TO IDX_C4C1047E166D1F9C');
        $this->processSql('DROP INDEX pk__transres__b1b06a8356f708f9');
        $this->processSql('ALTER TABLE transres_request_antibody ADD PRIMARY KEY (antibody_id, request_id)');
        $this->processSql('ALTER INDEX idx_c3a8494b166d1f9c RENAME TO IDX_22B6422D166D1F9C');
        $this->processSql('ALTER INDEX idx_bf2a5b6f834995b1 RENAME TO IDX_465C3939834995B1');
        $this->processSql('DROP INDEX pk__user_col__c48da81b7b714316');
        $this->processSql('ALTER TABLE user_collaborationInstitution_collaboration ADD PRIMARY KEY (collaboration_id, collaborationinstitution_id)');
        $this->processSql('ALTER INDEX idx_832a3fc4ef1544ce RENAME TO IDX_33047118EF1544CE');
        $this->processSql('DROP INDEX pk__user_doc__2253520cf77fb24d');
        $this->processSql('ALTER TABLE user_documentcontainer_document ADD PRIMARY KEY (document_id, documentcontainer_id)');
        $this->processSql('ALTER INDEX idx_f172e05ab974d123 RENAME TO IDX_FB465C67C5B7A34A');
        $this->processSql('DROP INDEX pk__user_fel__07eb659e4a39ae1b');
        $this->processSql('ALTER TABLE user_fellowshipSubspecialty_director ADD PRIMARY KEY (director_id, fellowshipsubspecialty_id)');
        $this->processSql('ALTER INDEX idx_fffa6a60899fb366 RENAME TO IDX_68324FB8899FB366');
        $this->processSql('ALTER INDEX idx_166ef8c6802d4908 RENAME TO IDX_E8B806BC8E87796');
        $this->processSql('DROP INDEX pk__user_fel__28db425da1707d96');
        $this->processSql('ALTER TABLE user_fellowshipSubspecialty_coordinator ADD PRIMARY KEY (coordinator_id, fellowshipsubspecialty_id)');
        $this->processSql('ALTER INDEX idx_708d2bcce7877946 RENAME TO IDX_8E5BD5B6E7877946');
        $this->processSql('DROP INDEX pk__user_loc__35bcd52c208294ed');
        $this->processSql('ALTER TABLE user_location_assistant ADD PRIMARY KEY (assistant_id, location_id)');
        $this->processSql('ALTER INDEX idx_d1adc86dc1a3e458 RENAME TO IDX_759D120B38D5860E');
        $this->processSql('DROP INDEX pk__user_log__97ffb833a182e135');
        $this->processSql('ALTER TABLE user_logger_institutions ADD PRIMARY KEY (institution_id, logger_id)');
        $this->processSql('DROP INDEX pk__user_med__689a97da1484ea19');
        $this->processSql('ALTER TABLE user_medicaltitle_medicalspeciality ADD PRIMARY KEY (medicalspeciality_id, medicaltitle_id)');
        $this->processSql('DROP INDEX pk__user_org__2798b6cee2fa03dc');
        $this->processSql('ALTER TABLE user_organizationalGroupDefault_permittedInstitutionalPHIScope ADD PRIMARY KEY (institution_id, permittedinstitutionalphiscope_id)');
        $this->processSql('ALTER INDEX idx_78626fe310405986 RENAME TO IDX_205B297510405986');
        $this->processSql('ALTER INDEX idx_52955ba8dc6f0d8 RENAME TO IDX_3681A952CA5ECD96');
        $this->processSql('DROP INDEX pk__user_org__5fe4fc345e1141a6');
        $this->processSql('ALTER TABLE user_organizationalGroupDefault_locationtype ADD PRIMARY KEY (locationtypelist_id, organizationalgroupdefault_id)');
        $this->processSql('ALTER INDEX idx_a5107c4d3d296b97 RENAME TO IDX_FC2629A3D296B97');
        $this->processSql('DROP INDEX pk__user_per__0b7d9b53a84a7d17');
        $this->processSql('ALTER TABLE user_permission_institution ADD PRIMARY KEY (institution_id, permission_id)');
        $this->processSql('ALTER INDEX platformlist_unique RENAME TO platformlist_unique00000');
        $this->processSql('DROP INDEX pk__user_pre__4536d4e84049c021');
        $this->processSql('ALTER TABLE user_preferences_institutions ADD PRIMARY KEY (institution_id, preferences_id)');
        $this->processSql('ALTER INDEX idx_8cd97cb3c5961d3d RENAME TO IDX_D4BB041EB9556F54');
        $this->processSql('DROP INDEX pk__user_rol__5f8f6d8eb114b79e');
        $this->processSql('ALTER TABLE user_roles_attributes ADD PRIMARY KEY (roleattributelist_id, roles_id)');
        $this->processSql('DROP INDEX pk__user_sit__6b49b54038d4a002');
        $this->processSql('ALTER TABLE user_site_document ADD PRIMARY KEY (document_id, site_id)');
        $this->processSql('DROP INDEX pk__user_sit__db00a856bd763053');
        $this->processSql('ALTER TABLE user_siteparameter_platformLogo ADD PRIMARY KEY (platformlogo_id, siteparameter_id)');
        $this->processSql('ALTER INDEX idx_29c001c825e6d108 RENAME TO IDX_89F2AEF6EA5BE894');
        $this->processSql('DROP INDEX pk__user_sit__f5766ba66a7a5b55');
        $this->processSql('ALTER TABLE user_siteparameter_emailcriticalerrorexceptionuser ADD PRIMARY KEY (exceptionuser_id, siteparameter_id)');
        $this->processSql('DROP INDEX pk__user_sit__654f4d96ed048b52');
        $this->processSql('ALTER TABLE user_sites_lowestRoles ADD PRIMARY KEY (role_id, site_id)');
        $this->processSql('ALTER INDEX idx_a56efffad60322ac RENAME TO IDX_64AFD0FED60322AC');
        $this->processSql('DROP INDEX pk__user_roo__ac9af705947b0756');
        $this->processSql('ALTER TABLE user_rooms_floors ADD PRIMARY KEY (floorlist_id, roomlist_id)');
        $this->processSql('DROP INDEX pk__user_tra__5c60edaced31c275');
        $this->processSql('ALTER TABLE user_trainings_minors ADD PRIMARY KEY (minortraininglist_id, training_id)');
        $this->processSql('DROP INDEX pk__user_sui__49566268fd665a3c');
        $this->processSql('ALTER TABLE user_suites_buildings ADD PRIMARY KEY (buildinglist_id, suitelist_id)');
        $this->processSql('DROP INDEX pk__user_tra__ae3b80e435df1f7b');
        $this->processSql('ALTER TABLE user_trainings_majors ADD PRIMARY KEY (majortraininglist_id, training_id)');
        $this->processSql('DROP INDEX pk__user_tra__813e488d7f13a8d3');
        $this->processSql('ALTER TABLE user_trainings_honors ADD PRIMARY KEY (honortraininglist_id, training_id)');
        $this->processSql('DROP INDEX pk__user_use__946aa66016ca2554');
        $this->processSql('ALTER TABLE user_userpreferences_languages ADD PRIMARY KEY (languagelist_id, userpreferences_id)');
        $this->processSql('DROP INDEX pk__user_use__31f366e3bcc33919');
        $this->processSql('ALTER TABLE user_users_publications ADD PRIMARY KEY (publication_id, user_id)');
        $this->processSql('DROP INDEX pk__user_use__bd2ee6a18eb4edb3');
        $this->processSql('ALTER TABLE user_users_books ADD PRIMARY KEY (book_id, user_id)');
        $this->processSql('DROP INDEX pk__user_use__ce4d3a9f97fedef6');
        $this->processSql('ALTER TABLE user_users_researchlabs ADD PRIMARY KEY (researchlab_id, user_id)');
        $this->processSql('DROP INDEX pk__user_use__3327d6c1bcbc2e61');
        $this->processSql('ALTER TABLE user_users_grants ADD PRIMARY KEY (grant_id, user_id)');
        $this->processSql('DROP INDEX pk__vacreq_s__17bd0de8d9078ca0');
        $this->processSql('ALTER TABLE vacreq_settings_user ADD PRIMARY KEY (emailuser_id, settings_id)');
        $this->processSql('ALTER INDEX siteparameters_unique RENAME TO siteparameters_unique00000');
        $this->processSql('DROP INDEX pk__transres__f2f4f1fa47f2f505');
        $this->processSql('ALTER TABLE transres_project_principalinvestigator ADD PRIMARY KEY (principalinvestigator_id, project_id)');
        $this->processSql('DROP INDEX pk__user_sui__2d7f55ed9f48fa8b');
        $this->processSql('ALTER TABLE user_suites_floors ADD PRIMARY KEY (floorlist_id, suitelist_id)');
        $this->processSql('DROP INDEX pk__fellapp___57e065a2d50023cc');
        $this->processSql('ALTER TABLE fellapp_reference_document ADD PRIMARY KEY (document_id, reference_id)');
        $this->processSql('DROP INDEX pk__user_org__9da457f1e95d5a3d');
        $this->processSql('ALTER TABLE user_organizationalGroupDefault_language ADD PRIMARY KEY (languagelist_id, organizationalgroupdefault_id)');
        $this->processSql('ALTER INDEX idx_243b3090d88ec86e RENAME TO IDX_A4CC6E35D88EC86E');
        $this->processSql('DROP INDEX pk__fellapp___1e656710b5f511b5');
        $this->processSql('ALTER TABLE fellapp_fellApp_coverLetter ADD PRIMARY KEY (coverletter_id, fellapp_id)');
        $this->processSql('ALTER INDEX idx_263579c13111febe RENAME TO IDX_A95C4269B3E07C1D');
        $this->processSql('DROP INDEX pk__transres__c1b5d785d13af751');
        $this->processSql('ALTER TABLE transres_antibody_document ADD PRIMARY KEY (document_id, request_id)');
        $this->processSql('ALTER INDEX idx_5afc0f4bcd46f646 RENAME TO IDX_15B668721ACA1422');
        $this->processSql('DROP INDEX pk__user_use__56c84e47dcdd4347');
        $this->processSql('ALTER TABLE user_userPositions_positionTypes ADD PRIMARY KEY (positiontypelist_id, userposition_id)');
        $this->processSql('ALTER INDEX idx_61be9d18aae046c8 RENAME TO IDX_A29ABE84AAE046C8');
        $this->processSql('DROP INDEX pk__fellapp___256da0502a31d670');
        $this->processSql('ALTER TABLE fellapp_googleformconfig_fellowshipsubspecialty ADD PRIMARY KEY (fellowshipsubspecialty_id, googleformconfig_id)');
        $this->processSql('DROP INDEX pk__user_roo__c8b3c08035703210');
        $this->processSql('ALTER TABLE user_rooms_buildings ADD PRIMARY KEY (buildinglist_id, roomlist_id)');
        $this->processSql('ALTER INDEX idx_bc32a660537a1329 RENAME TO IDX_3EC324C3537A1329');
    }
}
