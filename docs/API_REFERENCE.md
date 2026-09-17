# Dotypos API Reference (docs.api.dotypos.com)

**API version:** v2 (all documented paths are under `/v2/...`)
**Reference scanned:** 2026-09-15, manually against https://docs.api.dotypos.com (MkDocs site, no OpenAPI/Swagger source available)

API base URL: `https://api.dotykacka.cz` (paths below are relative to
`/v2/clouds/:cloudId/...` unless noted otherwise).

---

## General conventions

Sources: `api-reference/general/filter/`, `.../sort/`, `.../paging/`, `.../etags/`,
`.../data-types/validation/`. These rules are not repeated in the per-domain
sections below — only noted there as "standard pagination/filter/sort, see the
section above."

### Filter

Parameter `filter`, syntax `filter=attribute|operation|value;attribute2|operation|value`
(multiple conditions joined with `;`).

| Group | Operations | Purpose |
|---|---|---|
| EQUALS | `eq`, `ne` | exact match |
| NUMBER | `gt`, `gteq`, `lt`, `lteq` | numeric comparison |
| STRING | `like` | case-insensitive substring search |
| ENUM | `in`, `notin` | set membership |
| BITS | `bin`, `bex` | bitwise operations on flags |

Examples: `price|gt|499.9`, `externalId|in|3,5`, `flags|bin|17` (bits set),
`flags|bex|15` (bits not set). The special values `null`/`notnull` work with
`eq`/`ne`/`in`/`notin` (`externalId|eq|null`, `externalId|notin|42,null`) — but are
not supported for the `deleted` attribute (a "not deleted" query already accounts for
`false` and `null`; for boolean filters use `0`/`1`). Date range:
`created|gteq|FROM;created|lt|TO` (inclusive-exclusive).

Which fields are filterable — see the field table for the specific domain (marked in
the documentation with a 📶 icon / list of operations next to the field).

### Sort

Parameter `sort`, comma-separated list: `sort=attribute,attribute2,-attribute3`. No
prefix means `ASC`, `-` means `DESC`. Multiple fields are applied in left-to-right
order, e.g. `sort=-created,name`.

### Paging

Query parameters: `page` (default 1, range `(1, ...)`), `limit` (default 20, range
`(1, 100)`).

The documentation shows a paginated response as a wrapper object:

```json
{
  "currentPage": 1,
  "perPage": 10,
  "totalItemsOnPage": 10,
  "totalItemsCount": 25,
  "firstPage": 1,
  "lastPage": 3,
  "nextPage": 2,
  "prevPage": null,
  "data": [ /* entities */ ]
}
```

**Important caveat from the documentation:** for some domains (explicitly named are
Order, OrderItem) `totalItemsCount`/`lastPage` are not computed, for performance
reasons — iterate until a page returns fewer items than `limit`, or a 404 is
returned. This is the official general pagination scheme from the `paging/` section,
but see the per-domain check below for each domain — in practice, the specific list
endpoints on entity pages in the documentation are shown as a **bare JSON array**
without this wrapper (see the notes in each section). It's possible the wrapper
applies only in some cases, or this is a mismatch between the general description and
the specific examples on the entity docs — the fact is recorded as observed.

### ETag

- `GET` — supports `If-None-Match`; on a match it returns `304 Not Modified`.
- `PUT`/`PATCH` — require `If-Match` with the current ETag; on a mismatch — an error
  (`409 Conflict` or `412 Precondition Failed`), if the header is missing —
  `428 Precondition Required`.
- `POST` (create) — no ETag required on the request.
- `DELETE` — `If-Match` is not yet mandatory ("temporal only," per the documentation
  it will become mandatory over time).
- The response returns an `ETag` header on all entity-based endpoint responses, which
  must be exposed via `Access-Control-Expose-Headers`.
- On a batch update (PUT/PATCH of multiple entities) — keep the same `id`s and order
  as in the original GET the ETag was taken from.

### Data types / validation

