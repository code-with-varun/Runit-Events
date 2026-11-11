<?php
namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRService {
    /**
     * Generate Base64 Data URI for inline display or PDF embedding
     */
    public static function generateDataUri(string $data): string {
        try {
            $options = new QROptions([
                'version' => 5,
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => true,
            ]);

            return (new QRCode($options))->render($data);
        } catch (\Throwable $e) {
            // Fallback SVG data URI if image extension not loaded
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120">'
                 . '<rect width="120" height="120" fill="#f8f9fa" stroke="#000" stroke-width="2"/>'
                 . '<text x="10" y="65" font-family="sans-serif" font-size="10" fill="#333">QR: ' . htmlspecialchars(substr($data, 0, 14)) . '</text>'
                 . '</svg>';
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
    }
}
