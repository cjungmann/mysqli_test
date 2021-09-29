<?php

$host = "localhost";
$user = "mysqli_test";
$password = "";
$database = "mysqli_test";

function get_connection()
{
   GLOBAL $host, $user, $password, $database;
   $connection = new mysqli($host, $user, $password, $database);
   if (! $connection || mysqli_connect_errno() )
   {
      printf("Connection failure: %s.\n", mysqli_connect_error());
   }

   return $connection;
}

/**
 * Minimum viable function: serving to isolate necessary steps.
 * This code is used for the StackExchange posting.
 *
 * The procedure called by this function returns either one
 * or zero records.
 */
function get_person($connection, $first_name)
{
   $query = "CALL Get_By_First_Name(?)";
   if (($stmt = $connection->prepare($query)))
   {
      $stmt->bind_param('s', $first_name);
      if ($stmt->execute())
      {
         $stmt->store_result();
         $stmt->bind_result($id, $fname, $lname, $pets);

         while($stmt->fetch())
            printf("%3d %20s %20s %2d.\n", $id, $fname, $lname, $pets);

         $stmt->free_result();
         while ($stmt->next_result())
         {
            $stmt->store_result();
            while ($stmt->fetch())
               ;
            $stmt->free_result();
         }
      }

      $stmt->close();
   }
}

/**
 * This function includes more messaging to track the
 * progress through the code and to more explicitely report
 * errors.
 *
 * This function calls the procedure that returns zero, one, or
 * many records, and may be useful to confirm that multiple
 * fetches can be successful.
 */
function get_people_by_pet_count($connection, $number_of_pets)
{
   $query = "CALL Get_By_Pets(?)";
   if (($stmt = $connection->prepare($query)))
   {
      $stmt->bind_param('i', $number_of_pets);
      if ($stmt->execute())
      {
         $stmt->store_result();
         $stmt->bind_result($id, $fname, $lname, $pets);

         while($stmt->fetch())
            printf("%3d %20s %20s %2d.\n", $id, $fname, $lname, $pets);

         $stmt->free_result();
         $nr_exit = $connection->next_result();
         printf("nr_exit is '%s' (connect error = %s) (sqlstate = %s).\n",
                $nr_exit,
                $connection->connect_error,
                $connection->sqlstate);

         while ($nr_exit)
         {
            $stmt->store_result();
            while ($stmt->fetch())
               ;
            $stmt->free_result();

            $nr_exit = $connection->next_result();
         }
      }
      else
         printf("Error executing the statement: %s.\n", $connection->error);

      $stmt->close();
   }
   else
      printf("Error while preparing statement: %s.\n", $connection->error);
}


/**
 * Invoking the stored procedure-calling functions.  The first 
 * call always works, the second call always fails.  The failure
 * of the second call is likely a result of the failure to get
 * the trailing result in the first function call.
 */
if ($conn = get_connection())
{
   // get_person($conn, "Samuel");
   // get_person($conn, "Zelda");
   get_people_by_pet_count($conn, 3);
   get_people_by_pet_count($conn, 2);
}

?>
