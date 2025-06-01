<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601201504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC2C5AD247
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC2C5AD247 FOREIGN KEY (id_commentaire_parent_id) REFERENCES commentaire (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_14B784185D4AAA1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_14B7841887FA6C96
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_14B784185D4AAA1 FOREIGN KEY (id_publication_id) REFERENCES publication (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_14B7841887FA6C96 FOREIGN KEY (id_commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC2C5AD247
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC2C5AD247 FOREIGN KEY (id_commentaire_parent_id) REFERENCES commentaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_14B784185D4AAA1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_14B7841887FA6C96
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_14B784185D4AAA1 FOREIGN KEY (id_publication_id) REFERENCES publication (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_14B7841887FA6C96 FOREIGN KEY (id_commentaire_id) REFERENCES commentaire (id)
        SQL);
    }
}
