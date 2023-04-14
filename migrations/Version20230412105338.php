<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412105338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `cart` (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, order_history_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BA388B79D86650F (user_id_id), INDEX IDX_BA388B7ADDF7907 (order_history_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, order_ref_id INT DEFAULT NULL, quantity INT DEFAULT 1 NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_F0FE25274584665A (product_id), INDEX IDX_F0FE2527E238517C (order_ref_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_history (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(255) NOT NULL, cart_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `cart` ADD CONSTRAINT FK_BA388B79D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `cart` ADD CONSTRAINT FK_BA388B7ADDF7907 FOREIGN KEY (order_history_id) REFERENCES order_history (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527E238517C FOREIGN KEY (order_ref_id) REFERENCES `cart` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `cart` DROP FOREIGN KEY FK_BA388B79D86650F');
        $this->addSql('ALTER TABLE `cart` DROP FOREIGN KEY FK_BA388B7ADDF7907');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25274584665A');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527E238517C');
        $this->addSql('DROP TABLE `cart`');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE order_history');
    }
}
