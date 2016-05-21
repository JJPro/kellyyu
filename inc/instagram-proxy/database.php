<?php
require_once( explode( "wp-content" , __FILE__ )[0] . 'wp-config.php' ); // constants for database connection

class MySQLDatabase {
    public $conn;

    function __construct() {
        $this->open_connection();
    }

    public function mysql_prep($string) {
        return $this->conn->real_escape_string($string);
    }

    /*
     * @param: $query must be escaped string
     */
    public function query ($query) {
        $result = $this->conn->query($query);
        $this->confirm_query($query, $result);
        return $result;
    }

    /* 
     * escape special characters to prevent SQL injection 
     */
    public function escape_string($string) {
        return $this->conn->escape_string($string);
    }

    public function close_connection() {
        if (isset($this->conn)) {
            $this->conn->close();
            unset($this->conn);
        }
    }


    private function open_connection() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if($this->conn->connect_error) {
            die("Connect Error (" . $this->conn->connect_errno . ") " .
                $this->conn->connect_error);
        }
    }
    private function confirm_query($query, $result) {
        if (!$result) {
            error_log('query: ' . $query);
            die("Database query failed.");
        }
    }

}


$database = new MySQLDatabase();