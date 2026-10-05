<?php

class Fix_user_name_columns {

    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->database();
    }

    public function up()
    {
        $this->_lava->db->raw("
            ALTER TABLE users
            MODIFY firstname VARCHAR(100) NULL,
            MODIFY lastname VARCHAR(100) NULL
        ");
    }

    public function down()
    {
        $this->_lava->db->raw("
            ALTER TABLE users
            MODIFY firstname VARCHAR(100) NOT NULL,
            MODIFY lastname VARCHAR(100) NOT NULL
        ");
    }
}