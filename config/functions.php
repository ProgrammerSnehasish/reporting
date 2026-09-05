<?php

/**
 * ============================================================================
 * Global Helper Functions
 * Luminia Lifecare - Reporting System
 * ============================================================================
 */

if (!function_exists('clean')) {
    function clean($data) {
        if (is_array($data)) {
            return array_map('clean', $data);
        }
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        if (!headers_sent()) {
            header("Location: " . $url);
        } else {
            echo "<script>window.location.href='" . addslashes($url) . "';</script>";
        }
        exit;
    }
}

if (!function_exists('showAlert')) {
    function showAlert($message, $type = 'success') {
        $alertClass = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
        $msg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        return "<div class='alert alert-{$alertClass} alert-dismissible fade show' role='alert'>
            {$msg}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}

if (!function_exists('generateEmployeeCode')) {
    function generateEmployeeCode($prefix = 'LLC') {
        return strtoupper($prefix) . '-' . date('Y') . '-' . mt_rand(1000, 9999);
    }
}

if (!function_exists('uploadImage')) {
    function uploadImage($fileInput, $targetDir = '../uploads/') {
        if (!isset($fileInput) || $fileInput['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'No file uploaded or upload error occurred.'];
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($fileInput['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

        if (!in_array($ext, $allowed, true)) {
            return ['success' => false, 'error' => 'Invalid file extension.'];
        }

        $newFileName = uniqid('doc_', true) . '.' . $ext;
        $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $newFileName;

        if (move_uploaded_file($fileInput['tmp_name'], $destination)) {
            return ['success' => true, 'file_name' => $newFileName, 'path' => $destination];
        }

        return ['success' => false, 'error' => 'Failed to move uploaded file.'];
    }
}