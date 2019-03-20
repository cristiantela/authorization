create database authorization character set utf8;

create table user(
	id int not null auto_increment,
	username varchar (30) not null,
	password varchar (30) not null,
	primary key (id)
)default character set=utf8;

create table user_session(
	id int not null auto_increment,
	user int not null,
	token varchar(20) not null,
	active boolean,
	primary key (id),
	foreign key (user) references user(id)
)default character set=utf8;

insert into user (username, password) values ('mathues', 'test');