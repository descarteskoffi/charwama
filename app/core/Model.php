<?php
/**
 * Classe Model de base
 */
class Model {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }
}
