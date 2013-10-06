rescuetime-statusboard
====

Array of panels for [Status Board][] iPad app from [Panic][] displaying your weekly [RescueTime][] productivity statistics.

Application is consuming actual [RescueTime API][] data through [rescuetime-api-php][] wrapper library.

### Panels ###

##### Summary Panel #####
![Summary Panel](http://f.cl.ly/items/153P0T393F2G411f172d/statusboard-summary.jpg)

##### Productivity By Day Panel #####
![Productivity By Day Panel](http://f.cl.ly/items/1p0n00010s052H3T0B1e/statusboard-productivity-by-day.jpg)

##### Top Categories Panel #####
![Top Categories Panel](http://f.cl.ly/items/273U27002i1q3x2H2I1z/statusboard-productivity-by-category.jpg)

##### Top Activities Panel #####
![Top Activities Panel](http://f.cl.ly/items/3B1v0n053X1C1o1o180s/statusboard-productivity-by-activity.jpg)

### Installation ###

The easiest way to get started is to install application on [Heroku][].

Clone application locally:

```
git clone git@github.com:borivojevic/rescuetime-statusboard.git
```

You can obtain RescueTime API key on [API Key Management][] console page.

Replace "rescuetime-api-key" in config.json with your private api-key.

Commit changes:

```
git commit -am "Set rescuetime API key"
```

Create Heroku application stack:

```
heroku create --buildpack https://github.com/CHH/heroku-buildpack-php
```

> If you don't have it already, sign-up for free [Heroku][] account and install [Heroku Toolbelt][].

Push application to Heroku:

```
git push heroku master
```

To determine your application URL run:

```
heroku apps:info
```

Remember "Web URL":

![Web URL](http://f.cl.ly/items/0x230E002B3S0t3C3I0t/heroku-app-info-2.png)

### Loading panels in Status Board iPad app ###

Open status board app on your iPad.

Go to settings.

To add summary panel select table and provide following Data URL - {$YOUR_WEB_URL_HERE}/summary

To add productivity by day panel select graph and provide following Data URL - {$YOUR_WEB_URL_HERE}/productivity_by_day

To add top categories panel select graph and provide following Data URL - {$YOUR_WEB_URL_HERE}/productivity_by_category

To add top activities panel select graph and provide following Data URL - {$YOUR_WEB_URL_HERE}/productivity_by_activity

[Status Board]: http://panic.com/statusboard/
[Panic]: http://panic.com/
[RescueTime]: https://www.rescuetime.com
[RescueTime API]: https://www.rescuetime.com/analytic_api_setup/doc
[rescuetime-api-php]: https://github.com/borivojevic/rescuetime-api-php
[API Key Management]: https://www.rescuetime.com/anapi/manage
[Heroku]: https://www.heroku.com/
[Heroku Toolbelt]: https://toolbelt.heroku.com/
