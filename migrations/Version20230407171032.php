<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230407171032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, marketplace_id INT NOT NULL, name VARCHAR(50) NOT NULL, nickname VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, adress VARCHAR(255) DEFAULT NULL, INDEX IDX_81398E097078ABE4 (marketplace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, brand VARCHAR(50) NOT NULL, price DOUBLE PRECISION NOT NULL, os VARCHAR(50) NOT NULL, cpu VARCHAR(255) NOT NULL, screen VARCHAR(255) DEFAULT NULL, is_available TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_444F97DDEA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649EA750E8 (label), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E097078ABE4 FOREIGN KEY (marketplace_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E097078ABE4');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE `user`');
    }
}
