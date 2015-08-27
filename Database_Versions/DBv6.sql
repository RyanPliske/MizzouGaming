--v2 Update Begin
DROP SCHEMA IF EXISTS DB_2 CASCADE;
--v2 Update End
CREATE SCHEMA DB_2;
SET search_path = DB_2;

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
		orig_quantity integer DEFAULT NULL
);
--Customers are created when they register for a login name and pass
--Edit v6: Added Admin properties to customer table
CREATE TABLE customer (
        cID serial PRIMARY KEY,
		username varchar(16) NOT NULL,
		salt char(40) NOT NULL,
		hashpass char(40) NOT NULL,
        lastName varchar(15) NOT NULL,
        firstName varchar(15) NOT NULL,
        user_email varchar(50) NOT NULL,
		userType varchar(8) NOT NULL CHECK (userType='admin' OR userType='customer'),
        street varchar(40),
        city varchar(30),
        state varchar(2),
        zip integer,
        phoneNumber varchar(12)
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

--Edit v6 REMOVED Admin Table
--Edit v4 and v5
CREATE TABLE roster (
	roster_id serial PRIMARY KEY,
	tournament varchar(80) NOT NULL,
	team_captain_username varchar(16) NOT NULL,
	player1 varchar(25) DEFAULT NULL,
	player2 varchar(25) DEFAULT NULL,
	player3 varchar(25) DEFAULT NULL,
	player4 varchar(25) DEFAULT NULL,
	team_name varchar(30) DEFAULT NULL,
	wins integer DEFAULT 0,
	seed integer
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
INSERT INTO customer VALUES(default,'admin','ace3ae900d8023666b0731feb40d76cc632354c7', 'ee9b8d57358e8dbb34384abfff9c19eee8bde49b', 'Ryan', 'Pliske', 'mizzougaming@yahoo.com', 'admin');
--Set Store Items
INSERT INTO product VALUES(default,'Mizzou Xbox Controller', 'Envy Controllers presents a Mizzou black and gold xbox 360 Controller', '50', 'Merchandise', '40', '2014-08-16', 'img/controller.jpg', DEFAULT, 40);
INSERT INTO product VALUES(default,'Mizzou Gaming T-Shirt', 'Custom T-Shirt with Gamertag on the back',											 '15', 'Merchandise', '40', '2014-08-16', 'img/shirt1.jpg', 			DEFAULT, 40);
INSERT INTO product VALUES(default,'Halo Reach FFA Pass', 'FFA Halo Reach Tournament', 																'20', 'Tournament', '32', '2014-08-16', 'img/Tourny1.jpg', 			1, 16);
INSERT INTO product VALUES(default,'Black Ops II 4v4 Team Pass', '4v4 Black Ops Tournament',														'20', 'Tournament', '32', '2014-08-16', 'img/Tourny2.jpg', 			4, 32);
INSERT INTO product VALUES(default, 'StarCraft II Tournament Pass', '1v1 StarCraft II Tournament',														'20', 'Tournament', '16', '2014-08-16', 'img/Tourny4.jpg', 			1, 16);
INSERT INTO product VALUES(default,'Halo Reach 2v2 Team Pass', '2v2 Halo Reach Tournament', 														'40', 'Tournament', '16', '2014-08-16', 'img/Tourny1.jpg',			 2, 16);

--v5 Edit

--8 Team Halo 2v2 
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'Tsquared', 'Tsquared', 'Snip3down', '', '', 'Str8 Rippin');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'Shockwave', 'Shockwave', 'Karma', '', '', 'Carbon');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'SK', 'SK', 'Hysteria', '', '', 'Triggers Down');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'Roy ', 'Roy', 'Lunchbox', '', '', 'Instinct');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'Soldier187', 'Soldier187', 'Ant', '', '', 'Team Classic');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'Cloud ', 'Cloud ', 'Pistola', '', '', 'Ambush');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', '|x Ace x|', '|x Ace x|', 'Elamite Warrior', '', '', 'Shady Halo Kids');
INSERT INTO roster VALUES(default, 'Halo Reach 2v2', 'Ogre 1', 'Ogre 1', 'Ogre 2', '', '', 'Final Boss');

