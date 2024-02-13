# wporg-gp-translation-events

## Development environment
First follow [instructions to install `wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/#prerequisites).

Then you can run a local WordPress instance with the plugin installed:

```shell
composer dev:start
```

Once the environment is running, you must create the database tables needed by this plugin:

```shell
composer dev:db:schema
```

WordPress is now running at http://localhost:8888, user: `admin`, password: `password`.

### Local environment

If you are not using `wp-env`, you need to add the tables to the database of your local environment. To do this, you can run this command from the plugin folder:

```
wp db query < schema.sql
```

### Internal URLs

To access to the **event list**, you need to add `/glotpress/events` to your base URL. E.g.:

```
http://localhost:8888/glotpress/events
```

To add a **new event**, you need to add `/glotpress/events/new` to your base URL. E.g.:

```
http://localhost:8888/glotpress/events/new
```
