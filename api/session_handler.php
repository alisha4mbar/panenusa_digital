<?php
// File: session_handler.php
// Database session handler untuk Vercel serverless

class DatabaseSessionHandler {
    private $conn;
    private $table = 'user_sessions';
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function open($savePath, $sessionName) {
        return true;
    }
    
    public function close() {
        return true;
    }
    
    public function read($sessionId) {
        $sql = "SELECT session_data FROM {$this->table} WHERE session_id = ? AND last_activity > ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        $expiry = time() - 7200; // 2 jam timeout
        mysqli_stmt_bind_param($stmt, "si", $sessionId, $expiry);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['session_data'] ?: '';
        }
        return '';
    }
    
    public function write($sessionId, $sessionData) {
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_nama = $_SESSION['nama'] ?? '';
        $user_role = $_SESSION['role'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $last_activity = time();
        
        // Cek apakah session sudah ada
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE session_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $sessionId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if ($count > 0) {
            // Update existing session
            $sql = "UPDATE {$this->table} 
                    SET user_id = ?, user_nama = ?, user_role = ?, 
                        session_data = ?, ip_address = ?, user_agent = ?, 
                        last_activity = ?, created_at = CURRENT_TIMESTAMP
                    WHERE session_id = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssssis", 
                $user_id, $user_nama, $user_role, $sessionData, 
                $ip_address, $user_agent, $last_activity, $sessionId
            );
        } else {
            // Insert new session
            $sql = "INSERT INTO {$this->table} 
                    (session_id, user_id, user_nama, user_role, session_data, 
                     ip_address, user_agent, last_activity) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "sisssssi", 
                $sessionId, $user_id, $user_nama, $user_role, $sessionData, 
                $ip_address, $user_agent, $last_activity
            );
        }
        
        return mysqli_stmt_execute($stmt);
    }
    
    public function destroy($sessionId) {
        $sql = "DELETE FROM {$this->table} WHERE session_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $sessionId);
        return mysqli_stmt_execute($stmt);
    }
    
    public function gc($lifetime) {
        $sql = "DELETE FROM {$this->table} WHERE last_activity < ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        $expiry = time() - $lifetime;
        mysqli_stmt_bind_param($stmt, "i", $expiry);
        return mysqli_stmt_execute($stmt);
    }
}

// Inisialisasi session handler
function initDatabaseSession($conn) {
    $handler = new DatabaseSessionHandler($conn);
    session_set_save_handler(
        [$handler, 'open'],
        [$handler, 'close'], 
        [$handler, 'read'],
        [$handler, 'write'],
        [$handler, 'destroy'],
        [$handler, 'gc']
    );
    
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 7200,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
    return $handler;
}
?>