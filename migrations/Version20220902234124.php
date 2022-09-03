<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220902234124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment_for_topics (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, topic_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2594303DF675F31B (author_id), INDEX IDX_2594303D1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment_for_topics ADD CONSTRAINT FK_2594303DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_for_topics ADD CONSTRAINT FK_2594303D1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_for_topics DROP FOREIGN KEY FK_2594303DF675F31B');
        $this->addSql('ALTER TABLE comment_for_topics DROP FOREIGN KEY FK_2594303D1F55203D');
        $this->addSql('DROP TABLE comment_for_topics');
    }
}
