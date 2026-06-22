<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the tables for the SEO checks feature (issue #2): pages to test and the issues
 * detected on them. Supports MySQL/MariaDB and PostgreSQL.
 */
final class Version20260622093710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create setono_sylius_seo__page and setono_sylius_seo__issue tables';
    }

    public function up(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE setono_sylius_seo__page (id SERIAL NOT NULL, channel_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, localeCode VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, routeName VARCHAR(255) DEFAULT NULL, routeParameters JSON NOT NULL, sampleResourceId VARCHAR(255) DEFAULT NULL, checks JSON NOT NULL, enabled BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_64C9E45B72F5A1AA ON setono_sylius_seo__page (channel_id)');
            $this->addSql('CREATE TABLE setono_sylius_seo__issue (id SERIAL NOT NULL, page_id INT DEFAULT NULL, fingerprint VARCHAR(64) DEFAULT NULL, "check" VARCHAR(255) DEFAULT NULL, severity VARCHAR(255) DEFAULT NULL, messageTemplate VARCHAR(255) DEFAULT NULL, messageParameters JSON NOT NULL, url TEXT DEFAULT NULL, subjectType VARCHAR(255) DEFAULT NULL, subjectId VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT \'open\' NOT NULL, firstDetectedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, lastDetectedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, occurrenceCount INT DEFAULT 1 NOT NULL, context JSON NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_D50A48D8C4663E4 ON setono_sylius_seo__issue (page_id)');
            $this->addSql('CREATE UNIQUE INDEX setono_sylius_seo__issue_fingerprint ON setono_sylius_seo__issue (fingerprint)');
            $this->addSql('ALTER TABLE setono_sylius_seo__page ADD CONSTRAINT FK_64C9E45B72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE setono_sylius_seo__issue ADD CONSTRAINT FK_D50A48D8C4663E4 FOREIGN KEY (page_id) REFERENCES setono_sylius_seo__page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

            return;
        }

        $this->addSql('CREATE TABLE setono_sylius_seo__page (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, localeCode VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, routeName VARCHAR(255) DEFAULT NULL, routeParameters JSON NOT NULL COMMENT \'(DC2Type:json)\', sampleResourceId VARCHAR(255) DEFAULT NULL, checks JSON NOT NULL COMMENT \'(DC2Type:json)\', enabled TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_64C9E45B72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setono_sylius_seo__issue (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, fingerprint VARCHAR(64) DEFAULT NULL, `check` VARCHAR(255) DEFAULT NULL, severity VARCHAR(255) DEFAULT NULL, messageTemplate VARCHAR(255) DEFAULT NULL, messageParameters JSON NOT NULL COMMENT \'(DC2Type:json)\', url LONGTEXT DEFAULT NULL, subjectType VARCHAR(255) DEFAULT NULL, subjectId VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT \'open\' NOT NULL, firstDetectedAt DATETIME DEFAULT NULL, lastDetectedAt DATETIME DEFAULT NULL, occurrenceCount INT DEFAULT 1 NOT NULL, context JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_D50A48D8C4663E4 (page_id), UNIQUE INDEX setono_sylius_seo__issue_fingerprint (fingerprint), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE setono_sylius_seo__page ADD CONSTRAINT FK_64C9E45B72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE setono_sylius_seo__issue ADD CONSTRAINT FK_D50A48D8C4663E4 FOREIGN KEY (page_id) REFERENCES setono_sylius_seo__page (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE setono_sylius_seo__issue DROP CONSTRAINT FK_D50A48D8C4663E4');
            $this->addSql('ALTER TABLE setono_sylius_seo__page DROP CONSTRAINT FK_64C9E45B72F5A1AA');
        } else {
            $this->addSql('ALTER TABLE setono_sylius_seo__issue DROP FOREIGN KEY FK_D50A48D8C4663E4');
            $this->addSql('ALTER TABLE setono_sylius_seo__page DROP FOREIGN KEY FK_64C9E45B72F5A1AA');
        }

        $this->addSql('DROP TABLE setono_sylius_seo__issue');
        $this->addSql('DROP TABLE setono_sylius_seo__page');
    }
}
