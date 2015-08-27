--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: db_1; Type: SCHEMA; Schema: -; Owner: cs3380s14grp10
--

CREATE SCHEMA db_1;


ALTER SCHEMA db_1 OWNER TO cs3380s14grp10;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = db_1, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: customer; Type: TABLE; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

CREATE TABLE customer (
    cid integer NOT NULL,
    username character varying(16) NOT NULL,
    salt character(40) NOT NULL,
    hashpass character(40) NOT NULL,
    lastname character varying(15) NOT NULL,
    firstname character varying(15) NOT NULL,
    user_email character varying(50) NOT NULL,
    usertype character varying(8) NOT NULL,
    street character varying(40),
    city character varying(30),
    state character varying(2),
    zip integer,
    phonenumber character varying(12),
    CONSTRAINT customer_usertype_check CHECK ((((usertype)::text = 'admin'::text) OR ((usertype)::text = 'customer'::text)))
);


ALTER TABLE db_1.customer OWNER TO cs3380s14grp10;

--
-- Name: customer_cid_seq; Type: SEQUENCE; Schema: db_1; Owner: cs3380s14grp10
--

CREATE SEQUENCE customer_cid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE db_1.customer_cid_seq OWNER TO cs3380s14grp10;

--
-- Name: customer_cid_seq; Type: SEQUENCE OWNED BY; Schema: db_1; Owner: cs3380s14grp10
--

ALTER SEQUENCE customer_cid_seq OWNED BY customer.cid;


--
-- Name: free_agents; Type: TABLE; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

CREATE TABLE free_agents (
    agentid integer NOT NULL,
    cid integer,
    gamertag character varying(17),
    username character varying(17),
    contact character varying(26),
    tournament character varying(40),
    description character varying(40)
);


ALTER TABLE db_1.free_agents OWNER TO cs3380s14grp10;

--
-- Name: free_agents_agentid_seq; Type: SEQUENCE; Schema: db_1; Owner: cs3380s14grp10
--

CREATE SEQUENCE free_agents_agentid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE db_1.free_agents_agentid_seq OWNER TO cs3380s14grp10;

--
-- Name: free_agents_agentid_seq; Type: SEQUENCE OWNED BY; Schema: db_1; Owner: cs3380s14grp10
--

ALTER SEQUENCE free_agents_agentid_seq OWNED BY free_agents.agentid;


--
-- Name: product; Type: TABLE; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

CREATE TABLE product (
    productid integer NOT NULL,
    productname character varying(80) NOT NULL,
    productdesc character varying(400) NOT NULL,
    price double precision NOT NULL,
    category character varying(20) NOT NULL,
    quantity integer NOT NULL,
    endofsale date,
    imgpath character varying(40),
    team_members integer,
    orig_quantity integer
);


ALTER TABLE db_1.product OWNER TO cs3380s14grp10;

--
-- Name: product_productid_seq; Type: SEQUENCE; Schema: db_1; Owner: cs3380s14grp10
--

CREATE SEQUENCE product_productid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE db_1.product_productid_seq OWNER TO cs3380s14grp10;

--
-- Name: product_productid_seq; Type: SEQUENCE OWNED BY; Schema: db_1; Owner: cs3380s14grp10
--

ALTER SEQUENCE product_productid_seq OWNED BY product.productid;


--
-- Name: purchase_order; Type: TABLE; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

CREATE TABLE purchase_order (
    orderid integer NOT NULL,
    cid integer NOT NULL,
    dateofpurchase date NOT NULL,
    productid integer NOT NULL,
    ordercomplete boolean NOT NULL,
    price double precision NOT NULL
);


ALTER TABLE db_1.purchase_order OWNER TO cs3380s14grp10;

--
-- Name: purchase_order_orderid_seq; Type: SEQUENCE; Schema: db_1; Owner: cs3380s14grp10
--

CREATE SEQUENCE purchase_order_orderid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE db_1.purchase_order_orderid_seq OWNER TO cs3380s14grp10;

--
-- Name: purchase_order_orderid_seq; Type: SEQUENCE OWNED BY; Schema: db_1; Owner: cs3380s14grp10
--

