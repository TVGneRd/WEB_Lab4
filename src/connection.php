<?php
require_once "config.php";

$mysqli_class_name = 'mysqli';
if (version_compare(PHP_VERSION, '8.2', '<')) {

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    function mysqli_execute_query(mysqli $mysqli, string $query, ?array $params = null)
    {
        $stmt = $mysqli->prepare($query);
        if ($params) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    class my_mysqli extends mysqli
    {
        public function execute_query(string $query, ?array $params = null)
        {
            return mysqli_execute_query($this, $query, $params);
        }
    }
    $mysqli_class_name = 'my_mysqli';
}

/** @var mysqli */
$mysqli = new $mysqli_class_name(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}