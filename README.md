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

1. Update your production settings in the `.env.production` file. For more options see `.env.example`.
- Create remote repository (e.g. on GitHub or BitBucket). If your repositiory is private, you might have to manage [deploy keys](https://deployer.org/docs/advanced/deploy-and-git.html#deploy-keys).
- Update `hosts.yml` file with your server information (do not use `~` for `deploy_path`!). See [Deployer docs](https://deployer.org/docs/hosts.html) for more info.
- Add the URL of your remote repository to `deploy.php` file.

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

### Exchange content with remote server

To exchange data with server you have to install WP-CLI on your server first:

```sh
$ dep wp-cli:install production
```

Then you can *pull* or *push* database and uploads from/to remote server:

```sh
$ dep content:pull production
$ dep content:push production
```

### Backup remote server database

```sh
$ dep db:backup production
```

### Rollback remote database to last backup

```sh
$ dep db:rollback production
```

## Misc

### SSL/TLS

While Lando will automatically trust this CA internally it is up to you to trust it on your host machine. Doing so will alleviate browser warnings regarding certs we issue. Read more about this in the [Lando docs](https://docs.devwithlando.io/config/security.html).

## Documentation

- Bedrock: [https://roots.io/bedrock/docs/](https://roots.io/bedrock/docs/)
- Lando: [https://docs.devwithlando.io/](https://docs.devwithlando.io/)
- Deployer: [https://deployer.org/docs/getting-started.html](https://deployer.org/docs/getting-started.html)
