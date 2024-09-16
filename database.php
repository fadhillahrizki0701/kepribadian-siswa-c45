<?php

class database {
    private $servername;
    private $user_db;
    private $password_db;
    private $database;
    private $koneksi;

    // Constructor
    public function __construct() {
        $this->load_conf_db();
        $this->connect_db();
    }

    // Load database configuration
    private function load_conf_db() {
        $path = dirname(__FILE__) . '/koneksi.php';
        if (file_exists($path)) {
            $conf = include $path;
            $this->servername = $conf['host'] ?? 'localhost';
            $this->database = $conf['dbname'] ?? '';
            $this->user_db = $conf['username'] ?? 'root';
            $this->password_db = $conf['password'] ?? '';
        }
    }

    // Connect to database
    public function connect_db() {
        $this->koneksi = mysqli_connect($this->servername, $this->user_db, $this->password_db, $this->database);
        if (!$this->koneksi) {
            die("Koneksi gagal: " . mysqli_connect_error());
        }
    }

    // Execute query
    public function db_query($sql) {
        return mysqli_query($this->koneksi, $sql);
    }

    // Get error
    public function db_error() {
        return mysqli_error($this->koneksi);
    }

    // Fetch array
    public function db_fetch_array($result) {
        return mysqli_fetch_array($result);
    }

    // Get number of rows
    public function db_num_rows($result) {
        return mysqli_num_rows($result);
    }

    // Get last insert ID
    public function db_insert_id() {
        return mysqli_insert_id($this->koneksi);
    }

    // Insert record
    public function insert_record($table, array $val_cols) {
        $field = implode("`, `", array_keys($val_cols));
        $StValue = array_map(function($value) { return "'" . $value . "'"; }, $val_cols);
        $StValues = implode(", ", $StValue);
        $sql = "INSERT INTO $table (`$field`) VALUES ($StValues)";
        return $this->db_query($sql);
    }

    // Delete record
    public function delete_record($table, array $val_cols) {
        $exp = array_map(function($key, $value) { return "$key = '$value'"; }, array_keys($val_cols), $val_cols);
        $Stexp = implode(" AND ", $exp);
        $sql = "DELETE FROM $table WHERE $Stexp";
        return $this->db_query($sql);
    }

    // Update record
    public function update_record($table, array $set_val_cols, array $cod_val_cols) {
        $set = array_map(function($key, $value) { return "$key = '$value'"; }, array_keys($set_val_cols), $set_val_cols);
        $Stset = implode(", ", $set);
        $cod = array_map(function($key, $value) { return "$key = '$value'"; }, array_keys($cod_val_cols), $cod_val_cols);
        $Stcod = implode(" AND ", $cod);
        $sql = "UPDATE $table SET $Stset WHERE $Stcod";
        return $this->db_query($sql);
    }

    // Count data
    public function count_data($table, $field, $where = null) {
        $sql = "SELECT COUNT($field) FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $result = $this->db_query($sql);
        return $this->db_fetch_array($result);
    }

    // Display all columns
    public function display_table_all_column($table, $where = null, $fetch = false, $limit = false, $limit_posisi = 0, $limit_batas = 0, $sort = '') {
        $sql = "SELECT * FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        if ($sort) {
            $sql .= " ORDER BY $sort";
        }
        if ($limit) {
            $sql .= " LIMIT $limit_posisi, $limit_batas";
        }
        $result = $this->db_query($sql);
        return $fetch ? $this->db_fetch_array($result) : $result;
    }

    // Find in table
    public function find_in_table($table, $find_column = array(), $where = '') {
        $column = is_array($find_column) ? implode(",", $find_column) : $find_column;
        $sql = "SELECT $column FROM $table $where";
        $result = $this->db_query($sql);
        return $this->db_fetch_array($result);
    }

    // Check if data exists
    public function cek_data_is_in_table($table, $field, $value) {
        $sql = "SELECT COUNT($field) FROM $table WHERE $field = '$value'";
        $result = $this->db_query($sql);
        $num = $this->db_fetch_array($result);
        return $num[0] > 0;
    }

    // Get login by ID
    public function get_login_by_id($id_login) {
        $sql = "SELECT * FROM login WHERE id_login = $id_login";
        $result = $this->db_query($sql);
        return $this->db_fetch_array($result);
    }
}
?>
