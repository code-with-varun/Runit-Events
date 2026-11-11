<?php
namespace App\Controllers;

use App\Models\CMSBanner;
use App\Models\Event;
use App\Database;

class HomeController {
    public function index() {
        $banners = CMSBanner::getActiveBanners();
        $upcomingEvents = Event::getUpcoming(6);
        $trendingEvents = Event::getTrending(4);
        $pastEvents = Event::getPast(3);
        $categories = Database::fetchAll("SELECT * FROM event_categories ORDER BY id ASC");
        
        $stats = [
            'total_learners' => '12,500+',
            'events_hosted' => '180+',
            'certificates_issued' => '8,400+',
            'partner_companies' => '95+'
        ];

        require __DIR__ . '/../../views/home/index.php';
    }
}
