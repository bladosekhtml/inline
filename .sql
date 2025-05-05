create database tz1;
use tz1;

create table posts (
	id serial primary key,
	userId smallint not null,
	title varchar(100) not null,
	body text
);

create table comments (
	id serial primary key,
	postId int references posts(id) not null,
	name varchar(100) not null,
	email varchar(100) not null,
	body text
);