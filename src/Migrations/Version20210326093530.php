<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210326093530 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `msb_Container` (id INT AUTO_INCREMENT NOT NULL, container_name VARCHAR(255) NOT NULL, visibility VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_b2f` (id INT AUTO_INCREMENT NOT NULL, block_id INT NOT NULL, family_id INT NOT NULL, INDEX IDX_88D539BFE9ED820C (block_id), INDEX IDX_88D539BFC35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_b2s` (id INT AUTO_INCREMENT NOT NULL, sequence_id INT NOT NULL, block_id INT NOT NULL, next_block_id INT DEFAULT NULL, branch_reference_id INT DEFAULT NULL, is_branch TINYINT(1) DEFAULT \'0\' NOT NULL, block_original_id INT NOT NULL, sort INT NOT NULL, INDEX IDX_E508DD5498FB19AE (sequence_id), INDEX IDX_E508DD54E9ED820C (block_id), INDEX IDX_E508DD54D1F7CEE2 (next_block_id), INDEX IDX_E508DD544BA1090F (branch_reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_block` (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, block_name VARCHAR(255) NOT NULL, acronym VARCHAR(30) NOT NULL, residue VARCHAR(255) NOT NULL, block_mass DOUBLE PRECISION DEFAULT NULL, losses VARCHAR(255) DEFAULT NULL, block_smiles VARCHAR(255) DEFAULT NULL, usmiles VARCHAR(255) DEFAULT NULL, source SMALLINT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, is_polyketide TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_CD3263FFBC21F742 (container_id), UNIQUE INDEX UX_BLOCK_ACRONYM (acronym, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_block_family` (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, block_family_name VARCHAR(255) NOT NULL, INDEX IDX_1F452C21BC21F742 (container_id), UNIQUE INDEX UX_BLOCK_FAMILY_NAME (block_family_name, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_condition` (id INT AUTO_INCREMENT NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_modification` (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, modification_name VARCHAR(255) NOT NULL, modification_formula VARCHAR(255) NOT NULL, modification_mass DOUBLE PRECISION DEFAULT NULL, n_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, c_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_1C697E23BC21F742 (container_id), UNIQUE INDEX UX_MODIFICATION_NAME (modification_name, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_organism` (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, organism VARCHAR(255) NOT NULL, INDEX IDX_BA6090D4BC21F742 (container_id), UNIQUE INDEX UX_ORGANISM_NAME (organism, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_s2f` (id INT AUTO_INCREMENT NOT NULL, sequence_id INT NOT NULL, family_id INT NOT NULL, INDEX IDX_9531F0F898FB19AE (sequence_id), INDEX IDX_9531F0F8C35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_s2o` (id INT AUTO_INCREMENT NOT NULL, organism_id INT NOT NULL, sequence_id INT NOT NULL, INDEX IDX_ECED485C64180A36 (organism_id), INDEX IDX_ECED485C98FB19AE (sequence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_sequence` (id INT AUTO_INCREMENT NOT NULL, n_modification_id INT DEFAULT NULL, c_modification_id INT DEFAULT NULL, b_modification_id INT DEFAULT NULL, container_id INT NOT NULL, sequence_type VARCHAR(255) DEFAULT \'other\' NOT NULL, sequence_name VARCHAR(255) NOT NULL, sequence VARCHAR(500) NOT NULL, sequence_original VARCHAR(500) NOT NULL, sequence_formula VARCHAR(255) NOT NULL, sequence_mass DOUBLE PRECISION DEFAULT NULL, sequence_smiles VARCHAR(4000) DEFAULT NULL, usmiles VARCHAR(4000) DEFAULT NULL, source SMALLINT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, decays VARCHAR(255) DEFAULT NULL, unique_block_count INT NOT NULL, block_count INT NOT NULL, INDEX IDX_E5B5CDD3202EA3BB (n_modification_id), INDEX IDX_E5B5CDD3329000A9 (c_modification_id), INDEX IDX_E5B5CDD3B536CBEA (b_modification_id), INDEX IDX_E5B5CDD3BC21F742 (container_id), UNIQUE INDEX UX_SEQUENCE_NAME (sequence_name, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_sequence_family` (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, sequence_family_name VARCHAR(255) NOT NULL, INDEX IDX_520EE404BC21F742 (container_id), UNIQUE INDEX UX_SEQUENCE_FAMILY_NAME (sequence_family_name, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_setup` (id INT AUTO_INCREMENT NOT NULL, similarity VARCHAR(10) DEFAULT \'name\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_u2c` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, container_id INT NOT NULL, mode VARCHAR(10) NOT NULL, INDEX IDX_E1D678C5A76ED395 (user_id), INDEX IDX_E1D678C5BC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `msb_user` (id INT AUTO_INCREMENT NOT NULL, nick VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) DEFAULT NULL, conditions TINYINT(1) DEFAULT \'0\' NOT NULL, chem_spider_token VARCHAR(255) DEFAULT NULL, last_activity DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A0E45FAB290B2F37 (nick), UNIQUE INDEX UNIQ_A0E45FAB7BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `msb_b2f` ADD CONSTRAINT FK_88D539BFE9ED820C FOREIGN KEY (block_id) REFERENCES `msb_block` (id)');
        $this->addSql('ALTER TABLE `msb_b2f` ADD CONSTRAINT FK_88D539BFC35E566A FOREIGN KEY (family_id) REFERENCES `msb_block_family` (id)');
        $this->addSql('ALTER TABLE `msb_b2s` ADD CONSTRAINT FK_E508DD5498FB19AE FOREIGN KEY (sequence_id) REFERENCES `msb_sequence` (id)');
        $this->addSql('ALTER TABLE `msb_b2s` ADD CONSTRAINT FK_E508DD54E9ED820C FOREIGN KEY (block_id) REFERENCES `msb_block` (id)');
        $this->addSql('ALTER TABLE `msb_b2s` ADD CONSTRAINT FK_E508DD54D1F7CEE2 FOREIGN KEY (next_block_id) REFERENCES `msb_block` (id)');
        $this->addSql('ALTER TABLE `msb_b2s` ADD CONSTRAINT FK_E508DD544BA1090F FOREIGN KEY (branch_reference_id) REFERENCES `msb_block` (id)');
        $this->addSql('ALTER TABLE `msb_block` ADD CONSTRAINT FK_CD3263FFBC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
        $this->addSql('ALTER TABLE `msb_block_family` ADD CONSTRAINT FK_1F452C21BC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
        $this->addSql('ALTER TABLE `msb_modification` ADD CONSTRAINT FK_1C697E23BC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
        $this->addSql('ALTER TABLE `msb_organism` ADD CONSTRAINT FK_BA6090D4BC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
        $this->addSql('ALTER TABLE `msb_s2f` ADD CONSTRAINT FK_9531F0F898FB19AE FOREIGN KEY (sequence_id) REFERENCES `msb_sequence` (id)');
        $this->addSql('ALTER TABLE `msb_s2f` ADD CONSTRAINT FK_9531F0F8C35E566A FOREIGN KEY (family_id) REFERENCES `msb_sequence_family` (id)');
        $this->addSql('ALTER TABLE `msb_s2o` ADD CONSTRAINT FK_ECED485C64180A36 FOREIGN KEY (organism_id) REFERENCES `msb_organism` (id)');
        $this->addSql('ALTER TABLE `msb_s2o` ADD CONSTRAINT FK_ECED485C98FB19AE FOREIGN KEY (sequence_id) REFERENCES `msb_sequence` (id)');
        $this->addSql('ALTER TABLE `msb_sequence` ADD CONSTRAINT FK_E5B5CDD3202EA3BB FOREIGN KEY (n_modification_id) REFERENCES `msb_modification` (id)');
        $this->addSql('ALTER TABLE `msb_sequence` ADD CONSTRAINT FK_E5B5CDD3329000A9 FOREIGN KEY (c_modification_id) REFERENCES `msb_modification` (id)');
        $this->addSql('ALTER TABLE `msb_sequence` ADD CONSTRAINT FK_E5B5CDD3B536CBEA FOREIGN KEY (b_modification_id) REFERENCES `msb_modification` (id)');
        $this->addSql('ALTER TABLE `msb_sequence` ADD CONSTRAINT FK_E5B5CDD3BC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
        $this->addSql('ALTER TABLE `msb_sequence_family` ADD CONSTRAINT FK_520EE404BC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
        $this->addSql('ALTER TABLE `msb_u2c` ADD CONSTRAINT FK_E1D678C5A76ED395 FOREIGN KEY (user_id) REFERENCES `msb_user` (id)');
        $this->addSql('ALTER TABLE `msb_u2c` ADD CONSTRAINT FK_E1D678C5BC21F742 FOREIGN KEY (container_id) REFERENCES `msb_Container` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `msb_block` DROP FOREIGN KEY FK_CD3263FFBC21F742');
        $this->addSql('ALTER TABLE `msb_block_family` DROP FOREIGN KEY FK_1F452C21BC21F742');
        $this->addSql('ALTER TABLE `msb_modification` DROP FOREIGN KEY FK_1C697E23BC21F742');
        $this->addSql('ALTER TABLE `msb_organism` DROP FOREIGN KEY FK_BA6090D4BC21F742');
        $this->addSql('ALTER TABLE `msb_sequence` DROP FOREIGN KEY FK_E5B5CDD3BC21F742');
        $this->addSql('ALTER TABLE `msb_sequence_family` DROP FOREIGN KEY FK_520EE404BC21F742');
        $this->addSql('ALTER TABLE `msb_u2c` DROP FOREIGN KEY FK_E1D678C5BC21F742');
        $this->addSql('ALTER TABLE `msb_b2f` DROP FOREIGN KEY FK_88D539BFE9ED820C');
        $this->addSql('ALTER TABLE `msb_b2s` DROP FOREIGN KEY FK_E508DD54E9ED820C');
        $this->addSql('ALTER TABLE `msb_b2s` DROP FOREIGN KEY FK_E508DD54D1F7CEE2');
        $this->addSql('ALTER TABLE `msb_b2s` DROP FOREIGN KEY FK_E508DD544BA1090F');
        $this->addSql('ALTER TABLE `msb_b2f` DROP FOREIGN KEY FK_88D539BFC35E566A');
        $this->addSql('ALTER TABLE `msb_sequence` DROP FOREIGN KEY FK_E5B5CDD3202EA3BB');
        $this->addSql('ALTER TABLE `msb_sequence` DROP FOREIGN KEY FK_E5B5CDD3329000A9');
        $this->addSql('ALTER TABLE `msb_sequence` DROP FOREIGN KEY FK_E5B5CDD3B536CBEA');
        $this->addSql('ALTER TABLE `msb_s2o` DROP FOREIGN KEY FK_ECED485C64180A36');
        $this->addSql('ALTER TABLE `msb_b2s` DROP FOREIGN KEY FK_E508DD5498FB19AE');
        $this->addSql('ALTER TABLE `msb_s2f` DROP FOREIGN KEY FK_9531F0F898FB19AE');
        $this->addSql('ALTER TABLE `msb_s2o` DROP FOREIGN KEY FK_ECED485C98FB19AE');
        $this->addSql('ALTER TABLE `msb_s2f` DROP FOREIGN KEY FK_9531F0F8C35E566A');
        $this->addSql('ALTER TABLE `msb_u2c` DROP FOREIGN KEY FK_E1D678C5A76ED395');
        $this->addSql('DROP TABLE `msb_Container`');
        $this->addSql('DROP TABLE `msb_b2f`');
        $this->addSql('DROP TABLE `msb_b2s`');
        $this->addSql('DROP TABLE `msb_block`');
        $this->addSql('DROP TABLE `msb_block_family`');
        $this->addSql('DROP TABLE `msb_condition`');
        $this->addSql('DROP TABLE `msb_modification`');
        $this->addSql('DROP TABLE `msb_organism`');
        $this->addSql('DROP TABLE `msb_s2f`');
        $this->addSql('DROP TABLE `msb_s2o`');
        $this->addSql('DROP TABLE `msb_sequence`');
        $this->addSql('DROP TABLE `msb_sequence_family`');
        $this->addSql('DROP TABLE `msb_setup`');
        $this->addSql('DROP TABLE `msb_u2c`');
        $this->addSql('DROP TABLE `msb_user`');
    }
}
