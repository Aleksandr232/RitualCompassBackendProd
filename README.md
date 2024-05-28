## Сборка Docker образа


1.  используя команду `docker compose up`

## Настройка бэка (он настройн, если что-то пойдет не так)

1. composer install (установка всех зависимостей)
2. php artisan key:generate (уникальный ключ приложения)
3. php artisan config:cache (обеденение всех настройк)
4. php artisan route:cache (оновления маршрутов)
5. php artisan migrate (подключения наших таблиц к базе данных)
6. php artisan l5-swagger:generate (для генерация swagger описывае api интерфейсы)
7. php artisan config:cache && php artisan route:cache && php artisan migrate && php artisan l5-swagger:generate (запуск сразу всех зависимостей)
## Бэк
- хост http://localhost:8000/
- api документация http://localhost:8000/api/documentation (показывает какой запрос нужно использовать, и необходимые параметры)

## База данных 

- заходить через http://localhost:6080/
- логин:root
- пфроль:1234

## Фронт 

- заходить http://localhost:3000/
