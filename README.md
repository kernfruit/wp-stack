# WP-Stack

Opinionated starting point for WordPress projects based on [Bedrock](https://roots.io/bedrock/) by Roots. It adds [Lando](https://docs.devwithlando.io/) for spinning up a development environment on Docker and [Deployer](https://deployer.org/) for deploying to a production server.

## Requirements

### On you computer for development
* Lando - [Install](https://docs.devwithlando.io/installation/system-requirements.html)
* Deployer - [Install](https://deployer.org/docs/getting-started.html)

### On server

Versions should match your development container (See `.lando.yml`)!

* Apache 2.4
* PHP 7.2
* MySQL 5.7
* SSH access
* git
* Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Installation

1. Create a new project:
    ```sh
    $ git clone https://github.com/kernfruit/wp-stack
    $ git init
    ```
- *Optional*: Change package name and URLs to your project name in following files:
    ```
    composer.json
    .lando.yml
    .env.lando
    ```
- Start Lando and follow instructions to create WordPress site:
    ```sh
    $ lando start
    ```
- *Optional*: Require additional dependencies and install them:
    ```sh
    $ lando composer require <package>
    $ lando composer install
    ```
- Add the following line to your `/etc/hosts` file:
  ```
  127.0.0.1 wp-stack.test
  ```
  <small>If you changed the `WP_URL` in your .env file you have to use it here as well!</small>

- Access WordPress admin at https://wp-stack.test/wp-admin

## Deployment

### Prerequisites

Deployment is done via [Deployer](https://deployer.org/). You need SSH access to your server.

1. Make shure that your server has the required environment variables defined. If you are using Apache you can do this with the [SetEnv directive](https://docstore.mik.ua/orelly/linux/apache/ch04_06.htm) in your `.htaccess` file or your vhosts configuration file:
  ```
  SetEnv WP_ENV production
  SetEnv WP_HOME http://example.com
  ```
  For all options see `.env.example` file.

- Create remote repository (e.g. on GitHub or BitBucket).
- Update `hosts.yml` file with your server information. See [Deployer docs](https://deployer.org/docs/hosts.html) for more info.
- Add the URL of your remote repository to `deploy.php` file.
- *Optional*: If you have composer not installed on your server yet, you can use the following task:
```sh
$ dep install-composer production
```

### Deploy code

To install a fresh instance from your remote repository, run:

```sh
$ dep deploy production
```

## Migrate data

### Import/export database

```sh
$ lando db-export
$ lando db-import dump.sql
```

See Lando docs for more information and options on [exporting](https://docs.devwithlando.io/guides/db-export.html)/[importing](https://docs.devwithlando.io/guides/db-import.html) databases.


## Documentation

- Bedrock: [https://roots.io/bedrock/docs/](https://roots.io/bedrock/docs/)
- Lando: [https://docs.devwithlando.io/](https://docs.devwithlando.io/)
- Deployer: [https://deployer.org/docs/getting-started.html](https://deployer.org/docs/getting-started.html)
