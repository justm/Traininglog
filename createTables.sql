CREATE TABLE `role` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(20) not null,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `role` VALUES (1,'admin');
INSERT INTO `role` VALUES (2,'athlete');
INSERT INTO `role` VALUES (3,'coach');
	
CREATE TABLE `country` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(40) not null,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `activity` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    `name_sk` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `address` (
    `id` int NOT NULL AUTO_INCREMENT,
    `city` varchar(40),
    `street` varchar(40),  
    `zip` varchar(15),
    `id_country` int,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_country`) REFERENCES `country`(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `user_fitness` (
    `id` int NOT NULL AUTO_INCREMENT,
    `weight` int,
    `height` int,
    `max_hr` int,
    `rest_hr` int,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `privacy` (
    `id` int NOT NULL AUTO_INCREMENT,
    `lastname` int NOT NULL default 1,  
    `email` int NOT NULL default 1,
    `phone` int NOT NULL default 1,
    `street` int NOT NULL default 1,
    `city` int NOT NULL default 1,
    `country` int NOT NULL default 1,
    `birthday` int NOT NULL default 1,
    `weight` int NOT NULL default 1,
    `height` int NOT NULL default 1,
    `max_hr` int NOT NULL default 1,
    `rest_hr` int NOT NULL default 1,
    `basic_tr_data` int NOT NULL default 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `user` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_role` int NOT NULL,
    `id_address` int,
    `id_primary_activity` int,
    `id_user_fitness` int,
    `id_privacy` int,
    `name` varchar(30) NOT NULL,
    `lastname` varchar(30),
    `fullname` varchar(60) NOT NULL,
    `username` varchar(20) UNIQUE NOT NULL,
    `password` varchar(40) NOT NULL,
    `email` varchar(40) UNIQUE NOT NULL,
    `gender` char(1) NOT NULL,
    `birthday` date,
    `phone` varchar(15),
    `about` varchar(250),
    `profile_picture` varchar(100),
    `account_status` int(1) DEFAULT 0,  
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_role`) REFERENCES `role`(id),
    FOREIGN KEY (`id_address`) REFERENCES `address`(id),
    FOREIGN KEY (`id_primary_activity`) REFERENCES `activity`(id),
    FOREIGN KEY (`id_user_fitness`) REFERENCES `user_fitness`(id),
    FOREIGN KEY (`id_privacy`) REFERENCES `privacy`(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `user` (id_role, id_address, id_primary_activity, id_user_fitness, id_privacy, `name`, lastname, fullname, username, `password`, email, gender, birthday, phone, about, profile_picture, account_status) 
	VALUES (1, NULL, NULL, NULL, NULL, 'Main', 'Admin', 'Main Admin', 'mainadmin', '6fd1ff88b8a93a55e0693236b464c03c', 'admin@admin.sk', 'M', '2013-06-05', NULL, NULL, NULL, 0);


INSERT INTO `activity` values(1,'Cycling', 'Cyklistika');
INSERT INTO `activity` values(2,'Running', 'Beh');
INSERT INTO `activity` values(3,'Xc-skiing', 'Beh na lyžiach');
INSERT INTO `activity` values(4,'Swimming', 'Plávanie');
INSERT INTO `activity` values(5,'Rollerskating', 'Korčuľovanie');
INSERT INTO `activity` values(6,'Hiking / Skialp', 'Turistika / Skialp ');
INSERT INTO `activity` values(7,'Indoor', 'Indoor');
INSERT INTO `activity` values(8,'Other', 'Iné');


CREATE TABLE `visibility` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(15),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `visibility` values(1, 'public');
insert into `visibility` values(2, 'friends');
insert into `visibility` values(3, 'secret');

CREATE TABLE `sparring` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_user` int NOT NULL,  
    `id_partner` int NOT NULL,
    `status` int NOT NULL,
    `request_date` DATE NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_user`) REFERENCES `user`(id),
    FOREIGN KEY (`id_partner`) REFERENCES `user`(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `default_hr_zone` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `default_hr_zone` values (1,'Recovery zone');
insert into `default_hr_zone` values (2,'Aerobic zone');
insert into `default_hr_zone` values (3,'Anaerobic threshold');
insert into `default_hr_zone` values (4,'Lactate zone');

CREATE TABLE `hr_zone` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(30),
    `min` int(3) NOT NULL,
    `max` int(3) NOT NULL,
    `id_user` int NOT NULL,
    `id_default` int,
    FOREIGN KEY (`id_user`) REFERENCES `user`(id),
    FOREIGN KEY (`id_default`) REFERENCES `default_hr_zone`(id),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `default_label` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `color` varchar(10) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `default_label` values (1,'Rest day','00b7ea');
insert into `default_label` values (2,'Competition','FFCC00');
insert into `default_label` values (3,'Illness','000000');

create table `label`(
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(50),
    `color` varchar(10),
    `id_user` int NOT NULL,
    `id_default` int,
    PRIMARY KEY(`id`),
    FOREIGN KEY (`id_user`) REFERENCES `user`(id),
    FOREIGN KEY (`id_default`) REFERENCES `default_label`(id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `training_plan` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_user` int NOT NULL,  
    `id_activity` int,
    `date` DATE NOT NULL,
    `duration` TIME,
    `description` varchar(300),
    `id_label` int,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id`),
    FOREIGN KEY (`id_activity`) REFERENCES `activity`(`id`),
    FOREIGN KEY (`id_label`) REFERENCES `label`(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `training_entry`(
    `id` int NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `duration` TIME NOT NULL,
    `description` varchar(500),
    `feelings` int(1),
    `min_hr` int(3),
    `avg_hr` int(3),
    `max_hr` int(3),
    `distance` decimal(10,3),
    `avg_speed` decimal(10,3),
    `avg_pace` TIME,
    `ascent` int,
    `max_altitude` int,
    `avg_watts` int,
    `max_watts` int,
    `id_activity` int NOT NULL,
    `id_user` int NOT NULL,
    `id_visibility` int not NULL,
    `id_label` int,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id_user`) REFERENCES `user`(`id`),
    FOREIGN KEY(`id_activity`) REFERENCES `activity`(`id`),
    FOREIGN KEY(`id_visibility`) REFERENCES `visibility`(`id`),
    FOREIGN KEY (`id_label`) REFERENCES `label`(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `zone_time`(
    `id` int NOT NULL AUTO_INCREMENT,
    `p_time` int(3) NOT NULL,
    `id_trainingentry` int NOT NULL,
    `id_hrzone` int NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id_trainingentry`) REFERENCES `training_entry`(`id`),
    FOREIGN KEY(`id_hrzone`) REFERENCES `hr_zone`(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `comment`(
    `id` int NOT NULL AUTO_INCREMENT,
    `text` varchar(300),
    `id_trainingentry` int NOT NULL,
    `id_user` int NOT NULL,
    `id_visibility` int not NULL,
    `date` DATETIME NOT NULL,
    `seen` int(1) NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id_user`) REFERENCES `user`(`id`),
    FOREIGN KEY(`id_trainingentry`) REFERENCES `training_entry`(`id`),
    FOREIGN KEY(`id_visibility`) REFERENCES `visibility`(`id`) 
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `coach_cooperation` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_coach` int NOT NULL,
    `id_athlete` int NOT NULL,
    `status` int(1) NOT NULL,
    `cooperatin_since` date NOT NULL,
    `athlete_changes_made` int(1),
    `athlete_changes_date` date, 
    `athlete_changes_confirmed` int(1),
    FOREIGN KEY (`id_coach`) REFERENCES `user`(id),
    FOREIGN KEY (`id_athlete`) REFERENCES `user`(id),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;