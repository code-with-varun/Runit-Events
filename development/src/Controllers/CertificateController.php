<?php
namespace App\Controllers;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\User;
use App\Services\QRService;
use App\Services\PDFService;
use App\Database;

class CertificateController {
    public function download() {
        if (!User::isLoggedIn()) {
            header("Location: index.php?page=login");
            exit;
        }

        $certId = (int)($_GET['id'] ?? 0);
        $certNumber = $_GET['cert'] ?? '';

        if ($certId > 0) {
            $cert = Database::fetchOne("SELECT * FROM certificates WHERE id = ?", [$certId]);
        } else {
            $cert = Database::fetchOne("SELECT * FROM certificates WHERE cert_number = ?", [$certNumber]);
        }

        $currentUser = User::current();
        if (!$cert || ($cert['user_id'] != $currentUser['id'] && !User::isAdmin())) {
            die("Certificate not found or unauthorized.");
        }

        $event = Event::getById((int)$cert['event_id']);
        $user = User::findById((int)$cert['user_id']);

        // Update download count
        Database::execute("UPDATE certificates SET download_count = download_count + 1 WHERE id = ?", [$cert['id']]);

        // Generate QR code pointing to public verification page
        $verifyUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/index.php?page=verify-cert&cert=" . $cert['cert_number'];
        $qrDataUri = QRService::generateDataUri($verifyUrl);

        $pdf = PDFService::generateCertificate([
            'user_name' => $user['name'],
            'event_title' => $event['title'],
            'cert_number' => $cert['cert_number'],
            'issue_date' => date('F j, Y', strtotime($cert['issue_date'])),
            'qr_data_uri' => $qrDataUri,
            'instructor_name' => 'Dr. Sarah Mitchell',
            'instructor_title' => 'Lead Research Examiner & Instructor'
        ]);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $cert['cert_number'] . '.pdf"');
        echo $pdf;
        exit;
    }

    public function verify() {
        $certNumber = trim($_GET['cert'] ?? '');
        $cert = null;

        if (!empty($certNumber)) {
            $cert = Certificate::findByNumber($certNumber);
        }

        require __DIR__ . '/../../views/certificate/verify.php';
    }
}
