# RSS Filter

A simple proxy allowing to filter RSS feeds.

[![DockerHub](https://img.shields.io/badge/download-DockerHub-blue?logo=docker)](https://hub.docker.com/r/programie/rssfilter)
[![GitHub release](https://img.shields.io/github/v/release/Programie/RSSFilter)](https://github.com/Programie/RSSFilter/releases/latest)

## Security notice

As of now, there is no authentication implementation available. Everyone able to access the application can access and modify your feeds!

You should not make this application reachable from the internet without configuring authentication on the webserver level (i.e. htaccess/htpasswd on Apache).

A proper authentication system might be implemented in the future.

## Installation

Download the [latest release](https://github.com/Programie/RSSFilter/releases/latest) and extract it into your webserver directory. Change the document root to the `httpdocs` directory.

Alternatively, you might want to use the [Docker image from Docker Hub](https://hub.docker.com/r/programie/rssfilter).

Create a new database and import the [database schema](database.sql).

## Configuration

The configuration is done using environment variables which can be set in your webserver or by creating a `.env` file in the application root.

Example:

```dotenv
DATABASE_HOST="your-mysql-server.example.com"
DATABASE_NAME="your-database-name"
DATABASE_USERNAME="your-database-username"
DATABASE_PASSWORD="your-database-password"

APP_URL="https://rss.example.com"
```