Installation
------------
1. install packages for languages: python3, java, gcc, etc. (see test_attempts.php)
2. install the following packages: php sqlite3 php-sqlite3
3. copy static documentation to ./docs
4. create database by running: sqlite3 contest.db < create.sql

Startup
-------
1. start server by running: php -S 0.0.0.0:8000 -t html
2. open browser to localhost:8000

Add Contest
-----------
1. add database record using http://localhost:8000/edit_contests.php
2. create directory under contests with name specified above 
3. copy problems (*.html), test files (*.in, *.out), and rules.html to new directory 

Usage
-----
* initial judge account: admin/secret
* judge can edit: contests, contestants, problems, and notes via simple forms
* anyone can view standings
* contestants have limited rights
* questions and print requests supported via submissions (see submit_attempt.php)
* support for print requests via view_prints.php