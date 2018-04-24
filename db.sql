create table jobs (
    id int(10) unsigned not null primary key,
    title varchar(255) not null,
    company varchar(255) not null,
    created_at datetime not null
) engine=InnoDB default charset=utf8;
