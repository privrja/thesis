<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200223193145 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE b2s (id INT AUTO_INCREMENT NOT NULL, sequence_id INT NOT NULL, block_id INT NOT NULL, sort INT NOT NULL, INDEX IDX_906EB2AB98FB19AE (sequence_id), INDEX IDX_906EB2ABE9ED820C (block_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE block (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(255) NOT NULL, residue VARCHAR(255) NOT NULL, mass DOUBLE PRECISION DEFAULT NULL, losses VARCHAR(255) DEFAULT NULL, smiles VARCHAR(255) DEFAULT NULL, source SMALLINT DEFAULT NULL, indetifier VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, INDEX IDX_831B9722BC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE container (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, type SMALLINT NOT NULL, INDEX IDX_C7A2EC1BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modification (id INT AUTO_INCREMENT NOT NULL, container_id INT NOT NULL, name VARCHAR(255) NOT NULL, formula VARCHAR(255) NOT NULL, mass DOUBLE PRECISION DEFAULT NULL, n_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, c_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_EF6425D2BC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sequence (id INT AUTO_INCREMENT NOT NULL, n_modification_id INT DEFAULT NULL, c_modification_id INT DEFAULT NULL, b_modification_id INT DEFAULT NULL, container_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, formula VARCHAR(255) NOT NULL, mass DOUBLE PRECISION DEFAULT NULL, sequence VARCHAR(255) DEFAULT NULL, smiles VARCHAR(500) DEFAULT NULL, source SMALLINT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, decays VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, INDEX IDX_5286D72B202EA3BB (n_modification_id), INDEX IDX_5286D72B329000A9 (c_modification_id), INDEX IDX_5286D72BB536CBEA (b_modification_id), INDEX IDX_5286D72BBC21F742 (container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nick VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649290B2F37 (nick), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2AB98FB19AE FOREIGN KEY (sequence_id) REFERENCES sequence (id)');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2ABE9ED820C FOREIGN KEY (block_id) REFERENCES block (id)');
        $this->addSql('ALTER TABLE block ADD CONSTRAINT FK_831B9722BC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE container ADD CONSTRAINT FK_C7A2EC1BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE modification ADD CONSTRAINT FK_EF6425D2BC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72B202EA3BB FOREIGN KEY (n_modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72B329000A9 FOREIGN KEY (c_modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72BB536CBEA FOREIGN KEY (b_modification_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72BBC21F742 FOREIGN KEY (container_id) REFERENCES container (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2ABE9ED820C');
        $this->addSql('ALTER TABLE block DROP FOREIGN KEY FK_831B9722BC21F742');
        $this->addSql('ALTER TABLE modification DROP FOREIGN KEY FK_EF6425D2BC21F742');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72BBC21F742');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72B202EA3BB');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72B329000A9');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72BB536CBEA');
        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2AB98FB19AE');
        $this->addSql('ALTER TABLE container DROP FOREIGN KEY FK_C7A2EC1BA76ED395');
        $this->addSql('DROP TABLE b2s');
        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE container');
        $this->addSql('DROP TABLE modification');
        $this->addSql('DROP TABLE sequence');
        $this->addSql('DROP TABLE user');
    }
}
