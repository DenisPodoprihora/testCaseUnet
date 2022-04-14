<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413171525 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('user')) {
            $table = $schema->createTable('user');

            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);

            $table->addColumn('login', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('password', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('roles', 'json', ['notnull' => false]);

            $table->setPrimaryKey(['user_id']);
            $table->addUniqueIndex(['login'], 'login');

            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Table for storage users');
        }

        if (!$schema->hasTable('project')) {
            $table = $schema->createTable('project');

            $table->addColumn('project_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
            $table->addColumn('title', 'string', ['notnull' => true, 'length' => 255]);

            $table->setPrimaryKey(['project_id']);
            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Table for storage projects');
        }

        if (!$schema->hasTable('project_user')) {
            $table = $schema->createTable('project_user');
            $table->addColumn('id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);

            $table->addColumn('project_id', 'integer', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['project_id'], 'project_id');
            $table->addIndex(['user_id'], 'user_id');

            $table->addForeignKeyConstraint('project', ['project_id'], ['project_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_PROJECT_PROJECT_USER');
            $table->addForeignKeyConstraint('user', ['user_id'], ['user_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_PROJECT_USER');

            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Table for storage link projects and users');
        }

        if (!$schema->hasTable('task')) {
            $table = $schema->createTable('task');

            $table->addColumn('task_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);

            $table->addColumn('title', 'string', ['notnull' => true, 'length' => 15]);
            $table->addColumn('content', 'text', ['notnull' => false]);
            $table->addColumn('status','string', ['notnull' => true, 'default' => 'open']);
            $table->addColumn('created_at', 'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);
            $table->addColumn('user_id','integer', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('project_id','integer', ['unsigned' => true, 'notnull' => true]);

            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Table for storage tasks');

            $table->setPrimaryKey(['task_id']);

            $table->addIndex(['user_id'], 'user_id');
            $table->addIndex(['project_id'], 'project_id');

            $table->addForeignKeyConstraint('user', ['user_id'], ['user_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_TASK_USER');
            $table->addForeignKeyConstraint('project', ['project_id'], ['project_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_TASK_PROJECT');
        }
    }

    public function postUp(Schema $schema): void
    {
        $this->connection->executeQuery('ALTER TABLE tasks.task MODIFY status ENUM("open", "in process", "review", "done") NOT NULL DEFAULT "open"');

        $pass = '$2y$13$Di3MftSFvYnR7IqfyGHw5OJk/QJoYuRqQOwlcUCrdtjfq9Rq52Gvq'; //admin321
        $pass2 = '$2y$13$B3Mq/FyXxn8xTufVswVgh.Sm8XaWXrDdQNj80BmK7osMg/w40LP3i'; //userpass

        $this->connection->executeQuery("INSERT INTO tasks.user (login, password, roles) VALUES ('admin', '$pass', '[\"ROLE_ADMIN\"]')");

        $this->connection->executeQuery("INSERT INTO tasks.user (login, password, roles) VALUES ('testUser', '$pass2', '[]')");

        $this->connection->executeQuery("INSERT INTO tasks.user (login, password, roles) VALUES ('testUser2', '$pass2', '[]')");

        $this->connection->executeQuery("INSERT INTO tasks.project (title) VALUES (\"New web site\")");
        $this->connection->executeQuery("INSERT INTO tasks.project (title) VALUES (\"New mobile app\")");

        $this->connection->executeQuery("INSERT INTO tasks.project_user (project_id, user_id) VALUES ((SELECT project_id FROM tasks.project WHERE title = \"New web site\"), (SELECT user_id FROM tasks.user WHERE login = \"admin\"))");

        $this->connection->executeQuery("INSERT INTO tasks.project_user (project_id, user_id) VALUES ((SELECT project_id FROM tasks.project WHERE title = \"New web site\"), (SELECT user_id FROM tasks.user WHERE login = \"testUser\"))");

        $this->connection->executeQuery("INSERT INTO tasks.project_user (project_id, user_id) VALUES ((SELECT project_id FROM tasks.project WHERE title = \"New mobile app\"), (SELECT user_id FROM tasks.user WHERE login = \"testUser\"))");

        $this->connection->executeQuery("INSERT INTO tasks.project_user (project_id, user_id) VALUES ((SELECT project_id FROM tasks.project WHERE title = \"New mobile app\"), (SELECT user_id FROM tasks.user WHERE login = \"testUser2\"))");

        $this->connection->executeQuery("INSERT INTO tasks.task (user_id, project_id, title, content) VALUES ((SELECT user_id FROM tasks.user WHERE login = \"testUser\"), (SELECT project_id FROM tasks.project WHERE title = \"New web site\"), \"Create backend\", \"Write a lot of code....\")");

        $this->connection->executeQuery("INSERT INTO tasks.task (user_id, project_id, title, content, status) VALUES ((SELECT user_id FROM tasks.user WHERE login = 'testUser'), (SELECT project_id FROM tasks.project WHERE title = 'New web site'), 'Create frontEnd', 'Write a lot of js and css....', 'in process')");

        $this->connection->executeQuery("INSERT INTO tasks.task (user_id, project_id, title, content, status) VALUES ((SELECT user_id FROM tasks.user WHERE login = 'testUser'), (SELECT project_id FROM tasks.project WHERE title = 'New mobile app'), 'Process logic', 'New develop some logic for app', 'done')");

        $this->connection->executeQuery("INSERT INTO tasks.task (user_id, project_id, title, content, status) VALUES ((SELECT user_id FROM tasks.user WHERE login = 'testUser2'), (SELECT project_id FROM tasks.project WHERE title = 'New mobile app'), 'Test app', 'Need test some methods and API', 'review')");
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('task')) {
            $table = $schema->getTable('task');

            if ($table->hasForeignKey('FK_TASK_USER')) {
                $table->removeForeignKey('FK_TASK_USER');
            }

            if ($table->hasForeignKey('FK_TASK_PROJECT')) {
                $table->removeForeignKey('FK_TASK_PROJECT');
            }
            $schema->dropTable('task');
        }

        if ($schema->hasTable('project_user')) {
            $table = $schema->getTable('project_user');

            if ($table->hasForeignKey('FK_PROJECT_PROJECT_USER')) {
                $table->removeForeignKey('FK_PROJECT_PROJECT_USER');
            }

            if ($table->hasForeignKey('FK_USER_PROJECT_USER')) {
                $table->removeForeignKey('FK_USER_PROJECT_USER');
            }

            $schema->dropTable('project_user');
        }

        if ($schema->hasTable('project')) {
            $schema->dropTable('project');
        }

        if ($schema->hasTable('user')) {
            $schema->dropTable('user');
        }
    }
}
