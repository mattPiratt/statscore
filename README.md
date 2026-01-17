# Football Events Application

This is a demonstration application used for recording match events (fouls, goals) and tracking team statistics in
real-time.

## About the project

The project was created to showcase a modern approach to software architecture and engineering best practices. The main
assumptions are:

* **Hexagonal Architecture (Ports & Adapters):** Clear separation of business logic from infrastructure (file system,
  system clock). This allows for easy replacement of components (e.g., changing storage from file-based to a SQL
  database) without affecting the domain.
* **CQRS (Command Query Responsibility Segregation):** Separation of write operations (`Command`) from read operations (
  `Query`), which improves application readability and scalability.
* **Rich Domain Model:** Business logic and invariants are encapsulated within domain objects (`Value Objects`,
  `Domain Events`).
* **Clean Code & Patterns:** Application of patterns such as `Strategy` (for updating statistics), `Factory`, and
  `Composition Root`.
* **Test-Driven Development:** A complete set of unit, integration, and API tests (PHPUnit, Codeception).

### What was intentionally omitted (Next Steps)

The application serves as a foundation for an Enterprise-class system; however, due to time constraints, some elements
have been simplified:

* **DI Container:** Dependencies are manually injected in `Kernel.php` (Composition Root). In a target system, a
  container like `Symfony DI` or `PHP-DI` would be used.
* **Event Bus / Message Broker:** Statistics updates occur synchronously. At a production scale, it would be beneficial
  to introduce asynchronicity (e.g., RabbitMQ, Redis) and a full `Event-Driven Architecture`.
* **Read Model Persistence:** The read model is currently generated dynamically from statistics data.
* **Database:** `FileStorage` is currently used. Ports are prepared for implementations using `Doctrine` or `PDO`.

## Requirements

- Docker
- Docker Compose

## Installation and Setup

1. Build and run the container:

```bash
docker compose up --build -d
```

2. Build and run the container:

```bash
docker exec -it football_events_app composer install
```

3. The application will be available at: `http://localhost:8000`

## Usage

### Foul Event

Send a POST request with a foul event:

```bash
curl -X POST http://localhost:8000/event \
  -H "Content-Type: application/json" \
  -d '{"type": "foul", "player": "William Saliba", "team_id": "arsenal", "match_id": "m1", "minute": 45, "second": 34}'
```

### Example Response

Both events return a similar response structure:

```json
{
  "status": "success",
  "message": "Event saved successfully",
  "event": {
    "type": "foul",
    "timestamp": 1729599123,
    "data": {
      "type": "foul",
      "player": "William Saliba",
      "team_id": "arsenal",
      "match_id": "m1",
      "minute": 45,
      "second": 34
    }
  }
}
```

### Statistics Endpoint

Get team statistics for a specific match:

```bash
curl "http://localhost:8000/statistics?match_id=m1&team_id=arsenal"
```

Get all team statistics for a match:

```bash
curl "http://localhost:8000/statistics?match_id=m1"
```

Example response:

```json
{
  "match_id": "m1",
  "team_id": "arsenal",
  "statistics": {
    "fouls": 2
  }
}
```

Foul events automatically update team statistics (fouls counter) for the specified team in the given match.

## Tests

### PHPUnit Tests

Run PHPUnit tests inside the container:

```bash
docker exec -it football_events_app vendor/bin/phpunit tests
```

Or after entering the container:

```bash
docker exec -it football_events_app bash
vendor/bin/phpunit tests
```

### Codeception API Tests

Run Codeception API tests for comprehensive endpoint testing:

```bash
docker exec -it football_events_app vendor/bin/codecept run Api
```

Run all Codeception tests:

```bash
docker exec -it football_events_app vendor/bin/codecept run
```

### Test Coverage

The project includes:

- **Unit tests** (PHPUnit): Test individual classes and methods
- **API tests** (Codeception): Test HTTP endpoints and responses
- **Integration tests**: Test complete workflows including statistics tracking

## Project Structure

```
.
├── Dockerfile
├── docker-compose.yml
├── composer.json
├── phpunit.xml
├── public/
│   └── index.php          # Entry point
├── src/
│   ├── Kernel.php         # Composition Root & App Lifecycle
│   ├── Presentation/      # Presentation Layer (Http)
│   ├── Shared/            # Shared components (Clock, Utils)
│   └── Statistics/        # Statistics Domain Module
│       ├── Application/   # CQRS Handlers (Commands/Queries)
│       ├── Domain/        # Business Logic (Model, Events, Strategies)
│       └── Infrastructure/# Persistence (Storage, Repositories)
├── tests/
│   ├── Unit/              # Unit tests
│   ├── Api/               # Codeception API tests
│   └── Support/           # Test helpers
└── storage/               # File storage
```

