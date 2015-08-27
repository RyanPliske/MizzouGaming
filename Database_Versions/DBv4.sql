--v2 Update Begin
DROP SCHEMA IF EXISTS DB_1 CASCADE;
--v2 Update End
CREATE SCHEMA DB_1;
SET search_path = DB_1;

CREATE TABLE user_type (
        userType serial PRIMARY KEY
);

CREATE TABLE product (
        productID serial PRIMARY KEY,
        productName varchar(80) NOT NULL,
        productDesc varchar(400) NOT NULL,
        price float NOT NULL,
        category varchar(20) NOT NULL,
        quantity integer NOT NULL,
        endOfSale date,
		--Edit v2: Picture path so that we can store images when uploaded by Admin 
		imgPath varchar(40) ,
		--Edit v3: 1v1, 2v2 tournaments
		team_members integer DEFAULT NULL,
		orig_quantity integer DEFAULT NULL,
);
--Customers are created when they register for a login name and pass
CREATE TABLE customer (
        cID serial PRIMARY KEY,
		username varchar(16) NOT NULL,
		salt char(40) NOT NULL,
		hashpass char(40) NOT NULL,
        lastName varchar(15) NOT NULL,
        firstName varchar(15) NOT NULL,
        user_email varchar(50) NOT NULL,
        street varchar(40) NOT NULL,
        city varchar(30) NOT NULL,
        state varchar(2) NOT NULL,
        zip integer NOT NULL,
        phoneNumber varchar(12) NOT NULL
        --Serial referencing
        --userType integer REFERENCES User_Type
);

CREATE TABLE purchase_order (
	orderID serial PRIMARY KEY,
	cID integer REFERENCES Customer NOT NULL,
	dateOfPurchase date NOT NULL, 
	-- Serial referencing
	productID integer REFERENCES Product NOT NULL,
	orderComplete boolean NOT NULL,
	price float NOT NULL
);

CREATE TABLE admin (
	--Serial referencing
	--userType integer REFERENCES User_Type,
	id serial PRIMARY KEY,
	username varchar(16) NOT NULL,
	salt char(40) NOT NULL,
	hashpass char(40) NOT NULL,
	admin_email varchar(50) NOT NULL
);
--Edit v4: through error checking, fields were eliminated and also added
CREATE TABLE roster (
	roster_id serial PRIMARY KEY,
	tournament varchar(80) NOT NULL,
	team_captain_username varchar(16) NOT NULL,
	player1 varchar(25) DEFAULT NULL,
	player2 varchar(25) DEFAULT NULL,
	player3 varchar(25) DEFAULT NULL,
	player4 varchar(25) DEFAULT NULL,
	team_name varchar(30) DEFAULT NULL
);
--People should be able to register for multiple tournaments as a free agent cuz ppl don't have friends.
CREATE TABLE free_agents(
	agentID serial PRIMARY KEY,
	cID integer,
	gamertag varchar(17),
	username varchar(17),
	contact varchar(26),
	tournament varchar(40),
	description varchar(40),
	seed integer
);
--Create one Admin User with database
INSERT INTO admin VALUES(default,'admin','ace3ae900d8023666b0731feb40d76cc632354c7', 'ee9b8d57358e8dbb34384abfff9c19eee8bde49b', 'mizzougaming@yahoo.com');
--Set Store Items
INSERT INTO product VALUES(default,'Mizzou Xbox Controller', 'Envy Controllers presents a Mizzou black and gold xbox 360 Controller', '50', 'Merchandise', '40', '2014-08-16', 'img/controller.jpg', DEFAULT, 40);
INSERT INTO product VALUES(default,'Mizzou Gaming T-Shirt', 'Custom T-Shirt with Gamertag on the back',											 '15', 'Merchandise', '40', '2014-08-16', 'img/shirt1.jpg', 			DEFAULT, 40);
INSERT INTO product VALUES(default,'Halo Reach FFA Pass', 'FFA Halo Reach Tournament', 																'20', 'Tournament', '32', '2014-08-16', 'img/Tourny1.jpg', 			1, 16);
INSERT INTO product VALUES(default,'Black Ops II 4v4 Team Pass', '4v4 Black Ops Tournament',														'20', 'Tournament', '32', '2014-08-16', 'img/Tourny2.jpg', 			4, 32);
INSERT INTO product VALUES(default, 'StarCraft II Tournament Pass', '1v1 StarCraft II Tournament',														'20', 'Tournament', '16', '2014-08-16', 'img/Tourny4.jpg', 			1, 16);
INSERT INTO product VALUES(default,'Halo Reach 2v2 Team Pass', '2v2 Halo Reach Tournament', 														'40', 'Tournament', '16', '2014-08-16', 'img/Tourny1.jpg',			 2, 16);






