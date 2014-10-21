sports_league
=============

Software to manage a simple sports league with news items, file/information downloads, contact list management, schedule display and standings.

This project contains code that works under CodeIgniter to provide a simple sports league website. The code currently provides functionality that: - creates and uses accounts to provide minimum 2 levels of access to site's information; - main page has an announcement system that can display messages in a specified order and for a fixed time period; messages, upon retirement, can be accessible from an archive section - information page allows for listing of various files that can be downloaded or links to other related sites - contact information for teams provides for up to 3 contacts per team and can be managed by the team's with little intervention from the website administrator; - league schedules can be loaded from a CSV and then displayed by team, division or for the whole league - game results are submitted by team contacts with email confirmation sent to both teams; - league standings generated - schedules for regular season and playoffs (both round-robin and double elimination) can be managed; for double elimination, as teams win, schedule automatically will update showing teams their next game

This project is a work in progress but has been used by 2 leagues I participate in -- one for the past 2 years and one for a year. The leagues are different enough so I'm now working on trying to design the software to be more flexible in managing league differences so that the software can be used in different ways.

Note, it is never my intention to write software to generate schedules (there are too many suitable products out there) but rather to be flexible in importing schedules into the system.

Software requirements to use this project:

Web server (Apache is known to work; I haven't had experience with others but don't believe problems should be insurmountable)
Database (MySQL known to work)
CodeIgniter (has run on versions through 2.1)
jQuery
My intention is to provide software suitable for small to mid-size leagues to be able to have a functional website that can be run with minimal administration (other than start of season). I have a long list of functionality I wish to add and I also wish to continue to incorporate suitable web technologies to learn and to improve the site.
