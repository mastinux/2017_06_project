DROP database IF EXISTS `auctions_db`;

create database `auctions_db`;

use `auctions_db`;

create table auctions_user(
	email varchar(320) not null,
	pw varchar(255) not null,
	primary key (email)
);

create table auctions_thr(
    email varchar(320) not null,
    thr_value decimal(10,2) not null,
    thr_timestamp timestamp(6) not null,   /* updated every time the record is changed */
    primary key(email), 
    foreign key (email) references auctions_user(email) on delete cascade
);
alter table auctions_thr add constraint auctions_thr_constraint check(thr_value>1);

insert into auctions_user(email, pw) values('a@p.it', md5('p1'));
insert into auctions_user(email, pw) values('b@p.it', md5('p2'));
insert into auctions_user(email, pw) values('c@p.it', md5('p3'));


insert into auctions_thr(email, thr_value) values('a@p.it', 0.0);
insert into auctions_thr(email, thr_value) values('b@p.it', 0.0);
insert into auctions_thr(email, thr_value) values('c@p.it', 0.0);
