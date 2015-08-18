# The Campus Judge

[The Campus Judge](https://github.com/shubham1559/The-Campus-Judge/) is a free and open source online judge for C, C++, Java and Python programming languages.
The Campus Judge is modified version of [Sharif Judge](https://github.com/mjnaderi/Sharif-Judge).

The web interface is written in PHP (CodeIgniter framework) and the main backend is written in BASH.

Python in The Campus Judge is not sandboxed yet. Just a low level of security is provided for python.
If you want to use The Campus Judge for python, USE IT AT YOUR OWN RISK or provide sandboxing yourself.

The full documentation is [here](https://github.com/shubham1559/The-Campus-Judge/tree/master/docs)

Download the latest release from [github](https://github.com/shubham1559/The-Campus-Judge/releases)


##New Features/Changes
* Problem comments/queries by students
* Updated Documentation on Web Interface
* Downloadable Solution/Editorial of Assignment
* Other users code can be made public
* Judges response can be viewed after submission(no refreshing needed)
* Final submission is automatically selected
* Scoreboard algorithm changed according to standard ACM ICPC rules
* To save time, judge will stop execution once a wrong answer found
* Team profiles now have details of members
* Nothing is visible to students before assignment begins
* Countdown timer also shows time to start with proper messages
* Settings page updated
* Other minor changes


## Features
  * Multiple user roles (admin, head instructor, instructor, student)
  * Sandboxing _(not yet for python)_
  * Cheat detection (similar codes detection) using [Moss](http://theory.stanford.edu/~aiken/moss/)
  * Custom rule for grading late submissions
  * Submission queue
  * Download results in excel file
  * Download submitted codes in zip file
  * _"Output Comparison"_ and _"Tester Code"_ methods for checking output correctness
  * Add multiple users
  * Problem Descriptions (PDF/Markdown/HTML)
  * Rejudge
  * Scoreboard
  * Notifications
  * ...

## Requirements

For running The Campus Judge, a Linux server with following requirements is needed:

* Webserver running PHP version 5.3 or later
* PHP CLI (PHP command line interface, i.e. `php` shell command)
You should be able to run `php` from command line. In Ubuntu you need to install `php5-cli` package.
* MySql database (with `mysqli` extension for PHP) or PostgreSql database
* PHP must have permission to run shell commands using [shell_exec](http://www.php.net/manual/en/function.shell-exec.php) function.
For example this command should run correctly:
```php
echo shell_exec("php -v");
```
* Tools for compiling and running submitted codes (`gcc`, `g++`, `javac`, `java`, `python2`, `python3` commands)
* It is better to have `perl` installed for more precise time and memory limit and imposing size limit on output of submitted code.
* It is better to have `Latex` and `Pandoc` installed to convert md file to pdf given in [Sample Assignment](docs/sample_assignment.md). 

## Installation

For Installation instructions [click here](docs/installation.md).

##Documentation

Check out The Campus Judge Documentation inside `docs` folder.
After installation you can read the docs on web interface too. 

## License

GPL v3
