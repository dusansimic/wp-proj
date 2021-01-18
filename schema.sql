create table korisnik
(
    id        int auto_increment
        primary key,
    username  varchar(20) not null,
    password  varchar(20) not null,
    ime       varchar(15) not null,
    prezime   varchar(15) not null,
    datrodj   date        not null,
    brIndeksa varchar(9)  not null
);

create table predmet
(
    id         int auto_increment
        primary key,
    korisnikId int           not null,
    ime        varchar(50)   not null,
    sifra      varchar(5)    not null,
    opis       varchar(1024) null,
    profesor   varchar(30)   not null,
    asistenti  varchar(120)  null,
    constraint sifra
        unique (sifra),
    constraint predmet_korisnikId_id_fk
        foreign key (korisnikId) references korisnik (id)
);

create table nastava
(
    id        int auto_increment
        primary key,
    predmetId int          not null,
    naziv     varchar(20)  not null,
    predavac  varchar(30)  not null,
    link      varchar(200) not null,
    constraint nastava_predmetId_id_fk
        foreign key (predmetId) references predmet (id)
);
