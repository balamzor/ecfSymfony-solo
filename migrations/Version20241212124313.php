<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212124313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subsciption DROP FOREIGN KEY FK_CB2EE0E579F37AE5');
        $this->addSql('DROP TABLE subsciption');
        $this->addSql('ALTER TABLE subscriptions ADD id_user_id INT NOT NULL, ADD date_debut DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL, DROP debut_abonnement, DROP fin_abonnement');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A0179F37AE5 FOREIGN KEY (id_user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_4778A0179F37AE5 ON subscriptions (id_user_id)');
        $this->addSql('ALTER TABLE workspace ADD image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subsciption (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_CB2EE0E579F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE subsciption ADD CONSTRAINT FK_CB2EE0E579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE workspace DROP image');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A0179F37AE5');
        $this->addSql('DROP INDEX IDX_4778A0179F37AE5 ON subscriptions');
        $this->addSql('ALTER TABLE subscriptions ADD debut_abonnement DATETIME NOT NULL, ADD fin_abonnement DATETIME NOT NULL, DROP id_user_id, DROP date_debut, DROP date_fin');
    }
}
