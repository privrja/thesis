<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210317084911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE b2f (id INT AUTO_INCREMENT NOT NULL, block_id INT NOT NULL, family_id INT NOT NULL, INDEX IDX_FDB35640E9ED820C (block_id), INDEX IDX_FDB35640C35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE b2s (id INT AUTO_INCREMENT NOT NULL, sequence_id INT NOT NULL, block_id INT NOT NULL, next_block_id INT DEFAULT NULL, branch_reference_id INT DEFAULT NULL, is_branch TINYINT(1) DEFAULT \'0\' NOT NULL, block_original_id INT NOT NULL, sort INT NOT NULL, INDEX IDX_906EB2AB98FB19AE (sequence_id), INDEX IDX_906EB2ABE9ED820C (block_id), INDEX IDX_906EB2ABD1F7CEE2 (next_block_id), INDEX IDX_906EB2AB4BA1090F (branch_reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE block (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, block_name VARCHAR(255) NOT NULL, acronym VARCHAR(30) NOT NULL, residue VARCHAR(255) NOT NULL, block_mass DOUBLE PRECISION DEFAULT NULL, losses VARCHAR(255) DEFAULT NULL, block_smiles VARCHAR(255) DEFAULT NULL, usmiles VARCHAR(255) DEFAULT NULL, source SMALLINT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, is_polyketide TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_831B9722BC21F742 (container_id), UNIQUE INDEX UX_BLOCK_ACRONYM (acronym, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE block_family (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, block_family_name VARCHAR(255) NOT NULL, INDEX IDX_EC4877D0BC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `condition` (id INT AUTO_INCREMENT NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE container (id INT AUTO_INCREMENT NOT NULL, container_name VARCHAR(255) NOT NULL, visibility VARCHAR(10) NOT NULL, INDEX IDX_CONTAINER_ID (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modification (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, modification_name VARCHAR(255) NOT NULL, modification_formula VARCHAR(255) NOT NULL, modification_mass DOUBLE PRECISION DEFAULT NULL, n_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, c_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_EF6425D2BC21F742 (container_id), UNIQUE INDEX UX_MODIFICATION_NAME (modification_name, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organism (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, organism VARCHAR(255) NOT NULL, INDEX IDX_D538A2CBC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s2f (id INT AUTO_INCREMENT NOT NULL, sequence_id INT NOT NULL, family_id INT NOT NULL, INDEX IDX_E0579F0798FB19AE (sequence_id), INDEX IDX_E0579F07C35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s2o (id INT AUTO_INCREMENT NOT NULL, organism_id INT NOT NULL, sequence_id INT NOT NULL, INDEX IDX_998B27A364180A36 (organism_id), INDEX IDX_998B27A398FB19AE (sequence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sequence (id INT AUTO_INCREMENT NOT NULL, n_modification_id INT DEFAULT NULL, c_modification_id INT DEFAULT NULL, b_modification_id INT DEFAULT NULL, container_id INT NOT NULL, sequence_type VARCHAR(255) DEFAULT \'other\' NOT NULL, sequence_name VARCHAR(255) NOT NULL, sequence VARCHAR(500) NOT NULL, sequence_original VARCHAR(500) NOT NULL, sequence_formula VARCHAR(255) NOT NULL, sequence_mass DOUBLE PRECISION DEFAULT NULL, sequence_smiles VARCHAR(4000) DEFAULT NULL, usmiles VARCHAR(4000) DEFAULT NULL, source SMALLINT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, decays VARCHAR(255) DEFAULT NULL, unique_block_count INT NOT NULL, block_count INT NOT NULL, INDEX IDX_5286D72B202EA3BB (n_modification_id), INDEX IDX_5286D72B329000A9 (c_modification_id), INDEX IDX_5286D72BB536CBEA (b_modification_id), INDEX IDX_5286D72BBC21F742 (container_id), UNIQUE INDEX UX_SEQUENCE_NAME (sequence_name, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sequence_family (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, sequence_family_name VARCHAR(255) NOT NULL, INDEX IDX_C1F60532BC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setup (id INT AUTO_INCREMENT NOT NULL, similarity VARCHAR(10) DEFAULT \'name\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE u2c (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, container_id INT NOT NULL, mode VARCHAR(10) NOT NULL, INDEX IDX_94B0173AA76ED395 (user_id), INDEX IDX_94B0173ABC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nick VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) DEFAULT NULL, conditions TINYINT(1) DEFAULT \'0\' NOT NULL, chem_spider_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), UNIQUE INDEX UX_USER_NICK (nick), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE b2f ADD CONSTRAINT FK_FDB35640E9ED820C FOREIGN KEY (block_id) REFERENCES block (id)');
        $this->addSql('ALTER TABLE b2f ADD CONSTRAINT FK_FDB35640C35E566A FOREIGN KEY (family_id) REFERENCES block_family (id)');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2AB98FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (id)');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2ABE9ED820C FOREIGN KEY (block_id) REFERENCES block (id)');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2ABD1F7CEE2 FOREIGN KEY (next_block_id) REFERENCES block (id)');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2AB4BA1090F FOREIGN KEY (branch_reference_id) REFERENCES block (id)');
        $this->addSql('ALTER TABLE block ADD CONSTRAINT FK_831B9722BC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE block_family ADD CONSTRAINT FK_EC4877D0BC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE modification ADD CONSTRAINT FK_EF6425D2BC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE organism ADD CONSTRAINT FK_D538A2CBC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE s2f ADD CONSTRAINT FK_E0579F0798FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (id)');
        $this->addSql('ALTER TABLE s2f ADD CONSTRAINT FK_E0579F07C35E566A FOREIGN KEY (family_id) REFERENCES sequence_family (id)');
        $this->addSql('ALTER TABLE s2o ADD CONSTRAINT FK_998B27A364180A36 FOREIGN KEY (organism_id) REFERENCES organism (id)');
        $this->addSql('ALTER TABLE s2o ADD CONSTRAINT FK_998B27A398FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72B202EA3BB FOREIGN KEY (n_modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72B329000A9 FOREIGN KEY (c_modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72BB536CBEA FOREIGN KEY (b_modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72BBC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE sequence_family ADD CONSTRAINT FK_C1F60532BC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE u2c ADD CONSTRAINT FK_94B0173AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE u2c ADD CONSTRAINT FK_94B0173ABC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE b2f DROP FOREIGN KEY FK_FDB35640E9ED820C');
        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2ABE9ED820C');
        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2ABD1F7CEE2');
        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2AB4BA1090F');
        $this->addSql('ALTER TABLE b2f DROP FOREIGN KEY FK_FDB35640C35E566A');
        $this->addSql('ALTER TABLE block DROP FOREIGN KEY FK_831B9722BC21F742');
        $this->addSql('ALTER TABLE block_family DROP FOREIGN KEY FK_EC4877D0BC21F742');
        $this->addSql('ALTER TABLE modification DROP FOREIGN KEY FK_EF6425D2BC21F742');
        $this->addSql('ALTER TABLE organism DROP FOREIGN KEY FK_D538A2CBC21F742');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72BBC21F742');
        $this->addSql('ALTER TABLE sequence_family DROP FOREIGN KEY FK_C1F60532BC21F742');
        $this->addSql('ALTER TABLE u2c DROP FOREIGN KEY FK_94B0173ABC21F742');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72B202EA3BB');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72B329000A9');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72BB536CBEA');
        $this->addSql('ALTER TABLE s2o DROP FOREIGN KEY FK_998B27A364180A36');
        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2AB98FB19AE');
        $this->addSql('ALTER TABLE s2f DROP FOREIGN KEY FK_E0579F0798FB19AE');
        $this->addSql('ALTER TABLE s2o DROP FOREIGN KEY FK_998B27A398FB19AE');
        $this->addSql('ALTER TABLE s2f DROP FOREIGN KEY FK_E0579F07C35E566A');
        $this->addSql('ALTER TABLE u2c DROP FOREIGN KEY FK_94B0173AA76ED395');
        $this->addSql('DROP TABLE b2f');
        $this->addSql('DROP TABLE b2s');
        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE block_family');
        $this->addSql('DROP TABLE `condition`');
        $this->addSql('DROP TABLE container');
        $this->addSql('DROP TABLE modification');
        $this->addSql('DROP TABLE organism');
        $this->addSql('DROP TABLE s2f');
        $this->addSql('DROP TABLE s2o');
        $this->addSql('DROP TABLE sequence');
        $this->addSql('DROP TABLE sequence_family');
        $this->addSql('DROP TABLE setup');
        $this->addSql('DROP TABLE u2c');
        $this->addSql('DROP TABLE user');
    }
}
