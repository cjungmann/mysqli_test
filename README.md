# Project mysqli Test

A database connection, once established, should remain viable
through several interactions.  This expectation appears too
optimistic when I upgraded from an old tech stack including
PHP and MySQL to recent versions of the stack that uses MariaDB
in place of MySQL.

This old web application builds documents with multiple calls
to stored procedures using **mysqli** prepared statements.  This
project supports my posting on this topic on
[Stack Exchange](www.stackexchange.com).

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
then calls *./setup*.  In the unlikely event that someone already
has a *mysqli_test* database and user, it will be necessary to
manually create these items and change the *mysqli_test.php* to
reflect the alternate names.

### Run the Test

When setup is complete, run the test with:

~~~sh
php mysqli_test.php
~~~

## Analysis

The problem revolves around a failure to return a database
connection to a usable state after using a prepared statement to
run a stored procedure.  After the first prepared statement is
done, an attempt to prepare a second statment fails with a
*Packets out of order** error.  

I also encountered this problem while developing the original
web application.  It turned out that upon completion of a stored
procedure, an extra empty result is generated that must be
disposed of before the connection can be used again.

I succeeded in solving this problem in PHP using mysqli to
interface with MySQL and in another project that uses the C API.

MariaDB is the new default database for a (W|L)AMP stack.  It
is advertised as a drop-in replacement for MySQL.  With respect
to the C API, outside of one compatibility issue involving
uninitialized fields in the MYSQL_FIELD structure, the drop-in
promise has been fulfilled.

My C API code was modelled after the older PHP/mysqli code,
which no longer works.  The call to `mysqli_stmt_next_result()`
does not return the expected trailing result after executing
a stored procedure.  Subsequently, the next `mysqli_prepare()`
fails with the dread *Packets out of order* message.
