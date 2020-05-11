# Contributing

## Summary

1.  Choose an issue to work on. If none exists, create one.
2.  Fork this repository on github.com into your own account. (There's a button for that on github.com.)
3.  Clone _your_ repository to your local computer.
4.  Create a branch for the issue: `git checkout -b feature-xy`
5.  Work on your feature.
6.  Commit your results and push your branch to your repository: `git push -u origin feature-xy`
7.  Create a pull request from your repository on github.com. (There's a button for that on github.com.)

## Used libraries

- PHP app framework: [Lime](https://github.com/agentejo/lime).
- Data access layer: Home grown data access and transfer objects (DAOs, DTOs) on top of [PDO](https://www.php.net/manual/en/book.pdo.php).

## Development environment / software stack

For development, you should use a local web and database server. The classical setup (or software stack) for that is called "LAMP", which stands for Linux (OS), Apache (web server), MySQL (database), PHP (scripting language). For other operating systems the name has been sometimes adapted (XAMPP, WAMP, MAMP, etc.), but the idea stays the same.

### On Linux or MacOS

If you're developing on Linux or MacOS, I recommend using a local containerized development setup with [docker](https://www.docker.com/) and `docker-compose` (https://docs.docker.com/compose/). I have prepared one in `./docker/docker-compose.yml`.

If you have `docker` and `docker-compose` installed, all you need to do is:

```
cd docker
docker-compose up
```

This will give you:

- Container _tennis-www_: An apache web server (), serving content from the `./src/` directory on http://localhost:8100/.
- Container _tennis-db_: A MariaDB server, listening on port 9906 (in case you want to connect a local DB GUI).
- Container _tennis-pma_: A web server running phpMyAdmin, a web based DB GUI, available on http://localhost:8101/.

### On Windows

I have very little experience using docker on Windows.

I recommend using [UwAmp](https://www.uwamp.com/en/) as it does not require any installation. Just download and extract it and you can start the Apache web server and the MySQL database server. And, which is most important to me, if you delete the folder it's gone from your system and left nothing behind.

## Adjust local configuration

Copy file `./src/config.php` to `./src/config.local.php`. Now make local modifications in file `./src/config.local.php`, which is listed in `.gitignore` to prevent accidentally publishing settings to the world.

## Initial setup

For the initial setup, see the installation instructions in [README.md](./README.md). You'll need to create a database schema and the initial table structure.
