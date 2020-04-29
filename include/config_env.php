<?php


$current_file = $_SERVER['SCRIPT_FILENAME'];
$called_file = pathinfo($current_file);
$called_file_name = $called_file['basename'];

// We initialize the page title
$PAGE_TITLE = "Flightgear Air Traffic Control Events";
$PAGE_CANONICAL = "";
$array_pages = Array("index.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - Home page",
        'description' => "Find the next Flightgear ATC multiplayer event occuring on your favorite airport for more realism.",
        'canonical' => "http://flightgear-atc.alwaysdata.net"
    ),
    "new_event.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - Create a new event",
        'description' => "Create a new Flightgear Air Traffic Control event and play ATC online.",
        'canonical' => "http://flightgear-atc.alwaysdata.net/new_event.php"
    ),
    "edit_event.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - Edit an event",
        'description' => 'Edit a Flightgear ATC event',
        'canonical' => "http://flightgear-atc.alwaysdata.net/edit_event.php"
    ),
    "school.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - School",
        'description' => "Get ready to fly with the best ATCs in Flightgear with the help of didactic flight plan suggestions."
    ),
    "controlled_area.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - ATC multiplayer controlled area",
        'description' => "List of ATC being part of the Flightgear multiplayer world and helping you to fly under controlled area."
    ),
    "downloads.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - Downloads",
        'description' => "Download the latest and best Flightgear ATC resources to create your own ATC events on multiplayer."
    ),
    "contact.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - Contact",
        'description' => "Contact page if you wish to help us making better Flightgear ATC events."
    ),
    "faq.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - FAQ",
        'description' => "Find some questions and answers regarding your experience of Flightgear ATC events."
    ),
    "api.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - API",
        'description' => "Here are some resources if you want to integrate flightgear-atc in your applications. Collect ATC events, flight plans etc..."
    ),
    "dashboard.php" => Array(
        'title' => "Flightgear Air Traffic Control Events - ATC dashboard",
        'description' => "Flightgear ATC dashboard for ATC administration part."
    )
);
if (isset($called_file_name)) {
    if ($called_file_name != NULL && $called_file_name != '') {
        if ( isset($array_pages[$called_file_name])) {
            $PAGE_TITLE = $array_pages[$called_file_name]['title'];
            $PAGE_DESCRIPTION = $array_pages[$called_file_name]['description'];
            $PAGE_NAME = $called_file_name;
            if (isset($array_pages[$called_file_name]['canonical'])) {
                $PAGE_CANONICAL = $array_pages[$called_file_name]['canonical'];
            }
        }
    }
}

?>
