# Project mysqli Test

When I attempted to migrate an old PHP application to the current
versions PHP, MariaDB (in place of MySQL on the May 5, 2021
version of Raspian.

The migration was not a success.  The mysqli interface does not
perform properly.  Specifically, the second procedure run using a
single connection fails.  I know the MariaDB works because I have a
C++ application using the MySQL/MariaDB C-API that worked with
little or no changes.  I am using the same basic strategy for
both the C++ and PHP versions.

This project will include MariaDB scripts to create and populate
a table and create a stored procedure to query the table, along
with a PHP script that invokes the stored procedure multiple times.
