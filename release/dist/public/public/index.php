<?php
// public/index.php - Front Controller
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';

use App\Controllers\HomeController;
use App\Controllers\EventController;
use App\Controllers\BookingController;
use App\Controllers\CertificateController;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;

// 1. Resolve Route: support query parameter (?page=...) and SEO friendly URLs
$page = $_GET['page'] ?? null;

if (!$page) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

    // Strip subfolder prefixes if hosted in subdirectories
    $prefixes = ['/events/public', '/events', '/public'];
    foreach ($prefixes as $p) {
        if (strpos($path, $p) === 0) {
            $path = substr($path, strlen($p));
            break;
        }
    }

    $path = trim($path, '/');
    if (strpos($path, 'index.php') === 0) {
        $path = trim(substr($path, strlen('index.php')), '/');
    }

    $segments = $path !== '' ? explode('/', $path) : [];

    if (empty($segments)) {
        $page = 'home';
    } else {
        $first = strtolower($segments[0]);

        switch ($first) {
            case 'explore':
            case 'events':
                $page = 'events';
                if (!empty($segments[1]) && !isset($_GET['type'])) {
                    $_GET['type'] = urldecode($segments[1]);
                }
                break;

            case 'event':
                $page = 'event-detail';
                if (isset($segments[1])) {
                    $_GET['id'] = $segments[1];
                }
                break;

            case 'past':
                $page = 'past-event';
                if (isset($segments[1])) {
                    $_GET['id'] = $segments[1];
                }
                break;

            case 'checkout':
                $page = 'checkout';
                if (isset($segments[1])) {
                    $_GET['event_id'] = $segments[1];
                }
                break;

            case 'book-process':
                $page = 'book-process';
                break;

            case 'booking-success':
                $page = 'booking-success';
                if (isset($segments[1])) {
                    $_GET['booking_id'] = $segments[1];
                }
                break;

            case 'download-pass':
                $page = 'download-pass';
                if (isset($segments[1])) {
                    $_GET['booking_id'] = $segments[1];
                }
                break;

            case 'download-ical':
                $page = 'download-ical';
                if (isset($segments[1])) {
                    $_GET['booking_id'] = $segments[1];
                }
                break;

            case 'download-cert':
                $page = 'download-cert';
                if (isset($segments[1])) {
                    $_GET['cert_id'] = $segments[1];
                }
                break;

            case 'verify-cert':
                $page = 'verify-cert';
                break;

            case 'dashboard':
                $page = 'dashboard';
                break;

            case 'login':
                $page = 'login';
                break;

            case 'register':
                $page = 'register';
                break;

            case 'forgot-password':
                $page = 'forgot-password';
                break;

            case 'logout':
                $page = 'logout';
                break;

            case 'admin':
                $sub = strtolower($segments[1] ?? 'dashboard');
                if ($sub === 'dashboard' || $sub === '') {
                    $page = 'admin-dashboard';
                } elseif ($sub === 'events') {
                    if (($segments[2] ?? '') === 'create') {
                        $page = 'admin-event-form';
                    } else {
                        $page = 'admin-events';
                    }
                } elseif ($sub === 'event-form') {
                    $page = 'admin-event-form';
                    if (isset($segments[2])) {
                        $_GET['id'] = $segments[2];
                    }
                } elseif ($sub === 'banners') {
                    $page = 'admin-banners';
                } elseif ($sub === 'speakers') {
                    $page = 'admin-speakers';
                } elseif ($sub === 'users') {
                    $page = 'admin-users';
                } elseif ($sub === 'attendance') {
                    $page = 'admin-attendance';
                    if (isset($segments[2])) {
                        $_GET['event_id'] = $segments[2];
                    }
                } elseif ($sub === 'reports') {
                    $page = 'admin-reports';
                } elseif ($sub === 'settings') {
                    $page = 'admin-settings';
                } elseif ($sub === 'reset') {
                    $page = 'admin-reset';
                } else {
                    $page = 'admin-dashboard';
                }
                break;

            default:
                $page = 'home';
                break;
        }
    }
}

if (!$page) {
    $page = 'home';
}


try {
    switch ($page) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'events':
        (new EventController())->list();
        break;

    case 'event-detail':
        (new EventController())->detail();
        break;

    case 'past-event':
        (new EventController())->pastDetail();
        break;

    case 'checkout':
        (new BookingController())->checkout();
        break;

    case 'book-process':
        (new BookingController())->process();
        break;

    case 'booking-success':
        (new BookingController())->success();
        break;

    case 'download-pass':
        (new BookingController())->downloadPass();
        break;

    case 'download-ical':
        (new BookingController())->downloadIcal();
        break;

    case 'download-cert':
        (new CertificateController())->download();
        break;

    case 'verify-cert':
        (new CertificateController())->verify();
        break;

    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'login':
        (new AuthController())->login();
        break;

    case 'register':
        (new AuthController())->register();
        break;

    case 'forgot-password':
        (new AuthController())->forgotPassword();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // Admin Routes
    case 'admin-dashboard':
        (new AdminController())->dashboard();
        break;

    case 'admin-events':
        (new AdminController())->events();
        break;

    case 'admin-event-form':
        (new AdminController())->eventForm();
        break;

    case 'admin-banners':
        (new AdminController())->banners();
        break;

    case 'admin-speakers':
        (new AdminController())->speakers();
        break;

    case 'admin-users':
        (new AdminController())->users();
        break;

    case 'admin-attendance':
        (new AdminController())->attendance();
        break;

    case 'admin-reports':
        (new AdminController())->reports();
        break;

    case 'admin-settings':
        (new AdminController())->settings();
        break;

    case 'admin-reset':
        (new AdminController())->reset();
        break;

    default:
        (new HomeController())->index();
        break;
    }
} catch (Throwable $e) {
    echo '<div style="padding:20px;background:#fff;color:#c00;font-family:monospace;border:2px solid #e00;margin:20px;">'
       . '<h2>Application Exception</h2>'
       . '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>'
       . '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' on line ' . $e->getLine() . '</p>'
       . '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>'
       . '</div>';
}
