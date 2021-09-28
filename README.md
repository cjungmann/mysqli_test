# Project mysqli Test

A database connection, once established, should remain viable
through several interactions.  This expectation appears too
optimistic when I upgraded from an old tech stack including
PHP and MySQL to recent versions of the stack that uses MariaDB
in place of MySQL.

During a port of an old web application to the new stack,  I
found that preparing a new statement after running a stored
procedure fails in a familiar but unexpected way, resulting
in a *Packets out or order* error.  This project supports a
[Stack Exchange question][1] with additional discussion and 
scripts to make it very easy to duplicate the problem on
another computer.

## Usage

The code fragment found in the Stack Exchange question may
be sufficient for comment, in which case this project is
overkill.  However, I am providing this as a simple means for
someone to generate the error on their computer.

This project includes:

- **An SQL script** (*tables_and_procs.sql*) that creates and
  populates a table, two stored procedures, one returning a
  single row, the other multiple rows.

- **A PHP script** (*mysqli_test.php*), intended for CLI use,
  that includes two functions that each use *mysqli* to prepare
  a statement calling one of the stored procedures.  The second
  call of one of these functions results in the *Packets out of
  order* error.

- **A BASH script** (*setup*) whose optional use will:
  1. Request a MySQL/MariaDB the name and password for a
     user authorized to add a database and a user (typically
     this will be *root*).
  1. create a database named **mysqli_test** into which the SQL
     script will be loaded
  1. create a new user, **mysqli_test** with no password, and
     granted EXECUTE and SELECT privileges

### Installation

Ideally one simply clones the project, enters the new directory,
then calls *setup*:

~~~sh
user:~/mysqli_test$ ./setup
~~~

In the unlikely event that someone already
has a *mysqli_test* database and user, it will be necessary to
manually use or create an alternate database and user, load the
SQL script, and update *mysqli_test.php* to reflect the changes.

### Run the Test

When setup is complete, run the test with:

~~~sh
user:~/mysqli_test$ php mysqli_test.php
~~~

## Analysis

With MariaDB replacing MySQL as the default database in a
LAMP web server stack, it is important to ensure that old code
runs in that environment.

My problem revolves around a failure to return a database
connection to a usable state after using a prepared statement
to run a stored procedure.  After the first prepared statement
is done, an attempt to prepare a second statement produces a
*Packets out of order* error.  

I encountered and solved this problem while developing the
original web application.  It turned out that upon completion of
a stored procedure, the database engine generates an extra empty
result that must be disposed of before the connection can be used
again.  Failure to handle the extra result leads to the *Packets
out of order* error.

### C API: a New Persepctive

I had further developed ideas using stored procedures to build
pages with a C++ project using MySQL's C API.

While porting the C++ project to use MariaDB, I found only one
compatibility problem involving uninitialized fields in the
MYSQL_FIELD structure.  Aside from that one problem, MariaDB has
fulfilled its promise to be a drop-in replacement for MySQL.

Having similar solutions in two environments has been useful
in investigating the PHP/mysqli problem.  I could rewrite some
of the PHP/mysqli code to exactly match what I had proved was
working in the C API.  When the PHP/mysqli persistently fails
to run, I can see which functions are likely culprits.

### Blame

Since the C API of MariaDB works and the PHP *mysqli* code
fails, I strongly suspect the failure is in the implementation
of the *mysqli* wrapper functions.

The PHP call to `mysqli_stmt_next_result()` does not return
the expected trailing result after executing a stored procedure.
Subsequently, the next `mysqli_prepare()` fails with the dread
*Packets out of order* message.

[1]: https://stackoverflow.com/questions/69368867/what-is-the-best-practice-for-calling-stored-procedures-using-the-php-mysqli-int "so_post"
