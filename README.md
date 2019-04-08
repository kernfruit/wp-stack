# WP-Stack

Opinionated starting point for WordPress projects based on [Bedrock](https://roots.io/bedrock/) by Roots. It adds [Lando](https://docs.devwithlando.io/) for spinning up a development environment on Docker and [Deployer](https://deployer.org/) for deploying to a production server.

## Requirements

### On you computer for development
* Lando (Tested with v3.0.0-rc.10) - [Install](https://docs.devwithlando.io/installation/system-requirements.html)
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

- Change package name and URLs to your project name in following files:
  ```
  composer.json
  .lando.yml
  .env.lando
  ```
  <small>If you do not want to use the `*.lndo.site` domain for development (e.g. for offline use) you have to manage SSL certificates by yourself! See section [Use custom domain for development](#use-custom-domain-for-development).</small>

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
  127.0.0.1 wp-stack.lndo.site
  ```
  <small>If you changed the `WP_URL` in your .env file you have to use it here as well!</small>

- Trust the `*.lndo.site` certificate to avoid warnings in browser:
  ```sh
  $ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain ~/.lando/certs/lndo.site.pem
  ```

- Access WordPress admin at https://wp-stack.lndo.site/wp-admin

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

### Test mails

This boilerplate comes with a [MailHog](https://github.com/mailhog/MailHog) service installed and configured to catch all mail that is send in development. The MailHog frontend can be accessed at `mail.<domain>.lndo.site`. You can change this URL in the `.lando.yml` file.

### Use custom domain for development

The `*.lando.site` domain will not work offline because it is an actual ON THE INTERNET wildcard DNS entry that points all `*.lndo.site` subdomains to `localhost/127.0.0.1`. See [Lando docs](https://docs.devwithlando.io/config/proxy.html) for more details on this.

Fortunately you can use your own second-level domain replacing `lando.site` that will also work offline. To do so you have to add a proxy to your `.lando.yml`:

```
# .lando.yml
…
proxy:
  appserver:
    - wp-stack.dev.test
…
```

In this example we use `*.dev.test` as second-level domain for our project. Now we have to tell Lando to create and use certificates for this domain. For that we have to change the global Lando config in `~/.lando/config.yml`. If this file does not exist yet, we create it:

```
# ~/.lando/config.yml
domain: dev.test
```

Lando will create a wildcard certificate with this domain on start in `~/.lando/certs/`. This is also the reason why just using a top-level domain without a second level (e.g. just `*.test`) will not work: It is [not allowed](https://en.m.wikipedia.org/wiki/Wildcard_certificate#Limitations) due to security reasons.

After starting Lando with `lando start` we [trust the created Lando cert](https://docs.devwithlando.io/config/security.html#trusting-the-ca) to avoid browser warnings:

```sh
$ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain ~/.lando/certs/dev.test.pem
```

Do not forget to add your new project (sub-)domain to `/etc/hosts`.

## Documentation

- Bedrock: [https://roots.io/bedrock/docs/](https://roots.io/bedrock/docs/)
- Lando: [https://docs.devwithlando.io/](https://docs.devwithlando.io/)
- Deployer: [https://deployer.org/docs/getting-started.html](https://deployer.org/docs/getting-started.html)
