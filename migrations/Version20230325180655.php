<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325180655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD sold INT NOT NULL, ADD price DOUBLE PRECISION NOT NULL, ADD version VARCHAR(50) NOT NULL, ADD picture VARCHAR(255) NOT NULL, ADD file VARCHAR(255) NOT NULL, ADD creation_date DATE NOT NULL, ADD update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD games_id INT NOT NULL, CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C697FFC673 FOREIGN KEY (games_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_794381C697FFC673 ON review (games_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP sold, DROP price, DROP version, DROP picture, DROP file, DROP creation_date, DROP update_date');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C697FFC673');
        $this->addSql('DROP INDEX IDX_794381C697FFC673 ON review');
        $this->addSql('ALTER TABLE review DROP games_id, CHANGE author_id author_id INT NOT NULL');
    }
}
