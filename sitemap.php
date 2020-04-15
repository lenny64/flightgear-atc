<?php
require('./include/config.php');

// Let's open the DB
$db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);

require('./include/classes.php');
require('./include/functions.php');

date_default_timezone_set('UTC');

$xml = new SimpleXMLElement('<xml/>');
$xml->addAttribute('version', '1.0');
$xml->addAttribute('encoding','UTF-8');

$urlset = $xml->addChild('urlset');
$urlset->addAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$urlset->addAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

$pages_list = Array(
    '/',
    '/index.php',
    '/show_event.php',
    '/new_event.php',
    '/school.php',
    '/controlled_area.php',
    '/downloads.php',
    '/contact.php',
    '/faq.php',
    '/api.php',
    '/dashboard.php',
    '/information.php'
);

$today = date('Y-m-d');
$today_plus_21_days = date('Y-m-d', strtotime($today." + 21 days"));
$Event = new Event();
$events_list = $Event->getATCSessions($today,$today_plus_21_days);

foreach ($pages_list as $page) {
    $url = $urlset->addChild('url');

    // TWO EXCEPTIONS : index and show_event
    if ($page == "/" || $page == "/index.php") {
        $url->addChild('loc','http://flightgear-atc.alwaysdata.net'.$page);
        $url->addChild('changefreq','hourly');
        $lastmod = date('Y-m-d\TH:i:s+00:00', filemtime('.'.$page));
        $url->addChild('lastmod',$lastmod);
        $url->addChild('priority','1.0');
    }
    else if ($page == "/show_event.php") {
        foreach ($events_list as $event_id) {
            $Event->selectById($event_id);
            $url->addChild('loc','http://flightgear-atc.alwaysdata.net'.$page.'?eventId='.$event_id);
            $url->addChild('changefreq','hourly');
            $lastmod = date('Y-m-d\TH:i:s+00:00', strtotime($Event->datetime));
            $url->addChild('lastmod',$lastmod);
            $url->addChild('priority','0.7');
        }
    }
    else { // OTHERWISE
        $url->addChild('loc','http://flightgear-atc.alwaysdata.net'.$page);
        $url->addChild('changefreq','weekly');
        $lastmod = date('Y-m-d\TH:i:s+00:00', filemtime('.'.$page));
        $url->addChild('lastmod',$lastmod);
        $url->addChild('priority','0.5');
    }

}



header('Content-type: text/xml');
print($xml->asXML());

?>
