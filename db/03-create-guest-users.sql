-- initial guest accounts

INSERT INTO `users` (
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `disabledOn`,
    `verifiedOn`,
    `lockedUntil`,
    `metaVersion`
) VALUES (
	'Guest 1',
	'',  -- **this** password will never match anything
	'guest1',
	'guest',
	NULL,  -- not disabled, so it can be used when booking
	NULL,
	'9999-12-31 23:59:59',
	1
);
INSERT INTO `users` (
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `disabledOn`,
    `verifiedOn`,
		`lockedUntil`,
    `metaVersion`
) VALUES (
	'Guest 2',
	'',  -- **this** password will never match anything
	'guest2',
	'guest',
	NULL,  -- not disabled, so it can be used when booking
	NULL,
	'9999-12-31 23:59:59',
	1
);
INSERT INTO `users` (
    `displayName`,
    `passwordHash`,
    `email`,
    `role`,
    `disabledOn`,
    `verifiedOn`,
		`lockedUntil`,
    `metaVersion`
) VALUES (
	'Guest 3',
	'',  -- **this** password will never match anything
	'guest3',
	'guest',
	NULL,  -- not disabled, so it can be used when booking
	NULL,
	'9999-12-31 23:59:59',
	1
);
