<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120103121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C13F6C1480 FOREIGN KEY (id_size_stock) REFERENCES product_size (id)');
        $this->addSql('CREATE INDEX IDX_845CA2C13F6C1480 ON order_details (id_size_stock)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C13F6C1480');
        $this->addSql('DROP INDEX IDX_845CA2C13F6C1480 ON order_details');
    }
}
