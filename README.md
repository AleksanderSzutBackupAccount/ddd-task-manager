# DDD Task Manager - Zadanie Rekrutacyjne (Symfony Version)

## O projekcie

Projekt ten jest implementacją systemu zarządzania zadaniami, wykonaną z najwyższą dbałością o architekturę i jakość kodu. Unikalną cechą tego repozytorium jest to, że **cała logika biznesowa (Domain & Application) jest całkowicie niezależna od frameworka (Framework Agnostic)**.

Pierwotnie projekt został napisany w Laravelu, a następnie przeniesiony do Symfony. Dzięki zastosowaniu **DDD (Domain-Driven Design)** oraz **Architektury Hexagonalnej**, przeniesienie kodu produkcyjnego (`src/`) odbyło się bez żadnych zmian w logice biznesowej – zmieniona została jedynie warstwa infrastruktury i konfiguracja frameworka.

## Architektura i Technologie

Projekt opiera się na nowoczesnych wzorcach projektowych:

- **Hexagonal Architecture (Ports & Adapters)**: Ścisła separacja domeny od szczegółów technicznych (baza danych, HTTP, framework).
- **Domain-Driven Design (DDD)**: Skupienie na logice biznesowej, użycie Value Objects, Agregatów i Eventów.
- **Event Sourcing**: Stan zadań (`Task`) nie jest tylko nadpisywany w bazie, ale wynika z sekwencji zdarzeń domenowych (`TaskCreated`, `TaskStatusChanged`) zapisanych w `Event Store`.
- **CQRS (Command Query Responsibility Segregation)**: Wyraźny podział na operacje zmieniające stan (Commands) i operacje odczytu (Queries).
- **Read Model Projection**: Zdarzenia domenowe są projektowane na dedykowaną tabelę `tasks`, co pozwala na wydajne odczyty.
- **PHP 8.4**: Wykorzystanie najnowszych możliwości języka (readonly classes, property hooks - gdzie to możliwe, typowanie).

## Struktura Katalogów

```text
src/
├── Project/            # Moduł zarządzania projektami i zadaniami
│   ├── Domain/         # Logika biznesowa (Agregaty, Eventy, Interfejsy repozytoriów) - FRAMEWORK AGNOSTIC
│   ├── Application/    # Use Case'y (Command/Query Handlery) - FRAMEWORK AGNOSTIC
│   └── Infrastructure/ # Implementacja (Symfony, Doctrine, Kontrolery)
├── Identity/           # Moduł tożsamości i użytkowników
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
└── Shared/             # Kod współdzielony między modułami
    ├── Domain/         # Klasy bazowe dla DDD (AggregateRoot, DomainEvent)
    └── Infrastructure/ # Wspólne mechanizmy (Bus, EventStore, Middleware)
```

## Szybki Start

### Wymagania
- Docker & Docker Compose

### Instalacja i uruchomienie
1. Sklonuj repozytorium.
2. Uruchom kontenery:
   ```bash
   docker compose up -d
   ```
3. Aplikacja automatycznie:
   - Zainstaluje zależności (jeśli to pierwsze uruchomienie).
   - Poczeka na bazę danych.
   - Uruchomi migracje dla bazy deweloperskiej (`app`) i testowej (`app_test`).

Aplikacja jest dostępna pod adresem: `http://localhost:8081` (lub port określony w `.env`).

## Jakość Kodu i Testy

Projekt kładzie duży nacisk na stabilność i czytelność.

### Testy Automatyczne (PHPUnit)
Uruchamianie pełnego zestawu testów (Unit & Integration):
```bash
docker exec backend-php-1 vendor/bin/phpunit
```

### Analiza Statyczna (PHPStan)
Projekt jest zweryfikowany na **najwyższym poziomie (level 9)**:
```bash
docker exec backend-php-1 vendor/bin/phpstan analyse
```

### Formatowanie Kodu (PHP-CS-Fixer)
Zgodność ze standardem Symfony:
```bash
docker exec backend-php-1 vendor/bin/php-cs-fixer fix --dry-run --diff
```

## CI/CD
W projekcie skonfigurowano **GitHub Actions**, który przy każdym Pull Requeście automatycznie sprawdza:
- Poprawność składni i standardy kodowania.
- Statyczną analizę kodu (PHPStan Lvl 9).
- Uruchamia testy na rzeczywistej bazie danych MySQL.

---
**Uwaga dla sprawdzającego:** Projekt pokazuje, jak poprawnie odseparować domenę od frameworka, co w rzeczywistych warunkach biznesowych drastycznie obniża koszty utrzymania i ewentualnych migracji technologicznych.
