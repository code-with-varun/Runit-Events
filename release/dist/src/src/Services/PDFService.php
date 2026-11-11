<?php
namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFService {
    private static function getDompdf(string $orientation = 'portrait'): Dompdf {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', $orientation);
        return $dompdf;
    }

    /**
     * Generate Certificate of Completion PDF
     */
    public static function generateCertificate(array $certData): string {
        $dompdf = self::getDompdf('landscape');
        
        $attendeeName = htmlspecialchars($certData['user_name'] ?? 'Attendee');
        $eventTitle = htmlspecialchars($certData['event_title'] ?? 'Technical Webinar');
        $certNumber = htmlspecialchars($certData['cert_number'] ?? 'CERT-2026-0000000');
        $issueDate = htmlspecialchars($certData['issue_date'] ?? date('F j, Y'));
        $qrDataUri = $certData['qr_data_uri'] ?? '';
        $instructorName = htmlspecialchars($certData['instructor_name'] ?? 'Dr. Sarah Mitchell');
        $instructorTitle = htmlspecialchars($certData['instructor_title'] ?? 'Principal Lead Instructor');

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                @page { margin: 0; }
                body {
                    font-family: 'Helvetica', Arial, sans-serif;
                    margin: 0;
                    padding: 40px;
                    background-color: #0b1329;
                    color: #ffffff;
                }
                .cert-container {
                    border: 8px solid #c59b27;
                    padding: 35px 45px;
                    background: #0f172a;
                    position: relative;
                    text-align: center;
                    border-radius: 4px;
                }
                .inner-border {
                    border: 2px dashed rgba(197, 155, 39, 0.5);
                    padding: 30px;
                }
                .badge-header {
                    color: #d4af37;
                    font-size: 14px;
                    letter-spacing: 5px;
                    text-transform: uppercase;
                    margin-bottom: 10px;
                    font-weight: bold;
                }
                h1 {
                    color: #ffffff;
                    font-size: 38px;
                    letter-spacing: 2px;
                    margin: 10px 0 20px 0;
                    text-transform: uppercase;
                }
                .presented-to {
                    color: #94a3b8;
                    font-size: 16px;
                    font-style: italic;
                    margin-bottom: 15px;
                }
                .attendee-name {
                    color: #f8fafc;
                    font-size: 34px;
                    font-weight: bold;
                    border-bottom: 2px solid #c59b27;
                    display: inline-block;
                    padding-bottom: 6px;
                    margin-bottom: 20px;
                    letter-spacing: 1px;
                }
                .completion-text {
                    color: #cbd5e1;
                    font-size: 15px;
                    line-height: 1.6;
                    max-width: 650px;
                    margin: 0 auto 30px auto;
                }
                .event-name {
                    color: #38bdf8;
                    font-weight: bold;
                    font-size: 17px;
                }
                .footer-table {
                    width: 100%;
                    margin-top: 35px;
                    border-top: 1px solid rgba(148, 163, 184, 0.2);
                    padding-top: 20px;
                }
                .footer-table td {
                    vertical-align: middle;
                    text-align: left;
                }
                .sig-line {
                    border-bottom: 1px solid #c59b27;
                    width: 180px;
                    margin-bottom: 6px;
                }
                .sig-title {
                    font-size: 11px;
                    color: #94a3b8;
                }
                .cert-meta {
                    text-align: right !important;
                    font-size: 11px;
                    color: #94a3b8;
                }
                .cert-meta strong {
                    color: #e2e8f0;
                }
                .qr-cell {
                    text-align: center !important;
                }
                .qr-img {
                    width: 85px;
                    height: 85px;
                    background: #fff;
                    padding: 4px;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='cert-container'>
                <div class='inner-border'>
                    <div class='badge-header'>RUNIT EVENTS &bull; CERTIFICATE OF COMPLETION</div>
                    <h1>Certificate of Achievement</h1>
                    <div class='presented-to'>This is proudly presented and certified to</div>
                    <div class='attendee-name'>{$attendeeName}</div>
                    <div class='completion-text'>
                        for successfully attending, participating in, and fulfilling all duration requirements for the enterprise masterclass on<br>
                        <span class='event-name'>{$eventTitle}</span>
                    </div>

                    <table class='footer-table'>
                        <tr>
                            <td style='width: 33%;'>
                                <div class='sig-line'></div>
                                <strong style='font-size: 13px; color: #fff;'>{$instructorName}</strong><br>
                                <span class='sig-title'>{$instructorTitle}</span>
                            </td>
                            <td style='width: 34%;' class='qr-cell'>
                                " . ($qrDataUri ? "<img src='{$qrDataUri}' class='qr-img' alt='Verification QR'>" : "") . "
                                <div style='font-size: 9px; color: #94a3b8; margin-top: 4px;'>Scan to Verify Authenticity</div>
                            </td>
                            <td style='width: 33%;' class='cert-meta'>
                                Certificate ID: <strong>{$certNumber}</strong><br>
                                Issue Date: <strong>{$issueDate}</strong><br>
                                Verification: <strong>Verified Attendee</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </body>
        </html>
        ";

        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->output();
    }

    /**
     * Generate Ticket Pass PDF
     */
    public static function generatePass(array $passData): string {
        $dompdf = self::getDompdf('portrait');

        $bookingCode = htmlspecialchars($passData['booking_code'] ?? 'EVT202610001245');
        $eventTitle = htmlspecialchars($passData['event_title'] ?? 'Event');
        $eventDate = htmlspecialchars($passData['event_date'] ?? date('Y-m-d'));
        $startTime = htmlspecialchars($passData['start_time'] ?? '18:00');
        $duration = htmlspecialchars($passData['duration'] ?? '60 mins');
        $attendeeName = htmlspecialchars($passData['attendee_name'] ?? 'Attendee');
        $attendeeEmail = htmlspecialchars($passData['attendee_email'] ?? 'attendee@example.com');
        $meetingPlatform = htmlspecialchars($passData['meeting_platform'] ?? 'Zoom');
        $meetingLink = htmlspecialchars($passData['meeting_link'] ?? '#');
        $qrDataUri = $passData['qr_data_uri'] ?? '';

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body {
                    font-family: 'Helvetica', Arial, sans-serif;
                    margin: 20px;
                    background: #f1f5f9;
                    color: #0f172a;
                }
                .ticket {
                    background: #fff;
                    border: 2px solid #e2e8f0;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                    max-width: 600px;
                    margin: 0 auto;
                }
                .ticket-header {
                    background: #1e293b;
                    color: #fff;
                    padding: 24px;
                    border-bottom: 4px solid #3b82f6;
                }
                .ticket-body {
                    padding: 24px;
                }
                .tag {
                    display: inline-block;
                    background: #3b82f6;
                    color: #fff;
                    font-size: 11px;
                    font-weight: bold;
                    padding: 4px 8px;
                    border-radius: 4px;
                    text-transform: uppercase;
                    margin-bottom: 8px;
                }
                h2 {
                    margin: 0 0 10px 0;
                    font-size: 22px;
                    color: #ffffff;
                }
                .meta-table {
                    width: 100%;
                    margin: 20px 0;
                    border-collapse: collapse;
                }
                .meta-table td {
                    padding: 10px;
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 13px;
                }
                .label {
                    color: #64748b;
                    font-weight: bold;
                    width: 35%;
                }
                .val {
                    color: #0f172a;
                    font-weight: 500;
                }
                .qr-section {
                    text-align: center;
                    background: #f8fafc;
                    padding: 20px;
                    border-top: 2px dashed #cbd5e1;
                }
                .qr-img {
                    width: 120px;
                    height: 120px;
                }
                .instructions {
                    font-size: 11px;
                    color: #64748b;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class='ticket'>
                <div class='ticket-header'>
                    <div class='tag'>CONFIRMED ATTENDEE PASS</div>
                    <h2>{$eventTitle}</h2>
                    <div style='color: #94a3b8; font-size: 13px;'>Pass Reference: <strong>{$bookingCode}</strong></div>
                </div>
                <div class='ticket-body'>
                    <table class='meta-table'>
                        <tr>
                            <td class='label'>Attendee Name</td>
                            <td class='val'>{$attendeeName}</td>
                        </tr>
                        <tr>
                            <td class='label'>Email Address</td>
                            <td class='val'>{$attendeeEmail}</td>
                        </tr>
                        <tr>
                            <td class='label'>Date & Time</td>
                            <td class='val'>{$eventDate} at {$startTime} ({$duration})</td>
                        </tr>
                        <tr>
                            <td class='label'>Platform / Venue</td>
                            <td class='val'>{$meetingPlatform} Online</td>
                        </tr>
                        <tr>
                            <td class='label'>Meeting Access Link</td>
                            <td class='val' style='color: #2563eb;'>{$meetingLink}</td>
                        </tr>
                    </table>

                    <div class='qr-section'>
                        " . ($qrDataUri ? "<img src='{$qrDataUri}' class='qr-img' alt='Pass QR Code'>" : "") . "
                        <div style='font-size: 12px; font-weight: bold; margin-top: 6px;'>Verification Code: {$bookingCode}</div>
                        <div class='instructions'>Present this QR code during virtual check-in or scan to record your session attendance. Attendance of 75%+ is required for certificate eligibility.</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->output();
    }
}