ALTER SEQUENCE purchase_order_orderid_seq OWNED BY purchase_order.orderid;


--
-- Name: roster; Type: TABLE; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

CREATE TABLE roster (
    roster_id integer NOT NULL,
    tournament character varying(80) NOT NULL,
    team_captain_username character varying(16) NOT NULL,
    player1 character varying(25) DEFAULT NULL::character varying,
    player2 character varying(25) DEFAULT NULL::character varying,
    player3 character varying(25) DEFAULT NULL::character varying,
    player4 character varying(25) DEFAULT NULL::character varying,
    team_name character varying(30) DEFAULT NULL::character varying,
    wins integer DEFAULT 0,
    seed integer
);


ALTER TABLE db_1.roster OWNER TO cs3380s14grp10;

--
-- Name: roster_roster_id_seq; Type: SEQUENCE; Schema: db_1; Owner: cs3380s14grp10
--

CREATE SEQUENCE roster_roster_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE db_1.roster_roster_id_seq OWNER TO cs3380s14grp10;

--
-- Name: roster_roster_id_seq; Type: SEQUENCE OWNED BY; Schema: db_1; Owner: cs3380s14grp10
--

ALTER SEQUENCE roster_roster_id_seq OWNED BY roster.roster_id;


--
-- Name: cid; Type: DEFAULT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY customer ALTER COLUMN cid SET DEFAULT nextval('customer_cid_seq'::regclass);


--
-- Name: agentid; Type: DEFAULT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY free_agents ALTER COLUMN agentid SET DEFAULT nextval('free_agents_agentid_seq'::regclass);


--
-- Name: productid; Type: DEFAULT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY product ALTER COLUMN productid SET DEFAULT nextval('product_productid_seq'::regclass);


--
-- Name: orderid; Type: DEFAULT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY purchase_order ALTER COLUMN orderid SET DEFAULT nextval('purchase_order_orderid_seq'::regclass);


--
-- Name: roster_id; Type: DEFAULT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY roster ALTER COLUMN roster_id SET DEFAULT nextval('roster_roster_id_seq'::regclass);


--
-- Data for Name: customer; Type: TABLE DATA; Schema: db_1; Owner: cs3380s14grp10
--

COPY customer (cid, username, salt, hashpass, lastname, firstname, user_email, usertype, street, city, state, zip, phonenumber) FROM stdin;
1	admin	ace3ae900d8023666b0731feb40d76cc632354c7	ee9b8d57358e8dbb34384abfff9c19eee8bde49b	Ryan	Pliske	mizzougaming@yahoo.com	admin	\N	\N	\N	\N	\N
2	macy	0ea03c56f5a3fefa21a35c9dd9f5395da70b0563	786b7d4c6750864cf6c2e9b745d6e0afc9357257	macy	macy	macy	customer	macy	macy	MO	65203	1234567890
\.


--
-- Name: customer_cid_seq; Type: SEQUENCE SET; Schema: db_1; Owner: cs3380s14grp10
--

SELECT pg_catalog.setval('customer_cid_seq', 3, true);


--
-- Data for Name: free_agents; Type: TABLE DATA; Schema: db_1; Owner: cs3380s14grp10
--

COPY free_agents (agentid, cid, gamertag, username, contact, tournament, description) FROM stdin;
1	5	tPlisk	ryan	pliske16@yahoo.com	Halo Reach 4v4	Halo God, slow Coder
2	5	tPlisk	ryan	3147957055	Black Ops II 4v4	And Boss at COD
3	6	sara	sara	sara@something.com	Black Ops II 4v4	gamer
4	6	sara	sara	sara@something.com	Halo Reach 4v4	sara
5	1	FGC Penguin	krcz85	kyle-carlson@live.com	Halo Reach 2v2	Halo
6	8	dockDog	macy	macymay@macy.com	Halo Reach 4v4	I jump off docks
7	7	Krywulf	RonnieTest	ronnielewis88@gmail.com	Black Ops II 4v4	Da best at one hit I win games #skill
8	7	Krywulf	RonnieTest	ronnielewis88@gmail.com	Halo Reach 2v2	#bangbang
9	5		ryan	3147957055	Halo Reach 2v2	
10	5		ryan	3147957055	Halo Reach FFA	
11	5		ryan	3147957055		
12	9	weidhoppa	tom	abc@123.COM	Black Ops II 4v4	I'm a boss. With a cause. 
13	17	klaricMAN	klaric	klaric	Black Ops II 4v4	Like a BOSS
\.


