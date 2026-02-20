# Coalition Backend

Projekt backendowy stworzony w architekturze Hexagonalnej / Clean Architecture, przeniesiony z Laravela na Symfony.

## Cechy projektu

- **Framework Agnostic Domain**: Kod produkcyjny w folderze `src/` został przeniesiony z Laravela na Symfony bez zmian w logice domenowej.
- **Event Sourcing**: Zadania (`Task`) są zarządzane za pomocą Event Sourcingu.
- **Hexagonal Architecture**: Jasny podział na warstwy Domain, Application i Infrastructure.
- **CQRS**: Rozdzielenie operacji zapisu i odczytu.
- **PHP 8.4**: Wykorzystanie najnowszych funkcjonalności języka.

## Wymagania

- Docker i Docker Compose

## Szybki start

1. Uruchom kontenery:
   ```bash
   docker compose up -d
   ```

2. Projekt automatycznie uruchomi migracje dla bazy deweloperskiej (`app`) oraz testowej (`app_test`).

3. Aplikacja jest dostępna pod adresem `http://localhost:8080`.

## Testy i Jakość Kodu

Wszystkie narzędzia są dostępne wewnątrz kontenera `php`:

### Uruchamianie testów
```bash
docker exec backend-php-1 vendor/bin/phpunit
```

### Analiza statyczna (PHPStan poziom 9)
```bash
docker exec backend-php-1 vendor/bin/phpstan analyse
```

### Formatowanie kodu (PHP-CS-Fixer)
```bash
docker exec backend-php-1 vendor/bin/php-cs-fixer fix
```

## CI/CD

Projekt posiada skonfigurowany GitHub Actions (`.github/workflows/ci.yml`), który przy każdym Pull Requeście sprawdza:
- Formatowanie kodu (PHP-CS-Fixer)
- Analizę statyczną (PHPStan poziom 9)
- Testy automatyczne (PHPUnit) z wykorzystaniem bazy MySQL.
- Testy automatyczne (PHPUnit) z wykorzystaniem bazy MySQL.
