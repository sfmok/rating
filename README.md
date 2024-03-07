# Vico Rating
## Solution:

### How it works:

For easy testing this solution approach I recommend to set up Nix instead of docker or using local environment.

- Set up [Nix](https://github.com/DeterminateSystems/nix-installer)
- Lunch nix devenv shell ```nix develop --impure```
- Install packages ```composer install```
- Run PHPUnit ```./bin/phpunit```

### Manual Testing:
- Run Symfony & Mysql servers using devenv shell ```devenv up```
- Create DB ```bin/console doctrine:database:create```
- Execute Migrations ```bin/console doctrine:migrations:migrate```
- Load Fixtures ```bin/console doctrine:fixtures:load -n```
- Login to get JWT
```shell
curl -X POST 'http://localhost:8000/api/login_check' \
-H 'Content-Type: application/json' \
-d '{
    "username": "johndoe@foobar.com",
    "password": "admin"
}'
```
- Add Rate 
```shell
curl -X POST 'http://localhost:8000/api/rate' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer JWT_TOKEN' \
  -d '{
    "satisfaction": 5,
    "communication": 5,
    "feedback": "Good Job",
    "quality_of_work": 5,
    "value_for_money": 5,
    "project_id": 1
  }'
  ```
- Edit Rate
```shell
curl -X PUT 'http://localhost:8000/api/rate/1' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer JWT_TOKEN' \
  -d '{
    "satisfaction": 1,
    "communication": 1,
    "feedback": "Bad Job",
    "quality_of_work": 1,
    "value_for_money": 1,
    "project_id": 2
  }'
  ```


### Code Quality Checks

1. Psalm ```./vendor/bin/psalm```
2. PHPCsFixer ```./vendor/bin/php-cs-fixer fix -v --diff --dry-run```