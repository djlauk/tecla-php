-- initial guest account

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
	'Guest',
	'',  -- **this** password will never match anything
	'guest',
	'guest',
	NULL,  -- not disabled, so it can be used when booking
	NULL,
	'9999-12-31 23:59:59', -- but locked out for ever, os it can't log in
	1
);

