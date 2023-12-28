**Docker**
| | command|
|--|--|
| stop | `docker-compose stop`|
| shell | `docker-compose exec php sh`|
| start | `docker-compose up --detach` or `docker-compose up --build nginx -d`|
| destroy | `docker-compose down --volumes`|
| build | `docker-compose up --detach` |

**Composer**
| | command|
|--|--|
| install| `docker-compose run --rm composer install`|
| update | `docker-compose run --rm composer update`|
| dump-autoload| `docker-compose run --rm composer dump-autoload`|

**Artisan**
| | command|
|--|--|
| key-generate| `docker-compose run --rm artisan key:generate`|
| optimize| `docker-compose run --rm artisan optimize`|
| serve| `docker-compose run --rm artisan serve`|
| migrate-seed| `docker-compose run --rm laravel-migrate-seed`|

**Npm**
| | command|
|--|--|
| install| `docker-compose run --rm npm install`|
| build| `docker-compose run --rm npm run build`|
| run-dev| `docker-compose run --rm --service-ports npm run dev`|
