-- initial admin account

INSERT INTO `users` (
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `disabledOn`,
    `verifiedOn`,
    `metaVersion`
) VALUES (
	'Initial Admin',
	'$2y$10$sOfNus3mkz5MKaSkz9eZSuZFK2YSp0iwZBfzqetthS2yLypO/R2FC',  -- pw = topsecret
	'admin@example.org',
	'admin',
	NULL,
	NULL,
	1
);