The `data-types/validation/` page does not give an explicit timestamp format (ISO8601
is not explicitly mentioned in the text); the page's focus is on regex validation for
specific fields:
- `vatId` — validated by a country-specific regex (EU).
- URL fields — two levels of validation: allowed characters in the URL + for
  `http`/`https` URLs the protocol is optional, but if given it must be `http`/`https`;
  leading whitespace is ignored; the validator "accepts URLs that are invalid by the
  strict standard."

The timestamp format, as observed in examples and field captions on the entity pages,
is uniformly labeled just `timestamp` with no explicit format — confirming it requires
an empirical check against a real API response, not just this page.

---

## Branch

- Doc: https://docs.api.dotypos.com/entity/branch/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/branches` |
  | GET | `/v2/clouds/:cloudId/branches/:branchId` |

  Read-only — there is no create/replace/delete for Branch in the documentation.
- Fields:
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | int? | required for PUT/PATCH (these operations don't exist in the API, but it's marked this way in the schema) |
  | `_cloudId` | int | |
  | `created` | timestamp? | |
  | `deleted` | boolean | cannot be `true` on POST/PUT/PATCH |
  | `display` | boolean | |
  | `features` | long | bitfield |
  | `flags` | short | bitfield, see below |
  | `name` | string(100) | |
  | `versionDate` | timestamp? | |

  `flags` (bits): 0 `SUBSTITUTING_BRANCH`, 1 `REPLACED_BRANCH`, 8 `HIDE_STOCK`,
  9 `HIDE_PRICES`, 10 `FREE_LICENSE`.
- Pagination/Filter/Sort: standard, on the list endpoint (`GET /branches`).
- ETag: present, on list and single GET (`If-None-Match`). Create/replace/delete are
  not applicable — these operations don't exist for Branch at all.
- Response shape: the documentation does not show an explicit example JSON response
  for the list endpoint (only a `{ // Response }` placeholder) — the shape (bare array
  vs. wrapper object with pagination) cannot be confirmed from the docs alone; flagged
  as **undetermined from the documentation**, to be checked empirically along with
  the rest.

---

## Customer

- Doc: https://docs.api.dotypos.com/entity/customer/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/customers` |
  | GET | `/v2/clouds/:cloudId/customers/:customerId` |
  | POST | `/v2/clouds/:cloudId/customers` |
  | PUT | `/v2/clouds/:cloudId/customers` (batch replace/create) |
  | PUT | `/v2/clouds/:cloudId/customers/:customerId` |
  | PATCH | `/v2/clouds/:cloudId/customers/:customerId` |
  | DELETE | `/v2/clouds/:cloudId/customers/:customerId` (query `anonymize` bool, default false) |
  | OPTIONS | `/v2/clouds/:cloudId/customers`, `/v2/clouds/:cloudId/customers/:customerId` |
- Fields:
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | required for PUT/PATCH, not null |
  | `_cloudId` | int | |
  | `_discountGroupId` | long? | |
  | `_sellerId` | long? | |
  | `firstName` | string(180) | at least one of firstName/lastName/companyName must be non-empty |
  | `lastName` | string(180) | see above |
  | `companyName` | string(180) | see above |
  | `email` | string(100) | |
  | `phone` | string(20) | |
  | `addressLine1` | string(180) | required |
  | `addressLine2` | string?(180) | |
  | `city` | string?(255) | |
  | `country` | string?(10) | country code |
  | `zip` | string(20) | |
  | `barcode` | string(50) | |
  | `companyId` | string(255) | CZ+SK: IČO (business ID), PL: REGON |
  | `vatId` | string(255) | CZ+SK: DIČ (VAT ID), PL: NIP; country-specific regex validation |
  | `companyId2` | string?(255) | SK only: VAT ID (VAT payers/non-payers, EU partners) |
  | `birthday` | timestamp? | |
  | `expireDate` | timestamp? | |
  | `created` | timestamp? | |
  | `versionDate` | timestamp? | |
  | `points` | double | ≥ 0 |
  | `flags` | long | bitfield |
  | `tags` | string[](255) | the characters `` , ^ ? * ( ) [ ] $ `` are forbidden |
  | `display` | boolean | |
  | `deleted` | boolean | cannot be `true` on POST/PUT/PATCH |
  | `hexColor` | string(7) | |
  | `internalNote` | string(1000) | |
  | `note` | string?(500) | |
  | `headerPrint` | string(256) | |
  | `modifiedBy` | string?(32) | |
  | `externalId` | string?(256) | |
