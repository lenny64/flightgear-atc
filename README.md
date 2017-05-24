flightgear-atc
==============

Flightgear ATC events to coordinate ATC operations



BRANCH ORGANISATION
===================

> bug/*
Bug fixes and feature improvements

> feat/*
New features



CHANGES CHRONOLOGY
==================

Thibault 2014 06 17 20:04 - branch "bug/v4"
Main changes :
- V4/form_newEvent.php : line 193 if the user is connected it will gather the information of HIS previous event (and not anyone else's)
- V4/file_flightplan_v3.php : line 151 adding a picture to visually enhance the style

Thibault 2014 06 22 10:10 - merging branch "bug/v4" to "master"
Main changes above
If other V4 changes are made, they should be on this branch "bug/v4"

Thibault 2014 06 22 15:47 - branch "master"
Main changes :
- Making the V4 the "official" version
- Creation of a "V3" directory that stores the previous version

Thibault 2014 07 08 20:31 - branch "bug/v4"
Main change :
- include/log.php : removing the message to invite rolling back to V3 version

Thibault 2014 07 09 19:15 - branch "feat/atc-name"
Main changes :
- ATCs can now set a name in their Dashboard, which will appear on their ATC events
- include/classes.php : class User -> creating "connect" and "disconnect" functions
- Relooking the dashboard login page

Thibault 2017 04 28 22:20 - branch "master"
Main changes :
- Changing all "php5" extension to "php" according alwaysdata migration
