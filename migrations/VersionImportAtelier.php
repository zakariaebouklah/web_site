<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class VersionImportAtelier extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into atelier (name) values (\"Le doctorat n'est pas un long fleuve tranquille (DEKHISSI Ilyass - EST - UMP Oujda)\")");
        $this->addSql("insert into atelier (name) values (\"Développer son esprit scientifique par les 2C : Connaissance & Conscience  (MAJIDI Fouzia - FSJES - UMP Oujda)\")");
        $this->addSql("insert into atelier (name) values (\"La motivation dans la recherche scientifique (BERRICHI Abdelouahed - FSJES - UMP Oujda)\")");
        $this->addSql("insert into atelier (name) values (\"Spécifier son objet de recherche (EL ATTAR Abdellilah - FSJES - UMP Oujda)\")");
        $this->addSql("insert into atelier (name) values (\"Conduire son projet de recherche selon la perspective quantitative et qualitative (Karim BENNIS - FSJES - USMBA Fès)\")");
        $this->addSql("insert into atelier (name) values (\"Design de recherche : construire son modèle de recherche dans une perspective hypothético-déductive (HAFIANE Mohammed Amine - FSJES - UMP Oujda)\")");
        $this->addSql("insert into atelier (name) values (\"Réussir son étude empirique : De l'opérationnalisation à la modélisation par les équations structurelles et la vérification des hypothèses (EDDAOU Mohammed - FSJES- UMP Oujda)\")");
        $this->addSql("insert into atelier (name) values (\"L'art de rédiger son article scientifique (FIKRI Khalid - FSJES - UMP Oujda)\")");


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("delete from atelier");
    }
}
