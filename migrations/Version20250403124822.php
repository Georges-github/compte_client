<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403124822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, id_publication_id INT NOT NULL, id_commentaire_parent_id INT DEFAULT NULL, texte LONGTEXT NOT NULL, date_heure_insertion DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_heure_maj DATETIME DEFAULT NULL, INDEX IDX_67F068BC5D4AAA1 (id_publication_id), INDEX IDX_67F068BC2C5AD247 (id_commentaire_parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE contrat (id INT AUTO_INCREMENT NOT NULL, id_utilisateur_id INT NOT NULL, intitule VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_debut_prevue DATETIME DEFAULT NULL, date_fin_prevue DATETIME DEFAULT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, chemin_fichier VARCHAR(255) DEFAULT NULL, date_heure_insertion DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_heure_maj DATETIME DEFAULT NULL, INDEX IDX_60349993C6EE5C49 (id_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE etat_contrat (id INT AUTO_INCREMENT NOT NULL, id_utilisateur_id INT DEFAULT NULL, id_contrat_id INT NOT NULL, etat VARCHAR(255) NOT NULL, date_heure_insertion DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_heure_maj DATETIME DEFAULT NULL, INDEX IDX_67EBA616C6EE5C49 (id_utilisateur_id), INDEX IDX_67EBA616BDA986C8 (id_contrat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, id_publication_id INT NOT NULL, id_commentaire_id INT DEFAULT NULL, legende VARCHAR(255) DEFAULT NULL, chemin_fichier_image VARCHAR(500) NOT NULL, date_heure_insertion DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_heure_maj DATETIME DEFAULT NULL, INDEX IDX_14B784185D4AAA1 (id_publication_id), INDEX IDX_14B7841887FA6C96 (id_commentaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, id_utilisateur_id INT NOT NULL, id_contrat_id INT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_heure_insertion DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_heure_maj DATETIME DEFAULT NULL, INDEX IDX_AF3C6779C6EE5C49 (id_utilisateur_id), INDEX IDX_AF3C6779BDA986C8 (id_contrat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, courriel VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, genre VARCHAR(255) NOT NULL, telephone VARCHAR(30) DEFAULT NULL, rue_et_numero VARCHAR(255) NOT NULL, code_postal VARCHAR(20) NOT NULL, ville VARCHAR(100) NOT NULL, societe VARCHAR(100) DEFAULT NULL, date_heure_insertion DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', date_heure_maj DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_COURRIEL (courriel), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC5D4AAA1 FOREIGN KEY (id_publication_id) REFERENCES publication (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC2C5AD247 FOREIGN KEY (id_commentaire_parent_id) REFERENCES commentaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_60349993C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat ADD CONSTRAINT FK_67EBA616C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat ADD CONSTRAINT FK_67EBA616BDA986C8 FOREIGN KEY (id_contrat_id) REFERENCES contrat (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_14B784185D4AAA1 FOREIGN KEY (id_publication_id) REFERENCES publication (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_14B7841887FA6C96 FOREIGN KEY (id_commentaire_id) REFERENCES commentaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779BDA986C8 FOREIGN KEY (id_contrat_id) REFERENCES contrat (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC5D4AAA1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC2C5AD247
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993C6EE5C49
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat DROP FOREIGN KEY FK_67EBA616C6EE5C49
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etat_contrat DROP FOREIGN KEY FK_67EBA616BDA986C8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_14B784185D4AAA1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_14B7841887FA6C96
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779C6EE5C49
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779BDA986C8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commentaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contrat
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE etat_contrat
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE photo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE publication
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
