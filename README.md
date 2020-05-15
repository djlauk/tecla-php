# tecla-php

Tecla, the Tennis Club App, written as a server-side solution in PHP.

Tecla allows the tennis club to allocate the time slots it bought at tennis centers for its members.

## Contributing / Developing

If you're interested in contributing to this project, have a look at [CONTRIBUTING.md](./CONTRIBUTING.md).

## Installation

This part describes how to install tecla-php if you want to host it yourself or you're setting up your development environment for the first time.

### Prerequisites

You'll need the following:

- A web server running PHP. (Tecla was specifically written for PHP 7.3.)
- A MySQL or MariaDB server.

### Installation steps

#### Setup the database

- Connect to the database server using an SQL client or admin interface. This depends very much on the setup of your webhoster (or your local development environment), so I can't describe it in more detail here.
- If your hoster has not created a database schema for you, create one now, using the commands provided in `./db/00-create-db.sql`.
- Open the database schema, and create the initial table structure, using the commands provided in `./db/01-create-tables.sql`.
- Create an initial admin user, using the commands provided in `./db/02-create-admin.sql`.

#### Update the configuration

- Make a copy of `./src/config.php` and name it `./src/config.local.php`.
- Open `./src/config.local.php` in an editor.
- Edit the database connection settings. (The settings in the repository are from the local development environment using `docker-compose`.)
- Edit other settings, as appropriate.
- Save `./src/config.local.php`.
