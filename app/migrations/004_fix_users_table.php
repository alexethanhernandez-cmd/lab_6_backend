<?php

class Fix_users_table {

    private $_lava;
    protected $dbforge;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
        $this->_lava->call->database();
    }

    public function up()
    {
        $this->_lava->db->raw("
            ALTER TABLE users
            ADD COLUMN password VARCHAR(255) NULL AFTER username,
            ADD COLUMN role ENUM('admin','moderator','user') NOT NULL DEFAULT 'user' AFTER password,
            ADD COLUMN is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER role,
            ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER is_active,
            ADD COLUMN updated_at DATETIME NULL DEFAULT NULL AFTER created_at
        ");
    }

    public function down()
    {
        $this->_lava->db->raw("
            ALTER TABLE users
            DROP COLUMN updated_at,
            DROP COLUMN created_at,
            DROP COLUMN is_active,
            DROP COLUMN role,
            DROP COLUMN password
        ");
    }
}