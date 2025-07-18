## src/Controller/GetProductsController.php
- Заменили прямой SQL-код на вызов `ProductRepository::getByCategory()`.
- Получаем категорию из `$request->getQueryParams()['category']`.
- Инжектируем `ProductRepository` и `ProductsView`, не работаем с сырыми массивами.
- Формируем единый `JsonResponse` со статусом 200.

---

## src/Controller/GetCartController.php
- Метод `get()` переименован в `fetchCart()`, чтобы отражать возвращаемую сущность.
- Извлекаем `cartUuid` из query и `Customer` из атрибута авторизации.
- Вызываем `CartManager::getCart()`, который возвращает `?Cart`.
- Если корзина не найдена (`null`) — возвращаем 404 с JSON `{ message: 'Cart not found' }`.
- Если найдена — возвращаем 200 и `CartView->toArray()`.

---

## src/Controller/AddToCartController.php
- Метод `post()` теперь `addProduct()`, название чётко описывает действие.
- Читаем тело через `$request->getParsedBody()`, убрали `getAttribute('customer')`.
- Инжектируем `ProductRepository`, `CartManager`, `CartView`.
- Контроллер не создаёт `CartItem` — передаёт всё в `CartManager::addToCart()`.
- Возвращаем единый `JsonResponse` с `{ status: 'success', cart: … }`.

---

## src/Service/CartManager.php
- Убрали наследование от `ConnectorFacade` — добавили `CartRepository` через DI.
- `getCart(uuid, customer)`:
  - Запрашивает `CartRepository->fetch()`, получает `?Cart`.
  - При `null` — логирует info и создаёт новую `Cart` с `defaultPaymentMethod`.
  - При реальных ошибках Redis — логирует error и пробрасывает `ConnectorException`.
- `addToCart(productUuid, quantity, customer, ?cartUuid)`:
  - Берёт `Product` через `ProductRepository->getByUuid()`.
  - Получает или генерирует `cartUuid`, достаёт/создаёт корзину.
  - Добавляет `CartItem` и вызывает `CartRepository->save()`.

---

## src/Repository/CartRepository.php
- Прокладка между `CartManager` и `Connector`.
- `fetch(uuid): ?Cart` — возвращает `Cart` или `null`, больше не кидает 404.
- `save(cart): void` — сериализует и пишет через `Connector`.

---

## src/Repository/ProductRepository.php
- Избавились от небезопасной конкатенации.
- Используем `Connection::executeQuery($sql, [$param])` → `Result->fetchAssociative()` / `fetchAllAssociative()`.
- `getByUuid(uuid)`: если нет записи — бросаем `RuntimeException`.
- `getByCategory(category)`: возвращаем массив `Product::fromArray()`.

---

## src/Infrastructure/Connector.php
- `get(key): ?Cart`:
  - При RedisException — логируем и бросаем `ConnectorException(503)`.
  - Если ключа нет — возвращаем `null`.
  - Иначе — `json_decode()` → `Cart::fromArray()`.
- `set(key, cart): void` — `json_encode()` + `setex()`, при ошибках — `ConnectorException(503)`.
- Вернули метод `has(key): bool` для проверки наличия без исключений.

---

## src/Infrastructure/ConnectorFacade.php
- Собирает `Redis` в конструкторе: `connect()`, `auth()`, `select()`.
- Создаёт `Connector($redis, $this->getLogger(), $ttl)`.
- Слои сервисов наследуют, реализуя только `getLogger()`.

---

## src/Domain/Cart*.php / Customer.php / Product.php
- Все модели на PHP8 property-promotion + `readonly` для неизменяемых полей.
- `CartItem` поле `product` теперь объект `Product`, а не UUID.
- Добавлены `toArray()` и `fromArray()` везде для чистой сериализации.

---

### Результат
- Контроллеры: только HTTP → сервисы.
- Сервисы: бизнес-логика через репозитории.
- Репозитории: доступ к данным (MySQL/Redis).
- Инфраструктура: подключение и обработка Redis.
- Доменные модели: неизменяемые и сериализуемые.
- Код безопасен от SQL-инъекций, легко тестируется и расширяется.  