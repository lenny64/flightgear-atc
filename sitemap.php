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

$urlset = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
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
$latest_event_edited_id = $Event->getLatestEventEdited();
$LatestEvent = new Event();
$LatestEvent->selectById($latest_event_edited_id);

function generateNewUrl($urlset, $loc, $changefreq, $lastmod, $priority) {
    $url = $urlset->addChild('url');
    $url->addChild('loc','http://flightgear-atc.alwaysdata.net'.$loc);
    $url->addChild('changefreq',$changefreq);
    $url->addChild('lastmod',$lastmod);
    $url->addChild('priority',$priority);
}

foreach ($pages_list as $page) {
    // $url = $urlset->addChild('url');

    // TWO EXCEPTIONS : index and show_event
    if ($page == "/" || $page == "/index.php") {
        generateNewUrl($urlset, $page, 'hourly', date('Y-m-d\TH:i:s+00:00',strtotime($LatestEvent->datetime)), '1.0');
        for ($i = 1; $i <= 4; $i++) {
            $i_4_days = $i*4;
            $calculated_date = date('Y-m-d', strtotime($today." +".$i_4_days." days"));
            $loc = $page.'?dateBegin='.$calculated_date;
            generateNewUrl($urlset, $loc, 'hourly', date('Y-m-d\TH:i:s+00:00',strtotime($LatestEvent->datetime)), '0.8');
        }
    }
    else if ($page == "/show_event.php") {
        foreach ($events_list as $event_id) {
            $Event->selectById($event_id);
            $lastmod = date('Y-m-d\TH:i:s+00:00', strtotime($Event->datetime));
            generateNewUrl($urlset, $page.'?eventId='.$event_id, 'hourly', $lastmod, '0.7');
        }
    }
    else { // OTHERWISE
        $lastmod = date('Y-m-d\TH:i:s+00:00', filemtime('.'.$page));
        generateNewUrl($urlset, $page, 'weekly', $lastmod, '0.5');
    }

}



header('Content-type: text/xml');
print($urlset->asXML());

?>