--
-- Name: free_agents_agentid_seq; Type: SEQUENCE SET; Schema: db_1; Owner: cs3380s14grp10
--

SELECT pg_catalog.setval('free_agents_agentid_seq', 13, true);


--
-- Data for Name: product; Type: TABLE DATA; Schema: db_1; Owner: cs3380s14grp10
--

COPY product (productid, productname, productdesc, price, category, quantity, endofsale, imgpath, team_members, orig_quantity) FROM stdin;
12	SSBM	1v1 Max teams 8 Bring your own setup	14	Tournament	8	2014-06-16	img/Tourny1.jpg	1	8
9	Halo Reach 4v4	4v4 Halo Team Pass, you don't need four players, just sign up and search through the Free Agents!	100	Tournament	15	2014-06-16	img/Tourny1.jpg	4	16
6	Halo Reach 2v2	2v2 Halo Reach Tournament	40	Tournament	9	2014-08-16	img/Tourny1.jpg	2	11
2	Mizzou Gaming T-Shirt	Custom T-Shirt with Gamertag on the back	15	Merchandise	11	2014-08-16	img/shirt1.jpg	\N	40
4	Black Ops II 4v4	4v4 Black Ops Tournament	20	Tournament	9	2014-08-16	img/Tourny2.jpg	4	32
1	Mizzou Xbox Controller	Envy Controllers presents a Mizzou black and gold xbox 360 Controller	50	Merchandise	19	2014-08-16	img/controller.jpg	\N	40
3	Halo Reach FFA	FFA Halo Reach Tournament	20	Tournament	16	2014-08-16	img/Tourny1.jpg	1	32
5	StarCraft II	1v1 StarCraft II Tournament	20	Tournament	11	2014-08-16	img/Tourny4.jpg	1	32
\.


--
-- Name: product_productid_seq; Type: SEQUENCE SET; Schema: db_1; Owner: cs3380s14grp10
--

SELECT pg_catalog.setval('product_productid_seq', 19, true);


--
-- Data for Name: purchase_order; Type: TABLE DATA; Schema: db_1; Owner: cs3380s14grp10
--

COPY purchase_order (orderid, cid, dateofpurchase, productid, ordercomplete, price) FROM stdin;
1	2	2014-05-03	1	f	50
\.


--
-- Name: purchase_order_orderid_seq; Type: SEQUENCE SET; Schema: db_1; Owner: cs3380s14grp10
--

SELECT pg_catalog.setval('purchase_order_orderid_seq', 1, true);


--
-- Data for Name: roster; Type: TABLE DATA; Schema: db_1; Owner: cs3380s14grp10
--

