DELIMITER $

SET storage_engine=InnoDB$

CREATE TABLE IF NOT EXISTS Person
(
   id     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
   fname  VARCHAR(20),
   lname  VARCHAR(20),
   pets   INT
)$

INSERT INTO Person (fname, lname, pets)
VALUES("Adam", "Ziegler", 2),
("Barbara", "Young", 0),
("Carl", "Xavier", 1),
("Dorothy", "Wondra", 3),
("Earl", "Vaugn", 1),
("Frieda", "Ulrich", 3),
("George", "Thomas", 0),
("Hilda", "Smith", 1),
("Isaac", "Rogers", 0),
("Josephine", "Quentin", 2),
("Kevin", "Porter", 1),
("Linda", "Olson", 1),
("Michael", "Nelson", 1),
("Natasha", "Miller", 2),
("Oliver", "Lewis", 2),
("Penelope", "King", 1),
("Quentin", "Jones", 0),
("Rosalie", "Irving", 1),
("Samuel", "Hightower", 0),
("Theresa", "Gardner", 2),
("Ulysses", "Ferguson", 1),
("Victoria", "Erickson", 5),
("William", "Davis", 1),
("Xena", "Campbell", 0),
("Yuri", "Brown", 2),
("Zelda", "Anderson", 0) $

DROP PROCEDURE IF EXISTS Get_By_Pets $
CREATE PROCEDURE Get_By_Pets(numpets INT)
BEGIN
   SELECT id, fname, lname, pets
     FROM Person
    WHERE pets = numpets;
END $

DROP PROCEDURE IF EXISTS Get_By_First_Name $
CREATE PROCEDURE Get_By_First_Name(first_name VARCHAR(20))
BEGIN
   SELECT id, fname, lname, pets
     FROM Person
    WHERE fname = first_name; 
END $

DELIMITER ;
