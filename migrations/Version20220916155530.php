<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916155530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(512) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_atelier (subscription_id INT NOT NULL, atelier_id INT NOT NULL, INDEX IDX_92AEF8B89A1887DC (subscription_id), INDEX IDX_92AEF8B882E2CF35 (atelier_id), PRIMARY KEY(subscription_id, atelier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription_atelier ADD CONSTRAINT FK_92AEF8B89A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_atelier ADD CONSTRAINT FK_92AEF8B882E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription DROP ateliers_de_formation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_atelier DROP FOREIGN KEY FK_92AEF8B89A1887DC');
        $this->addSql('ALTER TABLE subscription_atelier DROP FOREIGN KEY FK_92AEF8B882E2CF35');
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE subscription_atelier');
        $this->addSql('ALTER TABLE subscription ADD ateliers_de_formation LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
