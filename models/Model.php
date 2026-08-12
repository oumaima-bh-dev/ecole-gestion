<?php
class Model {
    protected $db;

    public function __construct() {
        require_once dirname(__DIR__) . '/config/Database.php';
        $this->db = Database::getConnection();
    }
}
