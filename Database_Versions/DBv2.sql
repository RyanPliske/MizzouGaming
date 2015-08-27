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
		imgPath varchar(40) 
);

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

CREATE TABLE roster (
	playerID serial PRIMARY KEY,
	gamertag varchar(20),
	cID integer REFERENCES Customer NOT NULL
);

--Create one Admin User with database
INSERT INTO admin VALUES(default,'admin','ace3ae900d8023666b0731feb40d76cc632354c7', 'ee9b8d57358e8dbb34384abfff9c19eee8bde49b', 'mizzougaming@yahoo.com');
--Set Store Items
INSERT INTO product VALUES(default,'Mizzou Xbox Controller', 'Envy Controllers presents a Mizzou black and gold xbox 360 Controller', '50', 'Merchandise', '5', '2014-08-16', 'img/controller.jpg');
INSERT INTO product VALUES(default,'Mizzou Gaming T-Shirt', 'Custom T-Shirt with Gamertag on the back',											 '15', 'Merhandise', '10', '2014-08-16', 'img/shirt1.jpg');
INSERT INTO product VALUES(default,'MGC Halo Reach FFA', 'FFA Halo Reach Tournament', 												'20', 'Tournament', '32', '2014-08-16', 'img/Tourny1.jpg');
INSERT INTO product VALUES(default,'4v4 Black Ops II', '4v4 Black Ops Tournament',																				'20', 'Tournament', '16', '2014-08-16', 'img/Tourny2.jpg');
INSERT INTO product VALUES(default, 'StarCraft II Tournament', '1v1 StarCraft II Tournament',																						'20', 'Tournament', '16', '2014-08-16', 'img/Tourny4.jpg', FALSE);

