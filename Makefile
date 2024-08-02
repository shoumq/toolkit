.PHONY: up

up:
	cp .env.example .env
	docker-compose up -d
	docker exec -it project_app bash -c "composer install && \
	php artisan migrate && \
	php artisan db:seed && \
	php artisan vendor:publish --provider='PHPOpenSourceSaver\\JWTAuth\\Providers\\LaravelServiceProvider' && \
	php artisan jwt:secret"
