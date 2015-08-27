CREATE SCHEMA DB_1;
SET search_path = DB_1;

CREATE TABLE User_Type (
        userType serial PRIMARY KEY
);

CREATE TABLE Product (
        productID serial PRIMARY KEY,
        productName varchar(20) NOT NULL,
        productDesc varchar(150) NOT NULL,
        price float NOT NULL,
        category varchar(20) NOT NULL,
        quantity integer NOT NULL,
        endOfSale date

);

CREATE TABLE Customer (
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
        phoneNumber varchar(12) NOT NULL,
        member boolean NOT NULL,
        --Serial referencing
        userType integer REFERENCES User_Type
);

CREATE TABLE Purchase_Order (
	orderID serial PRIMARY KEY,
	cID integer REFERENCES Customer NOT NULL,
	dateOfPurchase date NOT NULL, 
	-- Serial referencing
	productID integer REFERENCES Product NOT NULL,
	orderComplete boolean NOT NULL,
	price float NOT NULL
);

CREATE TABLE Admin (
	--Serial referencing
	userType integer REFERENCES User_Type,
	id varchar(15) NOT NULL,
	pass varchar(15) NOT NULL,
	admin_email varchar(50) NOT NULL
);

CREATE TABLE Roster (
	playerID serial PRIMARY KEY,
	gamertag varchar(20),
	cID integer REFERENCES Customer NOT NULL
);


