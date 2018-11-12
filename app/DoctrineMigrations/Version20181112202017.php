<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181112202017 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE my_user ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE my_user ADD CONSTRAINT FK_4DB4FF1DFE54D947 FOREIGN KEY (group_id) REFERENCES my_group (id)');
        $this->addSql('CREATE INDEX IDX_4DB4FF1DFE54D947 ON my_user (group_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE my_user DROP FOREIGN KEY FK_4DB4FF1DFE54D947');
        $this->addSql('DROP INDEX IDX_4DB4FF1DFE54D947 ON my_user');
        $this->addSql('ALTER TABLE my_user DROP group_id');
    }
}
