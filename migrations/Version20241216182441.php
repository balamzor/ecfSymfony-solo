<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216182441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notation (id INT AUTO_INCREMENT NOT NULL, id_book_id INT NOT NULL, user_id_id INT NOT NULL, note INT NOT NULL, comment VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_268BC95C83F1AF1 (id_book_id), INDEX IDX_268BC959D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC95C83F1AF1 FOREIGN KEY (id_book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC959D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC95C83F1AF1');
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC959D86650F');
        $this->addSql('DROP TABLE notation');
    }
}