- Pagination/Filter/Sort: standard, on the list endpoint.
- ETag: list GET and single GET — `If-None-Match`; PUT — `If-Match`; PATCH —
  `If-Match` required.
- Response shape: **bare array** — the documentation describes the list response as a
  bare JSON array of Customer objects (not a wrapper object with pagination). Batch
  POST/PUT also return arrays.

---

## Discount group

- Doc: https://docs.api.dotypos.com/entity/discount-group/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/discount-groups` |
  | GET | `/v2/clouds/:cloudId/discount-groups/:discountGroupId` |
  | POST | `/v2/clouds/:cloudId/discount-groups` |
  | PUT | `/v2/clouds/:cloudId/discount-groups` |
  | PUT | `/v2/clouds/:cloudId/discount-groups/:discountGroupId` |
  | PATCH | `/v2/clouds/:cloudId/discount-groups/:discountGroupId` |
  | DELETE | `/v2/clouds/:cloudId/discount-groups/:discountGroupId` |
  | OPTIONS | `/v2/clouds/:cloudId/discount-groups`, `.../:discountGroupId` |
- Fields:
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long? | required for PUT/PATCH |
  | `_cloudId` | int | |
  | `deleted` | boolean | cannot be `true` on POST/PUT/PATCH |
  | `discountPercent` | double | max 100 |
  | `display` | boolean | |
  | `externalId` | string | |
  | `name` | string(100) | |
  | `versionDate` | timestamp? | |
- Pagination/Filter/Sort: standard, on the list endpoint. Filterable operations on
  the fields are EQUALS/ENUM/NUMBER (both directions).
- ETag: list GET and single GET — `If-None-Match`; POST — no ETag; PUT/PATCH —
  `If-Match` (required for PATCH); DELETE — `If-Match` not yet mandatory (temporary).
- Response shape: the documentation marks the list endpoint's response schema as a
  **bare array** (`"Response schema" indicates a bare array structure for collection
  responses`).

---

## Order

- Doc: https://docs.api.dotypos.com/entity/order/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/orders` |
  | GET | `/v2/clouds/:cloudId/orders/:orderId` |
  | OPTIONS | `/v2/clouds/:cloudId/orders`, `.../:orderId` |

  Read-only — there is no create/replace/delete for Order in the documentation
  (orders are created through the POS, not through this API).
- Fields (main ones; the list is large, grouped here):
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | |
  | `created` | timestamp | |
  | `updated` | timestamp | |
  | `versionDate` | timestamp | |
  | `status` | enum | see the Order status table below |
  | `documentType` | enum | `RECEIPT`, `INVOICE`, `INVOICE_FROM_RECEIPTS`, `CORRECTIVE_INVOICE`, `EXTERNAL_INVOICE_PAYMENT`, `CASH_IN`, `CASH_OUT` |
  | `currency` | string(3) | currency code |
  | `totalValueRounded` | double | |
  | `points` | double | |
  | `tipAmount` | double? | |
  | `paid` | boolean | |
  | `_branchId` | int | |
  | `_cloudId` | int | |
  | `_customerId` | long? | |
  | `_employeeId` | long? | |
  | `_tableId` | long? | |
  | `_courseId` | long? | |
  | `_eetSubjectId` | long? | |
  | `_sellerId` | long? | |
  | `_sourceOrderId` | long? | |
  | `_relatedOrderId` | long? | marked deprecated in the documentation |
  | `_relatedInvoiceId` | long? | |
  | `parked` | boolean | |
  | `canceledDate` | timestamp? | |
  | `completed` | timestamp? | |
  | `flags` | int | bitfield, 30 defined bits (fiscal/VAT/state flags, including `CANCELED_PART`, `CANCELED_FULL`, `FISCALIZATION_REQUIRED`, `PAID_PART`, `DELIVERY`, `FISCALIZATION_SUCCESSFUL`, and others) |
  | `documentNumber` | string | |
  | `itemCount` | int | |
  | `guestCount` | int | |
  | `note` | string(1000) | |
  | `tags` | string[]? | |
  | `externalId` | string? | |
  | `bkp`, `pkp`, `fik` | string? | fiscalization data |
  | `merchantPrintData`, `printData` | string | |
  | `locationLatitude`, `locationLongitude`, `locationAccuracy` | double? | GPS |
  | `locationDate` | timestamp? | |

  **Order status enum** (page `api-reference/enums/order-status/`):
  | Value | Description |
  |---|---|
  | `new` | order just created, not yet parked |
  | `parked` | order created and parked (saved) |
  | `ready_to_pickup` | ready for on-site pickup |
  | `ready_for_delivery` | ready and waiting for delivery |
  | `on_delivery` | out for delivery |
  | `delivered` | delivered to the customer |
  | `delivery_failed` | delivery failed |
  | `canceled` | canceled |
  | `closed` | closed and handed over (for pickup/delivery — received/delivered to the customer) |
  | (fallback, no explicit string value in the docs) | "Unknown" — fallback status |
