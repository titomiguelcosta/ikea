<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201023153424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Created tables for articles, products and their relation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, stock INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_article (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, article_id INT NOT NULL, amount INT NOT NULL, INDEX IDX_D3E315D64584665A (product_id), INDEX IDX_D3E315D67294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_article ADD CONSTRAINT FK_D3E315D64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_article ADD CONSTRAINT FK_D3E315D67294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_article DROP FOREIGN KEY FK_D3E315D67294869C');
        $this->addSql('ALTER TABLE product_article DROP FOREIGN KEY FK_D3E315D64584665A');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_article');
    }
}
