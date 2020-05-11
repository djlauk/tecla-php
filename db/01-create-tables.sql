-- initial table structure

CREATE TABLE `users` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `displayName` varchar(80) NOT NULL,
    `passwordHash` varchar(250) NULL,
    `email` varchar(250) NOT NULL,
    `role` varchar(20) NOT NULL DEFAULT 'guest',
    `failedLogins` int unsigned NOT NULL DEFAULT 0,  -- slow down brute force password cracking attacks
    `lockedUntil` datetime NULL DEFAULT NULL,  -- slow down brute force password cracking attacks
    `disabledOn` datetime NULL DEFAULT CURRENT_TIMESTAMP,  -- starts disabled, will be enabled later
    `verifiedOn` datetime NULL DEFAULT NULL,
    `lastLoginOn` datetime NULL DEFAULT NULL,
    `lastLoginFrom` varchar(80) NULL,

    -- meta data columns
    `metaCreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metaUpdatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `metaVersion` int unsigned NOT NULL,

    PRIMARY KEY (`id`),

    UNIQUE INDEX (`email`)
);


CREATE TABLE `auditlog` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `action` varchar(100) NOT NULL, -- action short code, e.g. 'USER:PWCHANGE' or 'GAME:BOOK'
    `user_id` int unsigned NOT NULL, -- who did it
    `object` varchar(250) NULL,  -- optional: to whom/what, e.g. 'GAME:17'
    `message` varchar(1000) NULL, -- optional: description of what happened

    -- meta data columns
    `metaCreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metaUpdatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `metaVersion` int unsigned NOT NULL,

    PRIMARY KEY (`id`),

    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),

    INDEX (`action`) -- speed up looking up only certain actions
);


CREATE TABLE `verification_tokens` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `token` varchar(32) NOT NULL,
    `user_id` int unsigned NOT NULL,
    `purpose` varchar(25) NOT NULL, -- e.g. 'pwreset'

    -- meta data columns
    `metaCreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metaUpdatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `metaVersion` int unsigned NOT NULL,

    PRIMARY KEY (`id`),

    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),

    UNIQUE INDEX (`token`) -- speed up looking up tokens
);


CREATE TABLE `tournaments` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `registrationStartsOn` datetime NOT NULL,
    `registrationClosesOn` datetime NOT NULL,
    `gamesStartOn` datetime NOT NULL,
    `gamesEndOn` datetime NOT NULL,
    `notes` varchar(250) NULL,

    -- meta data columns
    `metaCreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metaUpdatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `metaVersion` int unsigned NOT NULL,

    PRIMARY KEY (`id`)
);


CREATE TABLE `games` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `startTime` datetime NOT NULL,
    `endTime` datetime NOT NULL,
    `court` varchar(50) NOT NULL,
    `player1_id` int unsigned NULL,
    `player2_id` int unsigned NULL,
    `player3_id` int unsigned NULL,
    `player4_id` int unsigned NULL,
    `tournament_id` int unsigned NULL,
    `winner` int unsigned NULL, -- either NULL, 1, or 2
    `status` varchar(20) NOT NULL DEFAULT 'available', -- e.g. available, free, regular, training, tournament, blocked
    `notes` varchar(250) NULL,

    -- meta data columns
    `metaCreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metaUpdatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `metaVersion` int unsigned NOT NULL,

    PRIMARY KEY (`id`),

    FOREIGN KEY (`player1_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`player2_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`player3_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`player4_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`),

    INDEX (`startTime`)
);


CREATE TABLE `time_slots` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `weekday` int unsigned NOT NULL, -- Sunday = 0, Monday = 1, ...
    `startTime` time NOT NULL,
    `endTime` time NOT NULL,
    `court` varchar(50) NOT NULL,
    `notes` varchar(250) NULL,

    -- meta data columns
    `metaCreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `metaUpdatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `metaVersion` int unsigned NOT NULL,

    PRIMARY KEY (`id`)
);