- Pagination/Filter/Sort: standard, on the list endpoint. Additionally: `namedFilter`
  (`openOrders`, `orderItems.openOrderItems` — used together with `include`) and
  `include` for nested entities (`orderItems`, `moneyLogs`).
- ETag: list GET and single GET — `If-None-Match`. Create/replace/delete not
  applicable (read-only).
- Response shape: `GET .../orders` is shown in the documentation as a **bare array**
  of Order objects with no pagination wrapper in the response body (pagination
  metadata for Order/OrderItem is not computed at all per the general `paging/`
  section — "omit total counts to optimize performance," iterate based on the actual
  item count per page).

---

## Order item

- Doc: https://docs.api.dotypos.com/entity/order-item/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/order-items` |
  | GET | `/v2/clouds/:cloudId/order-items/:orderItemId` |
  | OPTIONS | `/v2/clouds/:cloudId/order-items`, `.../:orderItemId` |

  Read-only.
- Fields (main ones):
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | |
  | `_orderId` | long | |
  | `_productId` | long | |
  | `_branchId` | int | |
  | `_cloudId` | int | |
  | `_employeeId` | long | |
  | `_customerId` | long? | |
  | `_categoryId` | long | |
  | `_courseId` | long? | |
  | `_eetSubjectId` | long? | |
  | `_relatedOrderItemId` | long? | |
  | `_sellerId` | long? | |
  | `quantity` | double | |
  | `unitPriceWithVat` / `unitPriceWithoutVat` | double | price before discount |
  | `billedUnitPriceWithVat` / `billedUnitPriceWithoutVat` | double | per-unit price after discount |
  | `totalPriceWithVat` / `totalPriceWithoutVat` | double | accounting for quantity |
  | `discountPercent` | double | |
  | `discountPermitted` | boolean | |
  | `vat` | double | VAT rate |
  | `unitPurchasePrice` | double | per-unit cost price |
  | `currency` | string(3) | |
  | `name` | string | |
  | `alternativeName` | string? | |
  | `subtitle` | string | |
  | `note` | string | |
  | `unit` | enum | see the Units enum below |
  | `ean` | string[] | |
  | `tags` | string[] | |
  | `onSale` | boolean | |
  | `packaging` | double | |
  | `points` | double | |
  | `created` | timestamp | date the item was added to the Order |
  | `updated` | timestamp | |
  | `versionDate` | timestamp | |
  | `completed` | timestamp? | Order completion date |
  | `canceledDate` | timestamp? | item cancellation date |
  | `parked` | boolean | |
  | `preparationDuration` | int? | preparation time, seconds |
  | `stockDeduct` | boolean | |
  | `flags` | int | bitfield |
  | `orderItemCustomizations` | array | nested object, see below |

  **`orderItemCustomizations[]`:**
  | Field | Type |
  |---|---|
  | `id` | long |
  | `_orderItemId` | long |
  | `_productCustomizationId` | long |
  | `_productId` | long |
  | `_orderId` | long |
  | `_branchId` | int |
  | `_cloudId` | int |
  | `name` | string |
  | `alternativeName` | string? |
  | `quantity` | double |
  | `unit` | enum (Units) |
  | `unitPriceWithVat` | double |
  | `vat` | double |
  | `purchasePriceWithoutVat` | double |
  | `discountValue` | double |
  | `preparationDuration` | int? |
  | `defaultSelection` | string |
  | `created` | timestamp |
  | `canceledDate` | timestamp? |
  | `versionDate` | timestamp |
  | `flags` | long |

  **Units enum** (page `api-reference/enums/units/`) — the documentation's HTML output
  only gives descriptive (human-readable) names, not the literal JSON codes; **the
  exact identifier spelling (casing/format) is not exposed** by a plain text scrape of
  the page and needs to be checked empirically against a real API response, or against
  the page's source HTML/JS:
  - Count: Piece
  - Weight: Milligram, Decagram, Gram, Kilogram, Pound, Ounce, Quintal, Tonne
  - Length: Millimeter, Centimeter, Meter, Kilometer, Inch, Mile
  - Area: SquareMeter, SquareFoot
  - Volume: Milliliter, Deciliter, Centiliter, Liter, UsGallon, UkGallon, Hectoliter, CubicMeter, CubicFoot
  - Time: Second, Minute, Hour, Day
  - Point: Points