COPY roster (roster_id, tournament, team_captain_username, player1, player2, player3, player4, team_name, wins, seed) FROM stdin;
63	Halo Reach 4v4	Tsquared	Tsquared	Snip3down	Elamite Warrior	Neighbor	Str8 Rippin	1	2
57	Black Ops II 4v4	Karma	Karma	Crimsix	TeePee	Aches	Complexity	2	1
59	Black Ops II 4v4	Formal	Formal	Censor	Dedo	Saints	Faze Black	1	4
60	Black Ops II 4v4	Classic	Classic	Replays	JKAP	Theory	Faze Red	0	5
62	Black Ops II 4v4	Nadeshot	Nadeshot	Scumpii	Clayster	Proofy	Optic Gaming	1	3
58	Black Ops II 4v4	MBOZE	MBOZE	Ricky	Mirx	Killa	Optic Nation	0	6
61	Black Ops II 4v4	Sharp	Sharp	Goonjar	Apathy	Neslo	Team Kaliber	2	2
103	SSBM	Azen	Azen				West Coast	1	1
101	SSBM	ChillinDude829	ChillinDude829				West Coast	0	4
48	Halo Reach 2v2	Cloud 	Cloud 	Pistola			Ambush	2	1
44	Halo Reach 2v2	Shockwave	Shockwave	Karma			Carbon	0	8
99	SSBM	Isai	Isai				East Coast	1	5
97	SSBM	Ken	Ken				East Coast	0	6
50	Halo Reach 2v2	Ogre 1	Ogre 1	Ogre 2			Final Boss	0	4
46	Halo Reach 2v2	Roy 	Roy	Lunchbox			Instinct	1	5
49	Halo Reach 2v2	|x Ace x|	|x Ace x|	Elamite Warrior			Shady Halo Kids	0	3
43	Halo Reach 2v2	Tsquared	Tsquared	Snip3down			Str8 Rippin	1	6
100	SSBM	Mew2King	Mew2King	\N	\N	\N	\N	0	2
47	Halo Reach 2v2	Soldier187	Soldier187	Ant			Team Classic	1	2
45	Halo Reach 2v2	SK	SK	Hysteria			Triggers Down	1	7
102	SSBM	Wes	Wes				Deadly Alliance	0	7
89	StarCraft II	BoxeR	BoxeR				SlayerS	1	1
88	StarCraft II	iNcontroL	iNcontroL				Evil Geniuses	2	4
84	StarCraft II	MaDFroG	MaDFroG				SK Gaming	0	5
86	StarCraft II	NaNiwa	NaNiwa				MeetYourMakers	1	3
87	StarCraft II	SEn	SEn				fnatic	0	6
85	StarCraft II	SjoW	SjoW				Team Dignitas	0	2
83	StarCraft II	TheLittleOne	TheLittleOne				TeamLiquid	1	7
65	Halo Reach 4v4	Roy 	Roy	Lunchbox	FeatItself	Hokum	5k	1	1
66	Halo Reach 4v4	Cloud 	Cloud 	Pistola	Heinz	Nexus	Ambush	0	4
64	Halo Reach 4v4	Shockwave	Shockwave	Karma	GH057ayame	Gandhi	Carbon	0	5
68	Halo Reach 4v4	Ogre 1	Ogre 1	Ogre 2	Walshy	Saiyan	Final Boss	0	3
67	Halo Reach 4v4	|x Ace x|	|x Ace x|	Elamite Warrior	lBestman	Hysteria	Shady Halo Kids	0	6
98	SSBM	KDJ	Korean DJ	\N	\N	\N	\N	0	\N
31	Halo Reach FFA	ryan	tPlisk	\N	\N	\N	\N	0	1
32	Halo Reach FFA	ryan	tPlisk	\N	\N	\N	\N	0	1
\.


--
-- Name: roster_roster_id_seq; Type: SEQUENCE SET; Schema: db_1; Owner: cs3380s14grp10
--

SELECT pg_catalog.setval('roster_roster_id_seq', 103, true);


--
-- Name: customer_pkey; Type: CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (cid);


--
-- Name: free_agents_pkey; Type: CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

ALTER TABLE ONLY free_agents
    ADD CONSTRAINT free_agents_pkey PRIMARY KEY (agentid);


--
-- Name: product_pkey; Type: CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

ALTER TABLE ONLY product
    ADD CONSTRAINT product_pkey PRIMARY KEY (productid);


--
-- Name: purchase_order_pkey; Type: CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

ALTER TABLE ONLY purchase_order
    ADD CONSTRAINT purchase_order_pkey PRIMARY KEY (orderid);


--
-- Name: roster_pkey; Type: CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10; Tablespace: 
--

ALTER TABLE ONLY roster
    ADD CONSTRAINT roster_pkey PRIMARY KEY (roster_id);


--
-- Name: purchase_order_cid_fkey; Type: FK CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY purchase_order
    ADD CONSTRAINT purchase_order_cid_fkey FOREIGN KEY (cid) REFERENCES customer(cid);


--
-- Name: purchase_order_productid_fkey; Type: FK CONSTRAINT; Schema: db_1; Owner: cs3380s14grp10
--

ALTER TABLE ONLY purchase_order
    ADD CONSTRAINT purchase_order_productid_fkey FOREIGN KEY (productid) REFERENCES product(productid);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

