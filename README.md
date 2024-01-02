# JRK Kinderstadt

This is an App to manage the upcomming JRK Kinderstadt where we setup an "Online Banking" foreach child.

## How it works

The data are right now stored in JSON files, it is planed that this will be moved to a database later.
The bookings are stored into the `data/child_bookings.json` and the child metadata including the UUID is stored into `data/child_metadata.json`

## How to setup

- ssh to your webserver
- Create an folder
- Run `git clone https://github.com/zero-24/jrk-kinderstadt-app.git .` inside the folder
- Point your domain to the `www` folder
- `cp etc/constants.dist.php etc/constants.php`
- `nano etc/constants.php` -> Setup the constants within this file
- `composer install --no-dev`
- `cp data/child_bookings.dist.json data/child_bookings.json`
- `cp data/child_metadata.dist.json data/child_metadata.json`
- `cp data/session.dist.json data/session.json`

## Optional Setup
### Custom Favicon

- Create a favicon package using [Favicon Generator. For real.](https://realfavicongenerator.net/)
- place the files from the download into the `www` folder

### UUID Generator

[UUID Generator](https://www.uuidgenerator.net/)
