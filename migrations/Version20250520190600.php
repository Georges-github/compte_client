<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520190600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat DROP FOREIGN KEY FK_67EBA616BDA986C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat ADD CONSTRAINT FK_67EBA616BDA986C8 FOREIGN KEY (id_contrat_id) REFERENCES contrat (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo CHANGE id_publication_id id_publication_id INT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat DROP FOREIGN KEY FK_67EBA616BDA986C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat ADD CONSTRAINT FK_67EBA616BDA986C8 FOREIGN KEY (id_contrat_id) REFERENCES contrat (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo CHANGE id_publication_id id_publication_id INT NOT NULL
        SQL);
    }
}