- Pagination/Filter/Sort: standard, on the list endpoint. Filterable/sortable fields
  are explicitly marked in the documentation: `_branchId`, `_categoryId`, `_cloudId`,
  `_courseId`, `_customerId`, `_eetSubjectId`, `_employeeId`, `_orderId`,
  `_productId`, `_relatedOrderItemId`, `_sellerId` (EQUALS/ENUM); `canceledDate`,
  `completed`, `created`, `updated`, `versionDate` (EQUALS/ENUM/NUMBER, both
  directions); `name`, `subtitle` (STRING, `like`); `discountPermitted`, `onSale`,
  `parked`, `stockDeduct` (EQUALS/ENUM); `ean`, `tags` (EQUALS/ENUM); `flags` (BITS).
- ETag: list GET and single GET — `If-None-Match`. Read-only, create/replace/delete
  not applicable.
- Response shape: **bare array** of OrderItem objects, each with a nested
  `orderItemCustomizations` array.

---

## Reservation

- Doc: https://docs.api.dotypos.com/entity/reservation/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/reservations` |
  | GET | `/v2/clouds/:cloudId/reservations/:reservationId` |
  | POST | `/v2/clouds/:cloudId/reservations` |
  | PUT | `/v2/clouds/:cloudId/reservations` (batch) |
  | PUT | `/v2/clouds/:cloudId/reservations/:reservationId` |
  | PATCH | `/v2/clouds/:cloudId/reservations/:reservationId` |
  | DELETE | `/v2/clouds/:cloudId/reservation/:reservationId` — **note: singular `reservation`, not `reservations`, as written in the documentation** |
  | OPTIONS | `/v2/clouds/:cloudId/tables` (as written in the documentation — likely a typo in the docs, `/reservations` would be expected), `/v2/clouds/:cloudId/reservations/:reservationId` |
- Fields:
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | required for PUT/PATCH |
  | `_branchId` | int | |
  | `_cloudId` | int | |
  | `_customerId` | long | |
  | `_employeeId` | long | |
  | `_tableId` | long | |
  | `status` | enum | `NEW`, `CONFIRMED`, `CANCELLED` |
  | `startDate` | timestamp | |
  | `endDate` | timestamp | |
  | `created` | timestamp? | |
  | `versionDate` | timestamp? | |
  | `seats` | short | min 1, ≤ `Table.seats` |
  | `note` | string? | |
  | `flags` | int | |
- Pagination/Filter/Sort: standard, on the list endpoint. Batch operations
  (POST/PUT) — maximum 100 items per request.
