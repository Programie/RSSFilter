# RSSFilter

A simple proxy allowing to filter RSS feeds.

## How to run

* `docker run -p 80:80 registry.gitlab.com/programie/rssfilter`
* Use `https://yourhost/?url=<your RSS url>&filter[]=something to filter out&filter[]=something else` as the URL for your RSS feed reader