INSERT INTO roster VALUES(default, 'Halo Reach 4v4', 'Tsquared', 'Tsquared', 'Snip3down', 'Elamite Warrior', 'Neighbor', 'Str8 Rippin');
INSERT INTO roster VALUES(default, 'Halo Reach 4v4', 'Shockwave', 'Shockwave', 'Karma', 'GH057ayame', 'Gandhi', 'Carbon');
INSERT INTO roster VALUES(default, 'Halo Reach 4v4', 'Roy ', 'Roy', 'Lunchbox', 'FeatItself', 'Hokum', '5k');
INSERT INTO roster VALUES(default, 'Halo Reach 4v4', 'Cloud ', 'Cloud ', 'Pistola', 'Heinz', 'Nexus', 'Ambush');
INSERT INTO roster VALUES(default, 'Halo Reach 4v4', '|x Ace x|', '|x Ace x|', 'Elamite Warrior', 'lBestman', 'Hysteria', 'Shady Halo Kids');
INSERT INTO roster VALUES(default, 'Halo Reach 4v4', 'Ogre 1', 'Ogre 1', 'Ogre 2', 'Walshy', 'Saiyan', 'Final Boss');

--7 Player Starcraft
INSERT INTO roster VALUES(default, 'StarCraft II', 'TheLittleOne', 'TheLittleOne', '', '', '', 'TeamLiquid');
INSERT INTO roster VALUES(default, 'StarCraft II', 'MaDFroG', 'MaDFroG', '', '', '', 'SK Gaming');
INSERT INTO roster VALUES(default, 'StarCraft II', 'SjoW', 'SjoW', '', '', '', 'Team Dignitas');
INSERT INTO roster VALUES(default, 'StarCraft II', 'NaNiwa', 'NaNiwa', '', '', '', 'MeetYourMakers');
INSERT INTO roster VALUES(default, 'StarCraft II', 'SEn', 'SEn', '', '', '', 'fnatic');
INSERT INTO roster VALUES(default, 'StarCraft II', 'iNcontroL', 'iNcontroL', '', '', '', 'Evil Geniuses');
INSERT INTO roster VALUES(default, 'StarCraft II', 'BoxeR', 'BoxeR', '', '', '', 'SlayerS');

--6 Team Black Ops 4v4
INSERT INTO roster VALUES(default, 'Black Ops II 4v4', 'Karma', 'Karma', 'Crimsix', 'TeePee', 'Aches', 'Complexity');
INSERT INTO roster VALUES(default, 'Black Ops II 4v4', 'MBOZE', 'MBOZE', 'Ricky', 'Mirx', 'Killa', 'Optic Nation');
INSERT INTO roster VALUES(default, 'Black Ops II 4v4', 'Formal', 'Formal', 'Censor', 'Dedo', 'Saints', 'Faze Black');
INSERT INTO roster VALUES(default, 'Black Ops II 4v4', 'Classic', 'Classic', 'Replays', 'JKAP', 'Theory', 'Faze Red');
INSERT INTO roster VALUES(default, 'Black Ops II 4v4', 'Sharp', 'Sharp', 'Goonjar', 'Apathy', 'Neslo', 'Team Kaliber');
INSERT INTO roster VALUES(default, 'Black Ops II 4v4', 'Nadeshot', 'Nadeshot', 'Scumpii', 'Clayster', 'Proofy', 'Optic Gaming');

--7 Player Smash
INSERT INTO roster VALUES(default, 'SSBM', 'Ken', 'Ken', '', '', '', 'East Coast' );
INSERT INTO roster VALUES(default, 'SSBM', 'KDJ', 'Korean DJ' );
INSERT INTO roster VALUES(default, 'SSBM', 'Isai', 'Isai', '', '', '', 'East Coast' );
INSERT INTO roster VALUES(default, 'SSBM', 'Mew2King', 'Mew2King' );
INSERT INTO roster VALUES(default, 'SSBM', 'ChillinDude829', 'ChillinDude829', '', '', '', 'West Coast' );
INSERT INTO roster VALUES(default, 'SSBM', 'Wes', 'Wes', '', '', '', 'Deadly Alliance' );
INSERT INTO roster VALUES(default, 'SSBM', 'Azen', 'Azen', '', '', '', 'H2YL' );