- ETag: `If-None-Match` on GET (list and single); `If-Match` on PUT/PATCH/DELETE.
- **Response shape:** the documentation explicitly describes the
  `GET .../reservations` list response as a **bare JSON array**
  (`[{ Reservation schema }, ...]`), **with no pagination wrapper object**.

---

## Table

- Doc: https://docs.api.dotypos.com/entity/table/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/tables` |
  | GET | `/v2/clouds/:cloudId/tables/:tableId` |
  | OPTIONS | `/v2/clouds/:cloudId/tables`, `.../:tableId` |

  Read-only — there is no create/replace/delete for Table in the documentation.
- Fields:
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | required for PUT/PATCH (these operations don't exist for Table in the docs, but the field is marked this way in the schema) |
  | `_branchId` | int | |
  | `_cloudId` | int | |
  | `_tableGroupId` | long | |
  | `_sellerId` | long | |
  | `display` | boolean | |
  | `enabled` | boolean | |
  | `locationName` | string | |
  | `name` | string(180) | required |
  | `positionX`, `positionY`, `rotation` | int | |
  | `seats` | int | |
  | `tags` | string[] | |
  | `type` | enum | `SQUARE`, `SQUARE6`, `CIRCLE2`, `CIRCLE4`, `DELIVERY`, `CHAIR_SINGLE`, `ROUND`, `DOOR`, `GENERIC`, `CAR1`, `CAR2` |
  | `versionDate` | timestamp | |
- Pagination/Filter/Sort: standard, on the list endpoint.
- ETag: `If-None-Match` support on both the list and single GET endpoints for Table —
  the resource is versioned via ETag.
- Response shape: **bare array** of Table objects in the list response (no pagination
  wrapper).

---

## Warehouse

- Doc: https://docs.api.dotypos.com/entity/warehouse/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/warehouses` |
  | GET | `/v2/clouds/:cloudId/warehouses/:warehouseId` |
  | POST | `/v2/clouds/:cloudId/warehouses` |
  | PUT | `/v2/clouds/:cloudId/warehouses/:warehouseId` |
  | PUT | `/v2/clouds/:cloudId/warehouses` (batch) |
  | PATCH | `/v2/clouds/:cloudId/warehouses/:warehouseId` |
  | DELETE | `/v2/clouds/:cloudId/warehouses/:warehouseId` |
  | GET | `/v2/clouds/:cloudId/warehouses/:warehouseId/products` — list of products with warehouse stock status |
  | GET | `/v2/clouds/:cloudId/warehouses/:warehouseId/products/:productId` — a product with warehouse stock status |
  | POST | `/v2/clouds/:cloudId/warehouses/:warehouseId/stockups` — stock intake |
  | POST | `/v2/clouds/:cloudId/warehouses/:warehouseId/transfers` — transfer between warehouses |
  | POST | `/v2/clouds/:cloudId/warehouses/:warehouseId/sales` — product sale (inventory accounting) |
  | POST | `/v2/clouds/:cloudId/warehouses/:warehouseId/stock-takings` — stock-taking/inventory count |
  | POST | `/v2/clouds/:cloudId/warehouses/:warehouseId/stock-taking-dates` — dates of the most recent stock-takings |

- Fields (Warehouse):
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | required for updates |
  | `_cloudId` | int | |
  | `name` | string(180) | filterable/sortable |
  | `enabled` | boolean | |
  | `barcode` | string(180) | |
  | `hexColor` | string(7) | |
  | `deleted` | boolean | read-only on create/update |
  | `versionDate` | timestamp | |

  Fields of "Product with stock status" (extends Product): `_warehouseId` (long),
  `stockQuantityStatus` (double), `purchasePriceWithoutVat` (double?),
  `stockStatusVersiondate` (timestamp).
- Pagination/Filter/Sort: standard, on the GET list endpoints.
- ETag: `If-None-Match` on GET (list and single); `If-Match` on create/update/delete.
- Response shape: **bare array** on the warehouse GET list endpoints
  (`[{...}, {...}]`). Separately: `stock-taking-dates` (POST) also returns a bare
  array of objects with the fields `_productId` and `lastStockTakingDate`.

---

## Webhook

- Doc: https://docs.api.dotypos.com/others/webhook/
- Endpoints:
  | Method | Path |
  |---|---|
  | GET | `/v2/clouds/:cloudId/webhooks` |
  | POST | `/v2/clouds/:cloudId/webhooks` |
  | DELETE | `/v2/clouds/:cloudId/webhooks/:webhookId` |

  No single GET and no PUT/PATCH for Webhook — only list/create/delete.
- Fields:
  | Field | Type | Notes/enum |
  |---|---|---|
  | `id` | long | |
  | `_cloudId` | int | |
  | `_warehouseId` | long? | |
  | `method` | enum | `POST`, `GET` |
  | `url` | string | regex-validated |
  | `payloadEntity` | enum | `STOCKLOG`, `POINTSLOG`, `PRODUCT`, `ORDERBEAN`, `RESERVATION`, `CUSTOMER` |
  | `payloadVersion` | enum | `V1` |
  | `versionDate` | timestamp? | |
- Pagination/Filter/Sort: **not supported** (not explicitly mentioned in the
  documentation for Webhook).
- ETag: **not mentioned in the documentation** for Webhook.
- Response shape: the list response is an array of Webhook objects (schema as above);
  there's no explicit "bare array vs. wrapper" note on the page, but given the
  overall shape (no pagination at all) it should be read as a bare array.

---

## Authorization / OAuth2

- Doc: https://docs.api.dotypos.com/authorization/
- Not a CRUD domain — a two-step flow for obtaining a token, plus request
  authorization itself.

### Step 1 — obtaining a Refresh Token (user consent)

- `POST https://admin.dotykacka.cz/client/connect/v2`
- Parameters (form data):
  | Field | Notes |
  |---|---|
  | `client_id` | application identifier |
  | `timestamp` | Unix time in seconds |
  | `signature` | HMAC-SHA256(`timestamp` as a string, key = `client_secret`), 64-character hex |
  | `scope` | as of the scan, only `*` is supported |
  | `redirect_uri` | where to redirect after authorization |
  | `state` | optional, CSRF token |
- Response: redirect to `redirect_uri?token={refreshToken}&cloudid={cloudId}&state={state}`.

### Step 2 — exchanging the Refresh Token for an Access Token

- `POST https://api.dotykacka.cz/v2/signin/token`
- Header: `Authorization: User {refreshToken}`
- Request body (JSON): `{"_cloudId": "{cloudId}"}`
- Response: `{"accessToken": "eyJ..."}`. Default lifetime — 1 hour (not guaranteed).
- Without a `cloudId` (empty JSON `{}`) — access is limited to the cloud listing
  endpoint.
- For multiple clouds — a separate Access Token request per `_cloudId`.

### Step 3 — API requests

- Header: `Authorization: Bearer {accessToken}` on all authenticated endpoints.

### Pagination/Filter/Sort/ETag

Not applicable — these are not entity endpoints, but an internal auth flow.

---

## Summary table: list-endpoint response shape (bare array vs. wrapper)

A lightweight check of the response shape for each list endpoint, as shown in the
documentation (not as observed from a live API):

| Domain | List-response shape per the documentation |
|---|---|
| Branch | not shown explicitly (only a `{ // Response }` placeholder) — unconfirmed |
| Customer | bare array |
| Discount group | bare array |
| Order | bare array |
| Order item | bare array |
| Reservation | bare array |
| Table | bare array |
| Warehouse | bare array |

The general `paging/` documentation section describes a wrapper object with fields
like `currentPage` / `data` etc. (see "General conventions" above), but none of the
checked entity pages show a JSON response example with such a wrapper — it's either
a bare array everywhere, or (Branch) no example is given at all. Possible
explanations (unverified, hypotheses only): the wrapper describes some other/legacy
response mode, or the entity pages simply don't include full pagination metadata in
their examples. Confirming this requires a real `MockHttpClient`/live request against
the API, not documentation alone.
