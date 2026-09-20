# Dotypos API Reference (docs.api.dotypos.com)

**API version:** v2 (all documented paths are under `/v2/...`). API v1, the OMS API and
the API v1→v2 migration pages are a different/legacy surface and are intentionally not
covered here.
**Reference scanned:** 2026-09-20, manually against https://docs.api.dotypos.com (a
Mintlify/MkDocs-style site, no OpenAPI/Swagger source available). Covers every page
under Guides, API Reference (General/Enums/Entity — all 30 entities), POS Actions and
Others (except Release notes and Third-party libraries, see the notes in those
sections for why).

API base URL: `https://api.dotykacka.cz` (paths below are relative to
`/v2/clouds/:cloudId/...` unless noted otherwise).

---

## General conventions

### Recommended headers

| Header | Value |
|---|---|
| `Accept` | `application/json` |
| `Content-Type` | `application/json` |

### Generic CRUD template

Every entity below follows this template unless its own section says otherwise
(missing verbs, extra path/query params, etc.):

| Method | Path | Notes |
|---|---|---|
| GET | `/:entity/:entityId` | Get a single entity by ID. `If-None-Match` supported. |
| GET | `/:entity` | Paginated, filtered, sorted list. Without a `filter` on `deleted`, only non-deleted entities are returned. |
| POST | `/:entity` | Create a list of new entities (body: array). No ETag needed. |
| PUT | `/:entity/:entityId` | Replace a single entity. `If-Match` required when updating, ignored when inserting a new one under a given ID. |
| PUT | `/:entity` | Replace/create a list of entities (body: array). Same `If-Match` rule as single PUT. |
| PATCH | `/:entity/:entityId` | Partial update, only sent fields change. `If-Match` **required**. |
| DELETE | `/:entity/:entityId` | `If-Match` currently ignored ("temporary", documented as becoming mandatory later). |
| OPTIONS | `/:entity`, `/:entity/:entityId` | |

For endpoints working with a list of entities (POST/PUT), the response order matches
the request order. For PUT/PATCH batches, keep the same `id`s and order as in the
original GET the ETag was taken from.

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

Each entity's field table below has a **Filter** column listing exactly which of the
five groups above are allowed on that field (as marked with the 📶 icon on the site);
`—` means the field is not filterable at all.

### Sort

Parameter `sort`, comma-separated list: `sort=attribute,attribute2,-attribute3`. No
prefix means `ASC`, `-` means `DESC`. Multiple fields are applied in left-to-right
order, e.g. `sort=-created,name`.

Each entity's field table below has a **Sort** column (the 🔽 icon on the site):
`BOTH` (ascending and descending both allowed), `NONE` (marked explicitly as not
sortable), or `—` (the site shows no sort marker for the field at all).

### Paging

Query parameters: `page` (default 1, range `(1, ...)`), `limit` (default 20, range
`(1, 100)`).

The general Paging page documents a paginated response as a wrapper object:

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

**Important caveat:** for some domains (Order, Order item are explicitly named)
`totalItemsCount`/`lastPage` are not computed, for performance reasons — iterate until
a page returns fewer items than `limit`, or a 404 is returned (see the Breaking
changes/BC1 note below for what a 404-vs-empty-200 response means for an empty
result). This is the official general pagination scheme from the Paging page, but
every entity page's own list-endpoint example in this reference shows a **bare JSON
array**, without this wrapper — recorded as observed; it's possible the wrapper
applies only in some circumstances not otherwise documented.

### ETag

- `GET` — supports `If-None-Match`; on a match it returns `304 Not Modified`.
- `PUT`/`PATCH` — require `If-Match` with the current ETag; on a mismatch — `409
  Conflict` or `412 Precondition Failed`; if the header is missing on a required
  case — `428 Precondition Required`.
- `POST` (create) — no ETag on the request.
- `DELETE` — `If-Match` is not yet mandatory ("temporary," documented as becoming
  mandatory over time).
- The response returns an `ETag` header on all entity-based endpoint responses, which
  must be exposed via `Access-Control-Expose-Headers`.
- On a batch update (PUT/PATCH of multiple entities), keep the same `id`s and order
  as in the original GET the ETag was taken from.

### Schema / data types

- All internal IDs have a leading underscore, e.g. `_cloudId` — these are literal
  JSON field names, not a typo.
- Properties typed with a trailing `?` are optional; everything else is mandatory
  unless a footnote says otherwise.
- Data type notation: `string(400)` = max length 400; `string(200,250)` = length
  between 200 and 250; `string[]` = array of strings; `string[1,100]` = array with
  1–100 items; `string[](1000)` = array of strings with max length 1000 once joined.
- **Contradiction in the documentation, recorded as observed, not resolved by
  guessing:** the Schema page states "All numeric properties are represented as
  string in JSON" (i.e. every numeric field is expected to arrive as a JSON string,
  e.g. `"id": "100"`), but the ETag examples page shows a real response example with
  `"id": 1` as a plain JSON number. Both are official documentation pages and they
  disagree. Every JSON example throughout this reference (as scraped from each
  entity's own page) shows plain unquoted numbers, matching the ETag-examples style —
  but a consumer of this API should be prepared to accept either representation for
  any field documented as numeric, since the Schema page explicitly claims strings
  are also valid.
- The `data-types/validation/` page does not give an explicit timestamp format
  (ISO8601 is not mentioned); all entity pages just label date/time fields
  `timestamp` with no format example. This must be confirmed empirically against a
  real API response.
- `vatId` fields are validated by a country-specific regex (EU).
- URL fields get two levels of validation: allowed characters in the URL, and for
  `http`/`https` URLs the protocol is optional but if given must be `http`/`https`;
  leading whitespace is ignored; the validator "accepts URLs that are invalid by the
  strict standard."

### Prices

- Most price values are stored as double precision (≈15 decimal digits).
- **Do not round price values** when creating/updating an entity. Prices are rounded
  only for display in the Dotykacka application; storing rounded values causes
  compounding rounding errors on reports over many items/a long time interval.
  Example given: a product sold at 50 CZK with 21% VAT has an unrounded
  price-without-VAT of `41.3223140495868`; selling 1000 units gives exactly 50,000
  CZK. Rounding that price to `41.32` and selling 1000 units instead gives 49,997 CZK
  — a 3 CZK discrepancy from rounding alone.

### Flags

Flags are one or more booleans packed into a single signed numeric field. Bit numbers
are counted from the least significant bit (bit 0). Example: `flags = 257 = 2^8 +
2^0` means bits 0 and 8 are set, everything else unset.

- Get a bit's value: `1 << bit` (e.g. bit 8 → `256`).
- Check if set: `(flags & (1 << bit)) != 0`.
- Set a bit while preserving the rest: `flags |= (1 << bit)`.
- Clear a bit while preserving the rest: `flags &= ~(1 << bit)`.
- Toggle a bit while preserving the rest: `flags ^= (1 << bit)`.
- **Warning:** when changing a single flag via PUT/PATCH you must resend the full
  flags value with the other bits preserved — sending just the one bit's value
  clears every other flag.
- The API uses signed integer types for flags fields, so a negative value can appear
  if the most-significant bit is set.

Each entity's own Flags table (where it has one) is listed in its section below.

### Methods — HTTP status codes

**2xx**

| Code | Meaning | Reason |
|---|---|---|
| 200 | OK | Any method finished successfully |
| 201 | Created | POST inserted new data successfully |

**3xx**

| Code | Meaning | Reason |
|---|---|---|
| 304 | Not Modified | GET with `If-None-Match`, entity unchanged |

**4xx**

| Code | Meaning | Reason |
|---|---|---|
| 400 | Bad Request | Invalid body/query/path parameter, or an entity failed validation |
| 401 [1] | Authentication error | Missing or invalid `Authorization` header |
| 403 [1] | Authorization error | Invalid/expired access token (JWT), or insufficient permissions |
| 404 | Invalid or non-existent resource ID | GET found no matching entity |
| 405 | Method Not Allowed | |
| 409 | Conflict | PUT/PATCH fails `versionDate` verification |
| 412 | Precondition Failed | ETag: PUT/PATCH `If-Match` doesn't match |
| 428 | Precondition Required | ETag: PUT/PATCH called without `If-Match` |
| 429 | Too Many Requests | |

[1] 401/403 error bodies carry an extra `reason` enum field:

| `reason` | `status` | Meaning |
|---|---|---|
| `INVALID_AUTH_HEADER` | 401 | `Authorization` header missing or invalid |
| `INVALID_REFRESH_TOKEN` | 401 | Refresh token invalid/not found |
| `BAD_REGISTRATION` | 401 | Internal error — contact support |
| `UNAUTHORIZED` | 401 | Not authorized, no specific reason |
| `AUTHENTICATION_FAILED` | 401 | Generic authentication error — contact support |
| `INVALID_ACCESS_TOKEN` | 403 | Access token can't be parsed / signature invalid |
| `ACCESS_TOKEN_EXPIRED` | 403 | Access token expired — sign in again |
| `CLOUD_FORBIDDEN` | 403 | Access to the given cloud not allowed |
| `DOMAIN_FORBIDDEN` | 403 | Access to the endpoint path not allowed |
| `ACCESS_DENIED` | 403 | Access denied, no specific reason |
| `AUTHORIZATION_FAILED` | 403 | Generic authorization error — contact support |
| `LICENSE_UPGRADE_REQUIRED` | 403 | License upgrade required — contact support |

**5xx**

| Code | Meaning | Reason |
|---|---|---|
| 500 | Internal Server Error | Any method unexpectedly failed |
| 501 | Not Implemented | Feature not implemented yet |

### Breaking changes (validation)

The API evolves its validation rules via opt-in HTTP header flags before a rollout
becomes the forced default. Send `Allow-Version: <flag>` to opt into a change ahead
of its rollout, or `BC0` to keep the old behavior after the rollout date.

**BC1 — rolled out 2023-06-01 (now default behavior unless `BC0` is sent):**

| Area | Before (`BC0`) | After (`BC1`, now default) |
|---|---|---|
| Paginated GET with no matches | `404` with empty body | `200` with a pagination wrapper body and `"totalItemsCount": "0"` |
| Customer POST | `flags` optional (may be omitted/null) | `flags` required (send `"flags": 0` for none) |
| Employee update (`id == 0`, the administrator) | `enabled`/`deleted`/`accessLevel`/`stockAccessLevel` are silently ignored (forced to fixed values) | Any change to those four fields on the administrator is rejected with `400 Bad Request`. The administrator can never be deleted (`403`), regardless of this flag. |
| Category/Product `vat` (tax-payer clouds) | not validated | validated against the cloud's configured VAT rates (see the Tax entity); mismatch → `400 Bad Request` |

**BC2 — not yet scheduled, opt in with `Allow-Version: BC2`:**

Product's `externalId` (single string) is being replaced by `externalIds` (list of
strings, max 1 item during the transition, each up to 256 characters). Both fields
read/write the same stored value during the transition.

| Header sent | Effective behavior | Validation |
|---|---|---|
| (none) or `BC1` | `BC0`/`BC1` (old) | Only `externalId` may be written; sending `externalIds` on write → `400` (except `"externalIds": null` on POST, accepted and ignored — a GET response containing that null can be echoed back). Responses contain both fields. |
| `BC2` | `BC2` (new) | Only `externalIds` may be written (max 1 item, ≤256 chars each); sending `externalId` on PUT/PATCH → `400` (silently ignored on POST). Responses contain only `externalIds`. |

---

## Enums

### Payment methods

Used for POS Action requests with a `payment-method-id`. **Not a closed list** — the
documentation explicitly warns more values can appear in responses than are listed
here.

| Name | ID |
|---|---|
| Cash | 900000001 |
| Credit card | 900000002 |
| Check | 900000003 |
| Meal voucher | 900000004 |
| Bank transfer | 900000009 |
| Electronic food vouchers | 900000010 |
| Gift card/Voucher | 900000011 |
| Write-off | 900000012 |
| SumUp | 900000014 |
| Uber Eats | 900000016 |
| Vyzvednisi Online | 900000017 |
| QR code | 900000018 |
| Online | 900000019 |
| Room | 900000020 |
| Cash machine | 900000021 |
| Multisport | 900000022 |
| Qerko | 901000001 |
| Corrency | 901000002 |
| Bolt Food | 901000003 |
| Wolt | 901000004 |
| Speedlo | 901000005 |
| Choice QR | 901000006 |
| Previo | 901000007 |
| Foodora | 901000008 |
| Foodora (cash) | 901000009 |
| Slevomat | 901000010 |
| Zlavomat | 901000011 |
| Pyszne | 901000012 |
| Glovo | 901000013 |
| Apetigo | 901000014 |
| Bistro.sk | 901000015 |

### Units

Grouped list of unit names (used for `unit`/`unitMeasurement` fields on Product,
Product Ingredient, Stock Packaging, Order item, etc.). **The site gives only these
human-readable names, not the exact JSON string each one serializes to** — the exact
wire format needs an empirical check against a real API response.

| Group | Values |
|---|---|
| Count | Piece |
| Weight | Milligram, Decagram, Gram, Kilogram, Pound, Ounce, Quintal, Tonne |
| Length | Millimeter, Centimeter, Meter, Kilometer, Inch, Mile |
| Area | SquareMeter, SquareFoot |
| Volume | Milliliter, Deciliter, Centiliter, Liter, UsGallon, UkGallon, Hectoliter, CubicMeter, CubicFoot |
| Time | Second, Minute, Hour, Day |
| Point | Points |

(The Delivery Note XML units list — see that entity's section — additionally names
`Week`, `Month`, `Year` for Time, which don't appear on this Enums page; recorded as
observed, not reconciled.)

### Order status

| Status name | Wire value |
|---|---|
| New | `new` |
| Parked | `parked` |
| Ready to pickup | `ready_to_pickup` |
| Ready for delivery | `ready_for_delivery` |
| On delivery | `on_delivery` |
| Delivered | `delivered` |
| Delivery failed | `delivery_failed` |
| Canceled | `canceled` |
| Closed | `closed` |
| Unknown | *(fallback, no wire value — the documentation explicitly says this list is not exhaustive and unknown/future values fall back to this status)* |

Documented status meanings and transitions (`order/perform-status-transition` POS
action, see POS Actions below):

| Status | Meaning | Transition → end status |
|---|---|---|
| New | Newly created, not parked yet | |
| Parked | Created and parked (saved) | `prepare_for_delivery` → Ready for delivery; `prepare_to_pickup` → Ready to pickup |
| Ready to pickup | Ready for onsite pickup | `complete_pickup` → Closed; `cancel_pickup` → Canceled |
| Ready for delivery | Ready and waiting for delivery | `start_delivery` → On delivery; `cancel_delivery` → Canceled |
| On delivery | Being delivered | `complete_delivery` → Closed; `cancel_delivery` → Canceled |
| Delivered | Delivered to customer | |
| Delivery failed | Delivery failed | |
| Canceled | Canceled | |
| Closed | Closed/issued (picked up or delivered, if applicable) | `prepare_for_delivery` → Ready for delivery; `prepare_to_pickup` → Ready to pickup |
| Unknown | Fallback status | |

---

## Entity

### Attendance

- Doc: https://docs.api.dotypos.com/entity/attendance/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/attendances` | list |
  | GET | `/attendances/:attendanceId` | single |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | integer? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `_employeeId` | long | EQUALS, ENUM | — | |
  | `created` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `event` | enum | EQUALS, ENUM | — | one of `LOGIN`, `LOGOUT`, `CHECK_IN`, `CHECK_OUT`, `PAUSE`, `PAUSE_END` |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET (list/single); no write endpoints exist for this entity (read-only).

### Branch

- Doc: https://docs.api.dotypos.com/entity/branch/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/branches` | list |
  | GET | `/branches/:branchId` | single |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | integer? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `created` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `features` | long | BITS | — | bitfield, see Flags below |
  | `flags` | short | BITS | — | bitfield, see Flags below |
  | `name` | string(100) | — | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Flags:
  | Bit | Name |
  |---|---|
  | 0 | `SUBSTITUTING_BRANCH` |
  | 1 | `REPLACED_BRANCH` |
  | 8 | `HIDE_STOCK` |
  | 9 | `HIDE_PRICES` |
  | 10 | `FREE_LICENSE` |
- Response shape: bare array on list — no read/no-write, no CRUD endpoints beyond
  the two GETs (no POST/PUT/PATCH/DELETE for Branch at all).
- ETag: `If-None-Match` on both GETs.

### Category

- Doc: https://docs.api.dotypos.com/entity/category/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/categories` | list |
  | GET | `/categories/:categoryId` | single |
  | POST | `/categories` | array body, max 100 |
  | PUT | `/categories` | array body, max 100 |
  | PUT | `/categories/:categoryId` | single |
  | PATCH | `/categories/:categoryId` | `If-Match` required |
  | DELETE | `/categories/:categoryId` | category must contain no non-deleted products; `409 Conflict` otherwise. Move products out first via GET (`filter=deleted|eq|false;_categoryId|eq|:categoryId`) then PUT. |
  | OPTIONS | `/categories`, `/categories/:categoryId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `_defaultCourseId` | long? | EQUALS, ENUM | — | |
  | `_eetSubjectId` | long? | EQUALS, ENUM | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `externalId` | string? | EQUALS, ENUM | — | |
  | `flags` | long | BITS | — | see Flags below |
  | `hexColor` | string(7) | — | — | |
  | `margin` | string?(180) | — | — | |
  | `maxDiscount` | double? | — | — | |
  | `modifiedBy` | string?(32) | — | — | |
  | `name` | string(180) | EQUALS, STRING | BOTH | |
  | `sortOrder` | long? | — | BOTH | |
  | `tags` | string[]? | EQUALS, ENUM | — | |
  | `translatedName` | map\<string,string\>? | — | — | language code → translated category name |
  | `vat` | double? | — | — | multiplier in `<1.0; 2.0>`, e.g. `1.234` = 23.4%. Validated against configured VAT rates for tax-payer clouds. |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Flags:
  | Bit | Name |
  |---|---|
  | 8 | `FISCALIZATION_DISABLED` |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update, ignored to
  insert), required on PATCH; ignored (temporarily) on DELETE.

### Cloud

- Doc: https://docs.api.dotypos.com/entity/cloud/
- Endpoints (note: no `:cloudId` scoping prefix — this is the top-level entity):
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/clouds` | list |
  | GET | `/clouds/:cloudid` | single |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | integer? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `1ClickId` | integer? | EQUALS, ENUM | — | |
  | `_companyId` | long? | EQUALS, ENUM | — | |
  | `country` | string?(3) | — | — | country code |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `expired` | boolean | EQUALS, ENUM | — | |
  | `name` | string(255) | EQUALS, STRING | BOTH | |
  | `restricted` | boolean | EQUALS, ENUM | — | |
  | `segment` | string?(100) | — | — | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on both GETs. Read-only entity (no write endpoints
  documented).

### Cloud Manifest

- Doc: https://docs.api.dotypos.com/entity/cloud-manifest/
- A **read-only, single-object, aggregated snapshot** of a cloud's configuration,
  multi-cloud relationships and active-branch status — meant as one call to bootstrap
  a client against a given cloud. No list endpoint exists.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/manifest` | single object, not a list |
- Schema (nested object, no per-field filter/sort — this is a snapshot, not a
  filterable/sortable entity):
  - `cloud` (object) — cloud config profile:
    - `name` (string?), `countryCode` (string?, ISO 3166-1 alpha-2, 2 letters
      uppercase), `currency` (string?, ISO 4217, 3 letters uppercase)
    - `stockConfiguration` (object?): `mode` (enum: `avg_purchase_price` |
      `fixed_stock_price`)
    - `customerConfiguration` (object?): `useAccounts` (boolean), `usePoints`
      (boolean)
  - `multicloud` (object) — multi-cloud profile:
    - `parentCloudId` (integer?) — parent cloud ID, or `null` for a
      standalone/parent cloud
    - `config` (object?): `fullTableSync` (string[]?, table names fully synced
      across the multi-cloud), `customerAccountSource` (enum?: `local` |
      `parentCloud`)
    - `connectedCloudIds` (array) — flattened list of cloud IDs in the same
      multi-cloud tree (excluding the requested cloud), sorted ascending
  - `branches` (array) — active branches, each:
    - `branchId` (integer), `name` (string?), `isLicensed` (boolean — has an active,
      used, enabled, non-expired activation key, or a special enabled key),
      `features` (array, set of enabled feature flags — contains `posAction` when
      the branch has an active premium+ license), `stockDeductionMode` (enum:
      `disabled` | `orderPark` | `documentIssue`, defaults to `documentIssue` when
      not configured)
- No ETag documented for this endpoint.

### Course

- Doc: https://docs.api.dotypos.com/entity/course/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/courses` | list |
  | GET | `/courses/:courseId` | single |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `deleted` | boolean | EQUALS, ENUM | — | cannot be `true` on POST/PUT/PATCH |
  | `enabled` | boolean | — | — | |
  | `name` | string(1,...) | EQUALS, STRING | BOTH | |
  | `sortOrder` | long | — | — | value for ordering courses |
  | `tags` | string[] | — | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | — | |
- Response shape: bare array on list. No write endpoints documented (read-only on
  this page).
- ETag: not documented on this page.

### Customer

- Doc: https://docs.api.dotypos.com/entity/customer/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/customers` | list |
  | GET | `/customers/:customerId` | single |
  | POST | `/customers` | array body |
  | PUT | `/customers` | array body (batch replace/create) |
  | PUT | `/customers/:customerId` | single |
  | PATCH | `/customers/:customerId` | `If-Match` required |
  | DELETE | `/customers/:customerId` | query `anonymize` (boolean, default `false`) |
  | OPTIONS | `/customers`, `/customers/:customerId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `_discountGroupId` | long? | EQUALS, ENUM | — | |
  | `_sellerId` | long? | EQUALS, ENUM | — | |
  | `firstName` | string(180) [1] | STRING | BOTH | |
  | `lastName` | string(180) [1] | STRING | BOTH | |
  | `companyName` | string(180) [1] | STRING | BOTH | |
  | `addressLine1` | string(180) | STRING | — | |
  | `addressLine2` | string?(180) | STRING | — | |
  | `city` | string?(255) | EQUALS, STRING | — | |
  | `country` | string?(10) | STRING | — | country code |
  | `zip` | string(20) | STRING | — | |
  | `barcode` | string(50) | EQUALS, ENUM | — | |
  | `companyId` | string(255) | STRING | — | CZ+SK: IČO (business ID), PL: REGON |
  | `vatId` | string(255) | STRING | — | CZ+SK: DIČ (VAT ID), PL: NIP; country-specific regex |
  | `companyId2` | string?(255) | STRING | — | SK only: VAT ID for VAT payers/non-payers with EU partners (IČ DPH) |
  | `email` | string(100) | STRING | — | |
  | `phone` | string(20) | STRING | — | |
  | `birthday` | timestamp? | — | — | |
  | `expireDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `created` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `points` | double | NUMBER | — | must be ≥ 0 |
  | `flags` | long | BITS | — | bitfield; **required on POST since BC1** (send `0` for none) |
  | `tags` | string[](255) | EQUALS, ENUM | — | forbidden characters: `` , ^ ? * ( ) [ ] $ `` |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `hexColor` | string(7) | — | — | |
  | `internalNote` | string(1000) | — | — | |
  | `note` | string?(500) | — | — | |
  | `headerPrint` | string(256) | — | — | |
  | `modifiedBy` | string?(32) | — | — | |
  | `externalId` | string?(256) | EQUALS, ENUM | — | |

  [1] At least one of `firstName`/`lastName`/`companyName` must be non-blank; the
  others may be blank/empty.
- Response shape: **bare array** on list (batch POST/PUT also return arrays).
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update, ignored to
  insert); `If-Match` **required** on PATCH; ignored (temporarily) on DELETE.
- Breaking-changes note: see BC1 (flags required) and the planned PATCH/validation
  changes in the General section above.

### Customer Account

- Doc: https://docs.api.dotypos.com/entity/customer-account/
- Defines a customer's loyalty/credit account. For credit operations, the
  integrator is responsible for issuing tax documents.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | POST | `/customer-accounts` | create |
  | GET | `/customer-accounts/:customerAccountId` | get by account ID |
  | GET | `/customers/:customerId/accounts/:accountType` | get for a specific customer (`accountType` valid value: `default`) |
  | GET | `/customer-accounts` | search/list, standard page/limit/filter/sort |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long | EQUALS, ENUM | — | |
  | `_cloudId` | integer | EQUALS, ENUM | — | |
  | `_customerId` | long | EQUALS, ENUM | — | |
  | `_lastCustomerAccountLogId` | long? | EQUALS, ENUM | — | |
  | `allowedMinimalBalance` | BigDecimal | NUMBER | — | |
  | `balance` | BigDecimal | NUMBER | — | |
  | `currency` | string | STRING | BOTH | ISO 4217, 3 letters |
  | `type` | enum | STRING | BOTH | only supported value: `"default"` |
  | `created` | timestamp | EQUALS, ENUM, NUMBER | — | |
  | `modified` | timestamp | EQUALS, ENUM, NUMBER | — | |
- Response shape: search endpoint returns a bare array (consistent with the rest of
  the API); single-account/for-customer endpoints return a single object.
- ETag: not documented on this page.

### Customer Account Log

- Doc: https://docs.api.dotypos.com/entity/customer-account-log/
- Defines a single transaction on a customer account. Same tax-document caveat as
  Customer Account.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | POST | `/customer-account-logs` | requires an existing account (`_customerAccountId`) |
  | POST | `/customers/:customerId/accounts/:accountType/logs` | creates the default account first if it doesn't exist, then the log |
  | GET | `/customers/:customerId/accounts/:accountType/logs` | list for a customer |
  | GET | `/customers/:customerId/accounts/:accountType/logs/:customerAccountLogId` | single, scoped to a customer |
  | GET | `/customer-account-logs/:customerAccountLogId` | single, by log ID |
  | GET | `/customer-account-logs` | search/list |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long | EQUALS, ENUM | — | |
  | `_cloudId` | integer | EQUALS, ENUM | — | |
  | `_customerAccountId` | long? | EQUALS, ENUM | — | |
  | `_employeeId` | long? | EQUALS, ENUM | — | |
  | `_moneyLogId` | long? | EQUALS, ENUM | — | |
  | `_orderId` | long? | EQUALS, ENUM | — | |
  | `_previousCustomerAccountLogId` | long? | EQUALS, ENUM | — | |
  | `amount` | BigDecimal | NUMBER | — | transaction amount |
  | `balance` | BigDecimal | NUMBER | — | account balance after the transaction |
  | `currency` | string | STRING | BOTH | ISO 4217 |
  | `type` | enum | STRING | BOTH | `"top-up"`, `"payment"`, `"refund"` |
  | `note` | string | — | — | |
  | `source` | string | — | — | identifies where the transaction took place |
  | `created` | timestamp | EQUALS, ENUM, NUMBER | — | |
- Response shape: bare array on the list/search endpoints.
- ETag: not documented on this page.

### Daily Menu

- Doc: https://docs.api.dotypos.com/entity/daily-menu/
- **Documented as unstable — the schema may change in the future.**
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/daily-menus` | list |
  | GET | `/daily-menus/:dailyMenuId` | single |
  | OPTIONS | `/daily-menus`, `/daily-menus/:dailyMenuId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | long | — | — | |
  | `deleted` | boolean | EQUALS, ENUM | — | |
  | `footer` | string? | — | — | |
  | `header` | string? | — | — | |
  | `validFrom` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `validUntil` | timestamp | EQUALS, ENUM, NUMBER | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | — | |
- Response shape: not shown with a concrete example (page shows a `// Response`
  placeholder for both list and single GET); other entities on this site
  consistently return bare arrays for lists, but this is not directly confirmed here.
- ETag: not documented on this page.

### Daily Menu Product

- Doc: https://docs.api.dotypos.com/entity/daily-menu-product/
- **Documented as unstable — the schema may change in the future.**
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/daily-menu-products` | list |
  | GET | `/daily-menu-products/:dailyMenuProductId` | single |
  | OPTIONS | `/daily-menu-products`, `/daily-menu-products/:dailyMenuProductId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | long | — | — | |
  | `_dailyMenuId` | long | EQUALS, ENUM | — | |
  | `_productId` | long | EQUALS, ENUM | — | |
  | `dailyMenuSection` | string? | EQUALS, ENUM | — | one of `SOUP`, `MAIN`, `DESSERT` |
  | `deleted` | boolean | EQUALS, ENUM | — | |
  | `note` | string? | — | — | |
  | `prefix` | string(10) | — | — | |
  | `validFrom` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `validUntil` | timestamp | EQUALS, ENUM, NUMBER | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | — | |
- Response shape: not shown with a concrete example (same placeholder as Daily Menu).
- ETag: not documented on this page.

### Delivery Note

- Doc: https://docs.api.dotypos.com/entity/delivery-note/
- Upload delivery notes (as XML) and query information about those already
  imported. See also the Delivery Notes Integrations guide below.
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long | EQUALS, ENUM | — | |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `currency` | string(3) | — | — | from XML `DEASDV.DOCUMENT.CURRENCY` |
  | `created` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | from XML `DEASDV.DOCUMENT.CREATED` |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | |
  | `documentId` | string(180) | EQUALS, ENUM, STRING | — | from XML attribute `DEASDV.DOCUMENT.id` |
  | `documentNumber` | string(18) | — | — | from XML `DEASDV.DOCUMENT.DOCUMENT_NUMBER` |
  | `expeditionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | from XML `DEASDV.DOCUMENT.EXPEDITION_DATE` |
  | `status` | integer | EQUALS, ENUM | — | |
  | `supplierName` | string(180) | — | — | from XML `DEASDV.DOCUMENT.SUPPLIER.NAME` |
  | `text` | string | — | — | from XML `DEASDV.DOCUMENT.TEXT` |
  | `type` | enum | — | — | from XML `DEASDV.DOCUMENT.TYPE`: `DELIVERY_NOTE`, `RETURN` (remittance) |
  | `url` | string(256) | — | — | public URL to download the XML (only the `DOCUMENT` section — not re-uploadable as-is) |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | POST | `/branches/:branchId/delivery-note-uploads` | upload XML, `multipart/form-data`, key `file`; see validations below |
  | GET | `/delivery-notes` | list |
  | GET | `/delivery-notes/:deliveryNoteId` | single |
  | OPTIONS | `/delivery-notes`, `/delivery-notes/:deliveryNoteId` | |
- Upload validations: max XML size 60MB; single `DEASDV` root element; `Document id`
  must be a valid UUID; `TYPE` must be `DELIVERY_NOTE` (default) or `RETURN`;
  `CURRENCY` must be ISO 4217; `UNIT` must be one of the supported units below.
  - `DELIVERY_NOTE`: `PRICE_WITHOUT_VAT`, `PRICE_WITH_VAT`, `VAT_RATE` must not be
    null.
  - `RETURN` (remittance): `AMOUNT` cannot be positive; `PRICE_WITHOUT_VAT`,
    `PRICE_WITH_VAT`, `VAT_RATE` must be null.
- Delivery note XML structure (simplified; full definition in the XSD linked on the
  site):
  ```xml
  <DEASDV>
      <DOCUMENT id="Document UUID">
          <CREATED><!-- RFC 3339 --></CREATED>
          <EXPEDITION_DATE><!-- RFC 3339 --></EXPEDITION_DATE>
          <TEXT><!-- custom string --></TEXT>
          <CURRENCY><!-- ISO 4217 CODE --></CURRENCY>
          <DOCUMENT_NUMBER><!-- custom string --></DOCUMENT_NUMBER>
          <SUPPLIER id="Supplier UUID">...</SUPPLIER>
          <ITEM>
              <SKU><!-- internal SKU --></SKU>
              <PRODUCT_NAME lang="cs"><!-- product name --></PRODUCT_NAME>
              <DESCRIPTION><!-- long description --></DESCRIPTION>
              <AMOUNT><!-- number --></AMOUNT>
              <UNIT><!-- see units below --></UNIT>
              <PRICE_WITHOUT_VAT><!-- number --></PRICE_WITHOUT_VAT>
              <PRICE_WITH_VAT><!-- number --></PRICE_WITH_VAT>
              <VAT_RATE><!-- percent, e.g. 15.0 --></VAT_RATE>
              <SALE_INFO>...</SALE_INFO>
              <BARCODES>...</BARCODES>
              <IMGURL>...</IMGURL>
          </ITEM>
          <!-- more ITEM -->
      </DOCUMENT>
      <!-- more DOCUMENT -->
  </DEASDV>
  ```
- Units supported in the XML `UNIT` element (as literal names, not enum wire
  values): Piece, Points; Milligram, Gram, Decagram, Kilogram, Pound, Ounce,
  Quintal, Tone; Millimeter, Centimeter, Meter, Kilometer, Inch, Mile; SquareMeter,
  SquareFoot; Milliliter, Centiliter, Deciliter, Liter, UsGallon, UkGallon,
  CubicFoot, Hectoliter, CubicMeter; Second, Minute, Hour, Day, Week, Month, Year.
- Response shape: bare array on list.
- ETag: `If-None-Match` on list GET; not documented on the other endpoints.

### Discount group

- Doc: https://docs.api.dotypos.com/entity/discount-group/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/discount-groups` | list |
  | GET | `/discount-groups/:discountGroupId` | single |
  | POST | `/discount-groups` | array body, max 100 |
  | PUT | `/discount-groups` | array body, max 100 |
  | PUT | `/discount-groups/:discountGroupId` | single |
  | PATCH | `/discount-groups/:discountGroupId` | `If-Match` required |
  | DELETE | `/discount-groups/:discountGroupId` | group must not belong to any non-deleted customer; remove it from customers first via GET (`filter=_discountGroupId|eq|:discountGroupId`) then PUT |
  | OPTIONS | `/discount-groups`, `/discount-groups/:discountGroupId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `discountPercent` | double(100) | — | — | max 100 (= 100%) |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `externalId` | string | EQUALS, ENUM | — | |
  | `name` | string(100) | EQUALS, ENUM | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### EET subject

- Doc: https://docs.api.dotypos.com/entity/eet-subject/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/eet-subjects` | list |
  | GET | `/eet-subjects/:eetSubjectId` | single |
  | POST | `/eet-subjects` | array body, max 100 |
  | PUT | `/eet-subjects` | array body, max 100 |
  | PUT | `/eet-subjects/:eetSubjectId` | single |
  | PATCH | `/eet-subjects/:eetSubjectId` | `If-Match` required |
  | DELETE | `/eet-subjects/:eetSubjectId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `enabled` | boolean | EQUALS, ENUM | — | |
  | `name` | string(500) | STRING | BOTH | |
  | `vatId` | string(50) | EQUALS | — | CZ: DIČ, PL: NIP; regex-validated |
  | `vatPayer` | boolean | EQUALS, ENUM | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: not shown as a concrete list example on this page, but the
  documented pattern for `create`/`replace` body arrays and every other entity's
  list endpoint on this site are bare arrays.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Employee

- Doc: https://docs.api.dotypos.com/entity/employee/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/employees` | list |
  | GET | `/employees/:employeeId` | single |
  | POST | `/employees` | array body, max 100 |
  | PUT | `/employees` | array body, max 100 |
  | PUT | `/employees/:employeeId` | single |
  | PATCH | `/employees/:employeeId` | `If-Match` required |
  | DELETE | `/employees/:employeeId` | the administrator (`id == 0`) can never be deleted (`403`) |
  | OPTIONS | `/employees`, `/employees/:employeeId` | |
  | POST | `/employees/:employeeId/access-pins` | set the employee's access PIN, see schema below |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `_sellerId` | long? | EQUALS, ENUM | — | |
  | `accessLevel` | long | BITS | — | |
  | `barcode` | string?(180) | STRING | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH; special BC1 rules for the administrator, see Breaking changes above |
  | `email` | string?(100) | STRING | BOTH | |
  | `enabled` | boolean | EQUALS, ENUM | — | special BC1 rules for the administrator |
  | `hexColor` | string(7) | — | — | |
  | `maxDiscount` | double? | — | — | |
  | `modifiedBy` | string?(32) | — | — | |
  | `name` | string(256) | STRING | BOTH | must not be empty |
  | `phone` | string?(40) | STRING | — | |
  | `requirePinAlways` | boolean | EQUALS, ENUM | — | |
  | `stockAccessLevel` | long | BITS | — | special BC1 rules for the administrator |
  | `tags` | string[]? | EQUALS, ENUM | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Access-pin sub-schema (`POST .../access-pins`):
  | Field | Type | Notes |
  |---|---|---|
  | `accessPin` | string(4,...) | numeric characters only |
  | `requirePinAlways` | boolean? | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Money log

- Doc: https://docs.api.dotypos.com/entity/money-log/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/money-logs` | list |
  | GET | `/money-logs/:moneyLogId` | single |
  | OPTIONS | `/money-logs`, `/money-logs/:moneyLogId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `_employeeId` | long? | EQUALS, ENUM | — | |
  | `_orderId` | long | EQUALS, ENUM | — | |
  | `_relatedMoneyLogId` | long? | EQUALS, ENUM | — | |
  | `_sellerId` | long? | EQUALS, ENUM | — | |
  | `amount` | double | — | — | |
  | `amountDefaultCurrency` | double? | — | — | |
  | `created` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `currency` | string?(3) | — | — | |
  | `flags` | long | BITS | — | see Flags below |
  | `note` | string?(1000) | — | — | |
  | `paymentTypeId` | long | EQUALS, ENUM | — | |
  | `tags` | string[]? | EQUALS, ENUM | — | |
  | `tipAmount` | double? | — | — | |
  | `transactionType` | enum | EQUALS, ENUM | — | see below |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- `transactionType` values (string in JSON): `SALE`, `REFUND`, `REGISTER_CLOSE`,
  `REGISTER_OPEN`, `CASH_IN_OUT`, `REGISTER_CLOSE_SECONDARY`,
  `REGISTER_OPEN_SECONDARY`.
- Flags:
  | Bit | Name |
  |---|---|
  | 1 | `INVOICE_FROM_RECEIPTS` |
- Response shape: bare array on list. Read-only entity (no write endpoints
  documented).
- ETag: `If-None-Match` on GET.

### Order

- Doc: https://docs.api.dotypos.com/entity/order/
- Read-only entity — orders are created through POS Actions (see below), not
  through these endpoints.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/orders` | list; extra query params `include`, `namedFilter` (see below) |
  | GET | `/orders/:orderId` | single |
  | OPTIONS | `/orders`, `/orders/:orderId` | |
- Extra list query params:
  - `include` (array): entities to embed — `orderItems` (list of order items),
    `moneyLogs` (list of money logs); can combine, e.g.
    `include=orderItems,moneyLogs`. Client needs read permission on the included
    entity, or `403 Forbidden`. Response wraps entities under `data[].orderItems`
    /`data[].moneyLogs` alongside the order fields.
  - `namedFilter` (string): `openOrders` (open orders only),
    `orderItems.openOrderItems` (with `include=orderItems`, filters embedded order
    items to open ones too). Combine with `;`, e.g.
    `namedFilter=openOrders;orderItems.openOrderItems`.
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long | EQUALS, ENUM | — | |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_cloudId` | integer | EQUALS, ENUM | — | |
  | `_courseId` | long? | EQUALS, ENUM | — | |
  | `_customerId` | long? | EQUALS, ENUM | — | |
  | `_eetSubjectId` | long? | EQUALS, ENUM | — | |
  | `_employeeId` | long? | EQUALS, ENUM | — | |
  | `_relatedInvoiceId` | long? | EQUALS, ENUM | — | |
  | `_relatedOrderId` | long? | EQUALS, ENUM | — | **deprecated**, use `_sourceOrderId` |
  | `_sellerId` | long? | EQUALS, ENUM | — | |
  | `_sourceOrderId` | long? | EQUALS, ENUM | — | |
  | `_tableId` | long? | EQUALS, ENUM | — | |
  | `bkp` | string? | EQUALS, ENUM | — | fiscalized orders only |
  | `canceledDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `completed` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `created` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `currency` | string(3) | — | — | |
  | `documentNumber` | string | EQUALS, ENUM, STRING | — | |
  | `documentType` | enum | EQUALS, ENUM | — | see below |
  | `externalId` | string? | EQUALS, ENUM | — | |
  | `fik` | string? | — | — | fiscalized orders only |
  | `flags` | integer | BITS | — | see Flags below |
  | `guestCount` | integer | — | — | |
  | `itemCount` | integer | — | — | |
  | `locationAccuracy` | double? | — | — | GPS accuracy |
  | `locationDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | GPS record date |
  | `locationLatitude` | double? | — | — | |
  | `locationLongitude` | double? | — | — | |
  | `merchantPrintData` | string? | — | — | |
  | `note` | string(1000)? | STRING | — | |
  | `paid` | boolean | EQUALS, ENUM | — | |
  | `parked` | boolean | EQUALS, ENUM | — | |
  | `pkp` | string? | — | — | fiscalized orders only |
  | `points` | double | EQUALS, NUMBER | — | |
  | `printData` | string | — | — | |
  | `status` | enum | EQUALS, ENUM | — | see Enums → Order status above |
  | `tags` | string[]? | EQUALS, ENUM | — | |
  | `tipAmount` | double? | — | — | |
  | `totalValueRounded` | double | — | — | |
  | `updated` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `versionDate` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
- `documentType` values (string in JSON): `RECEIPT`, `INVOICE`,
  `INVOICE_FROM_RECEIPTS`, `CORRECTIVE_INVOICE`, `EXTERNAL_INVOICE_PAYMENT`,
  `CASH_IN`, `CASH_OUT`.
- Flags:
  | Bit | Name | Notes |
  |---|---|---|
  | 0 | `CANCELED_PART` | |
  | 1 | `CANCELED_FULL` | |
  | 2 | `CANCELLATION` | |
  | 3 | `FISCALIZATION_REQUIRED` | |
  | 4 | `MERGED` | |
  | 5 | `FISCALIZATION_DISABLED` | |
  | 6 | `PAID_PART` | |
  | 7 | `FISCALIZATION_SIMPLIFIED` | |
  | 8 | `VAT_PAYER` | |
  | 9 | `NON_VAT_PAYER` | |
  | 10 | `PDF_INVOICE` | |
  | 11 | `WRITEOFF` | |
  | 12 | `GASTRO` | |
  | 13 | `FISCALIZATION_FAILED` | |
  | 14 | `VAT_PRINT_DISABLED` | |
  | 15 | `LUNCH_INVITATION` | |
  | 16 | `DELIVERY` | |
  | 17 | `MOVED_FROM` | |
  | 18 | `MOVED_TO` | |
  | 19 | `WELMEC_ENABLED` | scale standard |
  | 20 | `REVERSE_CHARGE` | |
  | 21 | `CORRECTED` | |
  | 22 | `USE_ALTERNATIVE_ITEM_NAMES` | |
  | 23 | `PAID_WITH_ALTERNATIVE_CURRENCY` | |
  | 24 | `VAT_RECORDS_TOP_DOWN` | VAT summary computed from price with VAT |
  | 25 | `EOS_LOCKED` | modifiable only via EOS flows |
  | 26 | `SK_CANCELED_NONFISCALLY` | the original order carries this flag |
  | 27 | `ISSUED_EXTERNALLY` | issued externally without employee interaction; an info dialog should be shown |
  | 28 | `ISSUED_EXTERNALLY_DISPLAYED` | the external-issue info was displayed |
  | 29 | `FISCALIZATION_SUCCESSFUL` | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET.

### Order item

- Doc: https://docs.api.dotypos.com/entity/order-item/
- Read-only entity (order items are created/modified via POS Actions).
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/order-items` | list; response includes an extra `orderItemCustomizations` array field per item |
  | GET | `/order-items/:orderItemId` | single; same extra field |
  | OPTIONS | `/order-items`, `/order-items/:orderItemId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long | EQUALS, ENUM | — | |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_categoryId` | long | EQUALS, ENUM | — | |
  | `_cloudId` | integer | EQUALS, ENUM | — | |
  | `_courseId` | long? | EQUALS, ENUM | — | |
  | `_customerId` | long? | EQUALS, ENUM | — | |
  | `_eetSubjectId` | long? | EQUALS, ENUM | — | |
  | `_employeeId` | long | EQUALS, ENUM | — | |
  | `_orderId` | long | EQUALS, ENUM | — | |
  | `_productId` | long | EQUALS, ENUM | — | |
  | `_relatedOrderItemId` | long? | EQUALS, ENUM | — | |
  | `_sellerId` | long? | EQUALS, ENUM | — | |
  | `alternativeName` | string? | — | — | |
  | `billedUnitPriceWithVat` | double | — | — | unit price with VAT after discount |
  | `billedUnitPriceWithoutVat` | double | — | — | unit price without VAT after discount |
  | `canceledDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `completed` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | order completed date |
  | `created` | timestamp | EQUALS, ENUM, NUMBER | BOTH | added to order |
  | `currency` | string(3) | — | — | |
  | `discountPercent` | double | — | — | |
  | `discountPermitted` | boolean | EQUALS, ENUM | — | |
  | `ean` | string[] | EQUALS, ENUM | — | from product |
  | `flags` | integer | BITS | — | |
  | `name` | string | STRING | BOTH | |
  | `note` | string | STRING | — | |
  | `onSale` | boolean | EQUALS, ENUM | — | |
  | `packaging` | double | — | — | from product |
  | `parked` | boolean | EQUALS, ENUM | — | |
  | `points` | double | — | — | |
  | `preparationDuration` | integer? | — | — | seconds |
  | `quantity` | double | — | — | |
  | `stockDeduct` | boolean | EQUALS, ENUM | — | |
  | `subtitle` | string | STRING | — | |
  | `tags` | string[] | EQUALS, ENUM | — | |
  | `totalPriceWithVat` | double | — | — | includes quantity |
  | `totalPriceWithoutVat` | double | — | — | includes quantity |
  | `unit` | enum | — | — | see Enums → Units above |
  | `unitPriceWithVat` | double | — | — | before discount |
  | `unitPriceWithoutVat` | double | — | — | before discount |
  | `unitPurchasePrice` | double | — | — | |
  | `updated` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `vat` | double | — | — | rate |
  | `versionDate` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
- `orderItemCustomizations[]` (embedded on GET only), each object:
  | Field | Type | Notes |
  |---|---|---|
  | `id` | long | |
  | `_branchId` | integer | |
  | `_cloudId` | integer | |
  | `_orderId` | long | |
  | `_orderItemId` | long | |
  | `_productCustomizationId` | long | |
  | `_productId` | long | |
  | `alternativeName` | string | |
  | `canceledDate` | timestamp | |
  | `created` | timestamp | |
  | `defaultSelection` | string | |
  | `discountValue` | double | |
  | `flags` | long | |
  | `name` | string | |
  | `preparationDuration` | integer | |
  | `purchasePriceWithoutVat` | double | |
  | `quantity` | double | |
  | `unit` | enum | |
  | `unitPriceWithVat` | double | |
  | `vat` | double | |
  | `versionDate` | timestamp | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET.

### Portion

- Doc: https://docs.api.dotypos.com/entity/portion/
- A portion is a size a product can be sold in — a percentage of the standard
  portion, for a percentage of the standard price (a half portion at 60% of price is
  `portion: 50, price: 60`). Portions are stored as tags named
  `.portion-<portion>-<price>` (both numbers with 5 decimals, e.g.
  `.portion-50.00000-60.00000`); a product offers a portion by carrying that tag
  name. These endpoints are the supported way to manage portion tags (they hide the
  leading-dot name, and a repeated POST answers with the existing tag instead of
  refusing). Assigning a portion to a product is done through the product's `tags`,
  not here. **A portion cannot be edited** — its numbers are baked into the tag
  name, and products/categories/customers/reservations reference tags by name, not
  ID, so editing would require rewriting every record referencing the old name.
  Create the new one and delete the old one; deletion is refused while any product
  still carries it.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/portions` | list |
  | GET | `/portions/:portionId` | single |
  | POST | `/portions` | single object body (not a list); `409`/`400` with `reason: "PORTION_ALREADY_EXISTS"` if the same portion+price pair already exists |
  | DELETE | `/portions/:portionId` | `400` naming the products still offering it, if any |
- Fields:
  | Field | Type | Notes |
  |---|---|---|
  | `id` | integer? | the ID of the tag holding the portion |
  | `portion` | decimal | % of the standard portion, positive, ≤5 decimals |
  | `price` | decimal | % of the standard price, positive, ≤5 decimals |
  | `display` | boolean? | offered in the POS menu; defaults to `true` on create |
  | `deleted` | boolean | cannot be `true` in POST |
  | `versionDate` | timestamp? | |
- No filter/sort markers documented for this entity's fields.
- Response shape: bare array on list; POST takes/returns a single object (`201` on
  success).
- ETag: not documented on this page.

### Product

- Doc: https://docs.api.dotypos.com/entity/product/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/products` | list; extra query param `include` (see below) |
  | GET | `/products/:productId` | single |
  | POST | `/products` | array body, max 100 |
  | PUT | `/products` | array body, max 100 |
  | PUT | `/products/:productId` | single |
  | PATCH | `/products/:productId` | `If-Match` required |
  | DELETE | `/products/:productId` | body params `productIsIngredient`/`productHasIngredients` (`ERROR`\|`PRESERVE`\|`DELETE`), see below |
  | OPTIONS | `/products`, `/products/:productId` | |
- `include` query param (array): `customizations` (list of customizations),
  `ingredients` (list of ingredients); combine with a comma, e.g.
  `include=customizations,ingredients`. Requires read permission on the included
  entity or `403`. Response wraps entities under `data[].customizations`/
  `data[].ingredients`.
- Delete strategies: default (no body, or `{"...": "ERROR"}`) rejects deletion with
  `409 Conflict` if the product has its own ingredients or is an ingredient of
  another product; `"DELETE"` deletes the related ingredients too (needs delete
  permission on ingredients, else `403`); `"PRESERVE"` deletes the product but keeps
  the ingredient relations.
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_categoryId` | long | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `_defaultCourseId` | long? | EQUALS, ENUM | — | |
  | `_eetSubjectId` | long? | EQUALS, ENUM | — | |
  | `_supplierId` | long? | EQUALS, ENUM | — | |
  | `allergens` | int[] | — | — | per Annex II, Regulation (EU) No 1169/2011 |
  | `alternativeName` | string? | — | — | |
  | `currency` | string?(3) | — | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `deliveryNoteIds` | string? | — | — | |
  | `description` | string?(1000) | — | — | |
  | `discountPercent` | double | — | — | |
  | `discountPermitted` | boolean | EQUALS, ENUM | — | |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `ean` | string[]? | EQUALS, ENUM | BOTH | sorted by the first EAN in the list |
  | `externalId` | string? | EQUALS, ENUM | — | **being replaced by `externalIds`, see BC2 above** |
  | `externalIds` | string[]? | EQUALS, ENUM | — | max 1 item, ≤256 chars; the BC2 replacement for `externalId`, shares its stored value |
  | `features` | string[] | — | — | e.g. spicy/vegan, primarily for EOS |
  | `flags` | integer | BITS | — | see Flags below |
  | `hexColor` | string(7) | — | — | |
  | `imageUrl` | string? | — | — | can only be cleared by setting `null`; non-null values are ignored |
  | `margin` | string?(50) | — | — | exact number, or a `%`-suffixed percentage (≤100%) |
  | `marginMin` | double? | — | — | |
  | `minCustomerAge` | integer? | — | — | years |
  | `modifiedBy` | string?(32) | — | — | |
  | `name` | string(400) | STRING | BOTH | |
  | `notes` | string[]?(1000) | — | — | |
  | `onSale` | boolean | EQUALS, ENUM | — | discount offer |
  | `packageItem` | double? | — | — | |
  | `packaging` | double | — | — | items per package |
  | `packagingMeasurement` | double? | — | — | |
  | `packagingPriceWithVat` | double? | — | — | primarily for EOS |
  | `plu` | string[]? | EQUALS, ENUM | BOTH | sorted by the first PLU in the list |
  | `points` | double | — | — | |
  | `preparationDuration` | integer? | — | — | seconds |
  | `priceInPoints` | double | — | — | |
  | `priceWithVat` | double? | EQUALS, ENUM | BOTH | |
  | `priceWithVatB` | double? | — | — | alternative price level B |
  | `priceWithVatC` | double? | — | — | alternative price level C |
  | `priceWithVatD` | double? | — | — | alternative price level D |
  | `priceWithVatE` | double? | — | — | alternative price level E |
  | `priceWithoutVat` | double | — | BOTH | |
  | `purchasePriceWithoutVat` | double? | — | — | **deprecated: ignored on write, omitted from responses** |
  | `recipe` | string?(8192) | — | — | preparation instructions, shown below ingredients |
  | `requiresPriceEntry` | boolean | EQUALS, ENUM | — | must match flag `REQUIRES_PRICE_ENTRY` |
  | `sortOrder` | long? | — | — | |
  | `stockDeduct` | boolean | EQUALS, ENUM | — | deducted from Warehouse |
  | `stockOverdraft` | enum | — | — | `ALLOW`, `WARN`, `DISABLE` |
  | `subtitle` | string?(500) | — | — | |
  | `supplierProductCode` | string? | — | — | |
  | `tags` | string[]? | EQUALS, ENUM | — | forbidden characters: `` , ^ ? * ( ) [ ] $ `` |
  | `translatedDescription` | map\<string,string\>? | — | — | |
  | `translatedName` | map\<string,string\>? | — | — | |
  | `unit` | enum | EQUALS | — | see Enums → Units above |
  | `unitMeasurement` | enum? | — | — | |
  | `vat` | double | EQUALS | BOTH | multiplier `<1.0; 2.0>`; validated against configured VAT rates for tax-payers |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Flags:
  | Bit | Name |
  |---|---|
  | 0 | `SHORTCUT` |
  | 1 | `REQUIRES_QUANTITY_ENTRY` |
  | 2 | `REQUIRES_PRICE_ENTRY` |
  | 3 | `TIMEABLE` |
  | 5 | `MANUAL_SALE_ITEM` |
  | 6 | `TIMEABLE_LOCKED` |
  | 7 | `JOINT_SALE_ITEM` |
  | 8 | `FISCALIZATION_DISABLED` |
  | 10 | `ASSEMBLED_ITEM` |
  | 11 | `TAKE_AWAY` |
  | 12 | `TAKE_AWAY_ITEM` |
  | 13 | `SPECIAL` |
  | 14 | `WITH_CUSTOMIZATIONS` |
  | 15 | `EXEMPTED_VAT` |
  | 18 | `UNCERTIFIED_QUANTITY` |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Product Customization

- Doc: https://docs.api.dotypos.com/entity/product-customization/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/product-customizations` | list |
  | GET | `/product-customizations/:entityId` | single |
  | POST | `/product-customizations` | array body |
  | PUT | `/product-customizations` | array body |
  | PUT | `/product-customizations/:entityId` | single |
  | PATCH | `/product-customizations/:entityId` | `If-Match` required |
  | DELETE | `/product-customizations/:entityId` | |
  | OPTIONS | `/product-customizations`, `/product-customizations/:entityId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_categoryId` | long | EQUALS, ENUM | — | |
  | `_cloudId` | long | — | — | |
  | `_productId` | long | EQUALS, ENUM | — | |
  | `_defaultProductIds` | string[] | — | — | default-selected product IDs |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | |
  | `flags` | integer | BITS | — | see Flags below |
  | `maxItemQuantity` | integer? | — | — | max qty of one item; `null` = no limit (default 1); requires Dotypos 2.19+ |
  | `maxSelected` | integer | — | — | |
  | `minSelected` | integer | — | — | |
  | `name` | string?(400) | STRING | BOTH | |
  | `priceLevel` | enum? | — | — | `B`, `C`, `D`, `E`; Poland-only, ignored elsewhere |
  | `sortOrder` | long | — | BOTH | |
  | `translatedName` | map\<string,string\>? | — | — | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Flags:
  | Bit | Name |
  |---|---|
  | 0 | `DEFAULT_SELECTION_GRATIS` |
  | 1 | `ONE_CHEAPEST_ITEM_GRATIS` |
  | 2 | `ALL_ITEMS_GRATIS` |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Product Ingredient

- Doc: https://docs.api.dotypos.com/entity/product-ingredient/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/product-ingredients` | list |
  | GET | `/product-ingredients/:entityId` | single |
  | POST | `/product-ingredients` | array body |
  | PUT | `/product-ingredients` | array body |
  | PUT | `/product-ingredients/:entityId` | single |
  | PATCH | `/product-ingredients/:entityId` | `If-Match` required |
  | DELETE | `/product-ingredients/:entityId` | |
  | OPTIONS | `/product-ingredients`, `/product-ingredients/:entityId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | long | — | — | |
  | `_parentProductId` | long | EQUALS, ENUM | — | product the ingredient belongs to |
  | `_productId` | long | EQUALS, ENUM | — | the ingredient product itself |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | |
  | `quantity` | double | — | — | amount of the ingredient product |
  | `unit` | enum | EQUALS | — | see Enums → Units above |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Reservation

- Doc: https://docs.api.dotypos.com/entity/reservation/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/reservations` | list |
  | GET | `/reservations/:reservationId` | single |
  | POST | `/reservations` | array body, max 100 |
  | PUT | `/reservations` | array body, max 100 |
  | PUT | `/reservations/:reservationId` | single |
  | PATCH | `/reservations/:reservationId` | `If-Match` required |
  | DELETE | `/reservation/:reservationId` | note: **singular** `reservation` in the documented path |
  | OPTIONS | `/reservations`, `/reservations/:reservationId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `_customerId` | long | EQUALS, ENUM | — | |
  | `_employeeId` | long | EQUALS, ENUM | — | |
  | `_tableId` | long | EQUALS, ENUM | — | |
  | `created` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `endDate` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `flags` | integer | BITS | — | |
  | `note` | string? | — | — | |
  | `seats` | short | — | — | 1 ≤ value ≤ `Table.seats` of the referenced table |
  | `startDate` | timestamp | EQUALS, ENUM, NUMBER | BOTH | |
  | `status` | enum | — | — | `NEW`, `CONFIRMED`, `CANCELLED` |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Stock Packaging

- Doc: https://docs.api.dotypos.com/entity/stock-packaging/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/stock-packagings` | list |
  | GET | `/stock-packagings/:stockPackagingId` | single |
  | POST | `/stock-packagings` | array body, max 100; `409 Conflict` possible |
  | PUT | `/stock-packagings` | array body, max 100; `409 Conflict` possible |
  | PUT | `/stock-packagings/:stockPackagingId` | single; `409 Conflict` possible |
  | PATCH | `/stock-packagings/:stockPackagingId` | `If-Match` required; `409 Conflict` possible |
  | DELETE | `/stock-packagings/:stockPackagingId` | `409 Conflict` possible |
  | OPTIONS | `/stock-packagings`, `/stock-packagings/:stockPackagingId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `_productId` | long | EQUALS, ENUM | — | the stock item product |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `ean` | string[]? | EQUALS, ENUM | — | |
  | `externalId` | string? | EQUALS, ENUM | — | |
  | `name` | string(400) | STRING | BOTH | |
  | `plu` | string[]? | EQUALS, ENUM | — | |
  | `quantity` | double | — | — | |
  | `unit` | enum | — | — | must be in the same unit group as the referenced product's own unit |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list.
- ETag: not documented explicitly on this page (only generic `If-Match` on writes).

### Supplier

- Doc: https://docs.api.dotypos.com/entity/suppliers/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/suppliers` | list |
  | GET | `/suppliers/:entityId` | single |
  | POST | `/suppliers` | array body |
  | PUT | `/suppliers` | array body |
  | PUT | `/suppliers/:entityId` | single |
  | PATCH | `/suppliers/:entityId` | `If-Match` required |
  | DELETE | `/suppliers/:entityId` | |
  | OPTIONS | `/suppliers`, `/suppliers/:entityId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `addressLine1` | string(180) | EQUALS, STRING | — | |
  | `addressLine2` | string?(180) | EQUALS, STRING | — | |
  | `city` | string(100) | EQUALS, STRING | — | |
  | `companyId` | string(255) | EQUALS, ENUM | — | CZ: IČO, PL: REGON |
  | `country` | string?(10) | EQUALS, ENUM | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `deliveryNoteIds` | string? | — | — | |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `email` | string(100) | — | — | |
  | `externalId` | string?(256) | EQUALS, ENUM | — | |
  | `name` | string(180) | EQUALS, STRING | BOTH | |
  | `phone` | string(20) | — | — | |
  | `vatId` | string(255) | EQUALS, ENUM | — | CZ: DIČ, PL: NIP; regex-validated |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `zip` | string(20) | EQUALS, ENUM | — | |
  | `websiteUrl` | string? | — | — | regex-validated |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

### Table

- Doc: https://docs.api.dotypos.com/entity/table/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/tables` | list |
  | GET | `/tables/:tableId` | single |
  | OPTIONS | `/tables`, `/tables/:tableId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_branchId` | integer? | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `_tableGroupId` | long? | EQUALS, ENUM | — | |
  | `_sellerId` | long? | EQUALS, ENUM | — | |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `enabled` | boolean | EQUALS, ENUM | — | |
  | `locationName` | string | EQUALS, ENUM | BOTH | |
  | `name` | string(180) | STRING | BOTH | must not be empty |
  | `positionX` | integer? | — | — | |
  | `positionY` | integer? | — | — | |
  | `rotation` | integer? | — | — | |
  | `seats` | integer? | EQUALS, NUMBER | BOTH | |
  | `tags` | string[] | EQUALS, ENUM | — | |
  | `type` | enum | EQUALS, ENUM | — | see below |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- `type` values (string in JSON): `SQUARE`, `SQUARE6`, `CIRCLE2`, `CIRCLE4`,
  `DELIVERY`, `CHAIR_SINGLE`, `ROUND`, `DOOR`, `GENERIC`, `CAR1`, `CAR2`.
- Response shape: bare array on list. Read-only entity (no write endpoints
  documented).
- ETag: `If-None-Match` on GET.

### Tag

- Doc: https://docs.api.dotypos.com/entity/tag/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/tags` | list |
  | GET | `/tags/:tagId` | single |
  | POST | `/tags` | array body, max 100 |
  | OPTIONS | `/tags`, `/tags/:tagId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `display` | boolean | EQUALS, ENUM | BOTH | |
  | `externalId` | string? | EQUALS, ENUM | — | |
  | `name` | string(650) | STRING | BOTH | must be non-empty and unique; forbidden characters: `` , ^ ? * ( ) [ ] $ ``; cannot start with `.` on creation, except the reserved `.dailymenu` and `.portion` prefixes (see Portion entity) |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list. No PUT/PATCH/DELETE documented for this
  entity (only GET and POST).
- ETag: `If-None-Match` on GET; not documented on POST.

### Tax (VAT rates)

- Doc: https://docs.api.dotypos.com/entity/tax-vat-rates/
- Configuration of taxes; currently the only supported type is VAT.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/taxes` | list |
  | GET | `/taxes/:taxId` | single |
  | POST | `/taxes` | array body |
  | PUT | `/taxes` | array body |
  | PUT | `/taxes/:taxId` | single |
  | PATCH | `/taxes/:taxId` | `If-Match` required |
  | DELETE | `/taxes/:taxId` | |
  | OPTIONS | `/taxes`, `/taxes/:taxId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | long | — | — | |
  | `created` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `deleted` | boolean | EQUALS, ENUM | — | |
  | `flags` | short | BITS | — | see Flags below |
  | `name` | string | STRING | BOTH | must not be blank |
  | `value` | double | — | BOTH | percent, e.g. `20` = 20%; range `<0;100>`; must be unique across taxes (exempted rate is the exception) |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Flags:
  | Bit | Name | Notes |
  |---|---|---|
  | 0 | `TAKE_AWAY_RATE` | |
  | 1 | `EXEMPTED_RATE` | if set, `value` must be `0` |
  | 2 | `DELIVERY_DEFAULT_RATE` | |
- Response shape: bare array on list.
- ETag: `If-Match` on PUT (required to update); required on PATCH; not otherwise
  documented on this page.

### Warehouse

- Doc: https://docs.api.dotypos.com/entity/warehouse/
- Endpoints (base entity):
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/warehouses` | list |
  | GET | `/warehouses/:warehouseId` | single |
  | POST | `/warehouses` | create a list |
  | PUT | `/warehouses/:warehouseId` | replace single |
  | PUT | `/warehouses` | replace/create a list |
  | PATCH | `/warehouses/:warehouseId` | `If-Match` required |
  | DELETE | `/warehouses/:warehouseId` | |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_cloudId` | integer | — | — | |
  | `barcode` | string?(180) | EQUALS, ENUM | — | |
  | `deleted` | boolean | EQUALS, ENUM | BOTH | cannot be `true` on POST/PUT/PATCH |
  | `enabled` | boolean | EQUALS, ENUM | — | |
  | `hexColor` | string?(7) | — | — | |
  | `name` | string(180) | EQUALS, STRING | BOTH | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH; ignored (temporarily) on DELETE.

**Product with stock status** — an extension of the Product entity/schema (see
Product above for the full field list), returned by the sub-endpoints below with 4
extra fields:

| Field | Type | Filter | Sort | Notes |
|---|---|---|---|---|
| `_warehouseId` | long | EQUALS, ENUM | — | |
| `purchasePriceWithoutVat` | double? | — | — | `null` if never stocked up |
| `stockQuantityStatus` | double | NUMBER | — | quantity on this warehouse |
| `stockStatusVersiondate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |

| Method | Path | Notes |
|---|---|---|
| GET | `/warehouses/:warehouseId/products` | list of extended products, standard page/limit/filter/sort, `If-None-Match` |
| GET | `/warehouses/:warehouseId/products/:productId` | single extended product, `If-None-Match` |

**StockUp** — `POST /warehouses/:warehouseId/stockups`. Body:

| Field | Type | Notes |
|---|---|---|
| `_supplierId` | long? | |
| `_closeDeliveryNoteIds` | long[]? | delivery notes to mark closed on success |
| `currency` | string? | default `CZK` if omitted |
| `invoiceNumber` | string | must not be empty |
| `note` | string? | |
| `updatePurchasePrice` | boolean | |
| `items` | array[1,100] | each: `_productId` (long?) or `externalId` (string?) — exactly one required [1]; `purchasePrice` (double?); `quantity` (double, negative for corrections); `sellPrice` (double?) |

[1] One of `_productId`/`externalId` must be non-null per item.

**Transfer** — `POST /warehouses/:warehouseId/transfers` (moves stock from
`_originWarehouseId` into `:warehouseId`). Body:

| Field | Type | Notes |
|---|---|---|
| `_originWarehouseId` | long | |
| `currency` | string? | default `CZK` |
| `invoiceNumber` | string | must not be empty |
| `note` | string? | |
| `updatePurchasePrice` | boolean | |
| `items` | array[1,100] | each: `_productId`/`externalId` (one required), `purchasePrice` (double?), `quantity` (double) |

**Sale** — `POST /warehouses/:warehouseId/sales`. Body:

| Field | Type | Notes |
|---|---|---|
| `currency` | string? | default `CZK` |
| `items` | array[1,100] | each: `_productId`/`externalId` (one required), `note` (string?), `quantity` (double) |

**Stock-taking** — `POST /warehouses/:warehouseId/stock-takings`. Asynchronous: the
call returns a `statusWebhookUrl` to poll. `stockTakingDate` must be after the
warehouse's last stock-taking date (see the next endpoint) and cannot be in the
future.

Request body:

| Field | Type | Notes |
|---|---|---|
| `note` | string? | |
| `stockTakingDate` | timestamp | |
| `items` | array | each: `_productId` (long), `quantity` (BigDecimal, the new absolute quantity) |

Response: `{"_cloudId": <int>, "_warehouseId": <long>, "_stockTransactionId": <long>,
"statusWebhookUrl": <string>}`.

Polling `statusWebhookUrl` (GET) returns `{"status": ..., "reason": ..., "message":
...}` where `status` is `PROCESSING`, `FINISHED` or `FAILED`, and on `FAILED`,
`reason` is one of `STOCK_TAKING_DATE_IN_FUTURE`, `PRODUCT_DOES_NOT_EXIST`,
`PRODUCT_STOCK_TAKING_IN_FUTURE`, `PRODUCT_STOCK_TAKING_AFTER_DATE`, `UNEXPECTED`.

**Get last stock-taking dates** — `POST /warehouses/:warehouseId/stock-taking-dates`.
Body: `{"_productIds": [<long>, ...]}`. Response: array of `{"_productId": <long>,
"lastStockTakingDate": <timestamp>}` — only products with stock-taking history are
included.

### Warehouse Branch

- Doc: https://docs.api.dotypos.com/entity/warehouse-branches/
- An M:N relation entity between branches and warehouses — a branch can see many
  warehouses, and a warehouse can be seen by many branches.
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/warehouse-branches` | list |
  | GET | `/warehouse-branches/:warehouseBranchId` | single |
  | POST | `/warehouse-branches` | create a list; `409`/error if an entity with the same `_branchId`+`_warehouseId` already exists — use PUT/PATCH instead |
  | PUT | `/warehouse-branches/:warehouseBranchId` | replace single; error if it would change `_branchId`/`_warehouseId` to collide with another existing entity |
  | PUT | `/warehouse-branches` | replace/create a list, same collision rule |
  | PATCH | `/warehouse-branches/:warehouseBranchId` | `If-Match` required, same collision rule |
- Fields:
  | Field | Type | Filter | Sort | Notes |
  |---|---|---|---|---|
  | `id` | long? | EQUALS, ENUM | — | cannot be null in PUT/PATCH |
  | `_branchId` | integer | EQUALS, ENUM | — | |
  | `_cloudId` | integer | — | — | |
  | `_warehouseId` | long | EQUALS, ENUM | — | |
  | `flags` | integer | BITS | — | |
  | `visible` | boolean [1] | EQUALS, ENUM | — | whether the branch can see the warehouse |
  | `subscribed` | boolean [1] | EQUALS, ENUM | — | whether the branch is subscribed to warehouse updates |
  | `version` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |
  | `versionDate` | timestamp? | EQUALS, ENUM, NUMBER | BOTH | |

  [1] `visible` can only be set `true` if `subscribed` is also `true`.
- Response shape: bare array on list.
- ETag: `If-None-Match` on GET; `If-Match` on PUT (required to update); required on
  PATCH.

---

## POS Actions

- Doc: https://docs.api.dotypos.com/pos-actions/ (introduction),
  `.../pos-actions/actions/` (full action catalog),
  `.../pos-actions/developer-mode/`, `.../pos-actions/breaking-changes/`,
  `.../pos-actions/version-history/`.
- POS Actions send an action directly to a branch's point-of-sale device (the
  device must be online). The HTTP response is always `200 OK` with an **empty
  body** — the actual result is delivered asynchronously to a webhook.
- This reference (and the one on the site) assumes **Dotypos 2.17+**; capabilities
  needing a newer version are tagged inline, e.g. `(2.18+)`.

### Sending an action

`POST https://api.dotykacka.cz/v2/clouds/:cloudId/branches/:branchId/pos-actions`

- The `Response-url` response header carries the webhook URL from the request body.
  If no webhook (or `null`) was sent, the response body is instead the response from
  your integration's *default* webhook URL.
- If the default webhook URL is used and the device doesn't process the request
  within **21 seconds**, the call returns `404 Not Found`.
- The default-webhook path is rate-limited to **1 concurrent request** per
  `(userId, clientId, cloudId, branchId)` tuple; exceeding it returns `429 Too Many
  Requests`.
- Integers in POS Action responses are **not** sent as strings (unlike the
  Schema-page claim for the rest of the API — recorded as observed, consistent with
  the ETag-examples style).

### Basic request format

```json
{
  "action": "<string>",
  "webhook": "<string>",
  "validity": "<long>",
  "idempotency-key": "<string|null>",
  "user-id": "<long>"
}
```

| Field | Type | Notes |
|---|---|---|
| `action` | string | action name |
| `webhook` | string? | webhook URL for the response |
| `validity` | long? | Unix timestamp; if the device's clock is past this, fails with code `1007` |
| `idempotency-key` | string? | identifies retried webhook calls; auto-generated if omitted, sent back in the `Idempotency-Key` header |
| `user-id` | long? | corresponds to `_employeeId`; resolved for every `order/*` action as the acting user (permissions, seller context); nonexistent ID → code `1008`; omitted → acts as the integration's own API user |

### Shared request types

**Item** (used in `items[]`):

| Field | Type | Notes |
|---|---|---|
| `id` | long | `_productId` |
| `qty` | double? | default `1` |
| `note` | string? | |
| `discount-percent` | double? | overrides and takes priority over any customer discount-group/product fixed-sale discount, even if lower than either; if omitted, the customer's discount group (if any) or the product's fixed sale discount applies |
| `manual-price` | double? | unit price incl. VAT |
| `manual-points` | double? | |
| `tags` | string[]? | |
| `course-id` | long? | |
| `customizations` | customization[]? | omitted = apply the product's default selections; `[]` = no optional customizations |
| `take-away` | boolean? | |

**Item Customization** (in `items[].customizations[]`) — omitting `customizations`
on an item differs from sending `[]`: omitted applies configured defaults, `[]`
means none. Required-minimum-selection categories are always enforced (even if
hidden from the register UI) — violating the bound fails with code `10005`.

| Field | Type | Notes |
|---|---|---|
| `product-customization-id` | long | `_productCustomizationId` |
| `product-id` | long | `_productId` |
| `manual-price` | double? | override incl. VAT |
| `qty` | int? | default `1` |

**Split item**: `id` (long, order item ID from the Response schema's `items[].id`),
`qty` (double, quantity to move).

**Course change**: `order-item-id` (long), `new-course-id` (long?).

**Takeaway change**: `order-item-id` (long), `take-away` (boolean).

**Print type** (`print-type` field): `local` (device printer); `remote` (response
carries ESC/POS content — requires `print-config`, else the response has no print
content; SK also requires `print-email`, response then carries base64 `print-png`;
if the fiscal printout can't be produced remotely, no print is added and
pass-through error `100006` is set); `email` (sends to `print-email`, or the order's
customer email if omitted; SK: on send failure, falls back to filling `print-png`;
same `100006` fallback if fiscal printout unavailable); `none` (no print; **not
allowed in Slovakia** — returns an error).

**Print config** (`print-config`):

```json
{
  "characters": "<integer>",
  "codepage": "<byte>",
  "print-mini": "<boolean>",
  "print-logo": "<boolean>",
  "cut": "<boolean>",
  "append-lines": "<integer>",
  "font": "<integer 0|1>"
}
```

**Pass-through error** — a non-fatal warning surfaced during order processing:

```json
{"code": "<integer>", "description": "<string>", "localized-description": "<string>"}
```

| Code | Meaning |
|---|---|
| 100001 | SK fiscal receipt file not found (likely printed locally instead) |
| 100002 | SK fiscal receipt email not sent — `print-png` should carry the base64 file instead |
| 100003 | SK fiscal receipt base64 encoding failed — `print-png` is `null`; file kept on POS, retrievable via support |
| 100004 | SK fiscal receipt possibly incomplete (fiscal module may not have finished writing it); file kept on POS |
| 100005 | SK fiscal receipt possibly old (file predates processing by >60s); file kept on POS |
| 100006 | Fiscal receipts can only be printed locally |
| 100007 | SK fiscal print of the order failed; subsequent fiscal-printer actions will fail until `order/sk/print-last-document` is called |

### Actions

**Hello** — `{"action": "order/hello"}`. Basic liveness/metadata check.

Response:
```json
{
  "device": "<string>", "appName": "<string>", "timezone": "<string>",
  "registerStatus": "open|closed",
  "version": {"id": "<string>", "code": "<long>", "name": "<string>"},
  "code": "<int>", "deviceTimestamp": "<long ms>"
}
```

**Create order** — `order/create`: `customer-id` (long?), `discount-percent`
(double?), `guest-count` (int?, must be > 0 or code `2012`; ignored if the feature
is license-gated, 2.18+), `table-id` (long?), `user-id` (long?), `note` (string?),
`external-id` (string?), `delivery` (boolean?), `items` (item[]?, empty order if
omitted), `lock` (boolean?, locks for 45s; a later action against an expired lock
gets code `2014`, 2.19+).

**Update order** — `order/update`: `order-id` (long, required), plus
`customer-id`/`discount-percent`/`note`/`lock` as above; `guest-count` follows the
same rule as create, and `null` explicitly clears it (omitted leaves it unchanged).

**Add order items** — `order/add-item`: `order-id` (long), `items` (item[]),
`table-id` (long?, moves the order if given), `lock` (boolean?).

**Split order** — `order/split`: `order-id` (long), `customer-id` (long?, attached
to the new order; a nonexistent ID is silently ignored — code `1010` is not raised
by split actions), `table-id` (long?), `note`/`external-id` (string?, for the new
order), `discount-percent` (double?, applied to the new order), `split-items`
(split item[]), `lock` (boolean?, locks both orders).

**Issue order** (`order/issue`, not allowed in Slovakia): `order-id` (long),
`guest-count` (int?, same rules as update), `print-config`/`print-email`/
`print-type`, `take-away` (boolean?). Optional staff notifications for issued
orders require contacting Dotypos to enable.

**Pay issued order** (`order/pay`, not allowed in Slovakia): `order-id` (long),
`payment-method-id` (long).

**Create and issue order** (`order/create-issue`, not allowed in Slovakia): union of
Create order + Issue order fields, plus `payment-method-id` (long).

**Create, issue and pay order** (`order/create-issue-pay`): union of the above plus
`print-append` (string?). If `payment-method-id` is invalid (code `2003`), the
already-created order is **not** removed — find it (e.g. via `order/list`) and pay
or cancel it.

**Split and issue order** (`order/split-issue`, not allowed in Slovakia): Split
order fields plus `print-config`/`print-email`/`print-type`, `take-away` (boolean?).

**Split, issue and pay order** (`order/split-issue-pay`): the above plus
`print-append` (string?), `payment-method-id` (long).

**Issue and pay** (`order/issue-and-pay`): `order-id`, `payment-method-id`,
`guest-count` (same update rule), `print-append`, `print-config`, `print-email`,
`print-type`, `take-away`.

**Cancel order** (`order/cancel`): `order-id` (long); the order must be empty (no
items).

**Change order status** (`order/perform-status-transition`) — **active
development, subject to change**: `order-id` (long), `status-transition` (string —
see Enums → Order status above for the supported transitions).

**Get list of open orders** (`order/list`): `table-id` (long?, all orders if
omitted, `null` for orders with no table), `customer-id` (long?, filters to that
customer; `null` = only orders without a customer; combinable with `table-id`).
Returns the Multiple orders response shape.

**Change item's course** (`order/change-item-course`): `order-id` (long),
`course-changes` (Course change[]; if multiple changes target the same item, only
the last applies).

**Prepare next course** (`order/prepare-next-course`): `order-id` (long). Response
field `next-course-id` gives the next course to prepare, or `null` if none.

**Print last document (SK)** (`order/sk/print-last-document`) — usable only after a
failed fiscal print (result code `70302001`, or pass-through error `100007`).
Response: `{"resultCode": <integer>}` where `0` = `DOCUMENT_PRINTED`, `1` =
`INVALID_COUNTRY` (no-op outside SK), `2` = `NO_DOCUMENT_TO_PRINT`.

**Set/unset item(s) as takeaway** (`order/set-item-takeaway`): `order-id` (long),
`take-away-changes` (Takeaway change[]; last change wins per item).

### Response schema (default)

```json
{
  "order": {
    "id": "<long>", "bkp": "<string>", "completed": "<timestamp>",
    "canceled-date": "<timestamp>", "currency": "<string(3)>",
    "customer-id": "<long>", "course-id": "<long>", "guest-count": "<int>",
    "user-id": "<long>", "external-id": "<string>", "fik": "<string>",
    "flags": "<integer>", "created": "<timestamp>", "note": "<string>",
    "order-number": "<string?>", "order-series-id": "<string>", "paid": "<boolean>",
    "pkp": "<string>", "points": "<double>", "table-id": "<long>",
    "price-total": "<double>", "locked-until": "<timestamp>", "status": "<string>"
  },
  "next-course-id": "<long|null>",
  "items": [
    {
      "id": "<long>",
      "price-with-vat": {"unit-billed": "<double>", "total": "<double>", "unit": "<double>"},
      "price-without-vat": {"unit-billed": "<double>", "total": "<double>", "unit": "<double>"},
      "customizations": ["<see Order item customization response below>"],
      "course-id": "<long>", "name": "<string>", "alternative-name": "<string>",
      "packaging": "<double>", "points": "<double>", "product-id": "<long>",
      "price-in-points": "<double>", "qty": "<double>", "tags": "<string[]>",
      "vat": "<double>", "take-away": "<boolean>"
    }
  ],
  "print": ["<string>"],
  "print-png": "<string?>",
  "code": "<integer>",
  "pass-through-errors": ["<PassThroughError>"],
  "deviceTimestamp": "<long>"
}
```

Notes:
- `points` (order- and item-level): since 2.19, issuing an order without an
  assigned customer clears points to `0` on the order and every item; points never
  change after issue.
- `locked-until`: `null` means either not locked, or locked with no expiration —
  this default response doesn't distinguish the two (see `order/list` below, which
  does).
- `print`: array of base64 receipt contents, one per configured print task with all
  its filters/config applied; omitted if the fiscalized content isn't available
  (with pass-through error `100006`).
- `print-png` (SK): base64 receipt from the fiscal module, present for
  `print-type: "remote"`, or `"email"` when the email send failed.

**Order item customization (response)** — note the field *names* differ from the
request-side Item Customization (`qty`/`manual-price` there become
`quantity`/`unit-price` here):

```json
{
  "id": "<long>", "product-customization-id": "<long>", "product-id": "<long>",
  "name": "<string>", "alternative-name": "<string>", "quantity": "<double>",
  "unit": "<string>", "unit-price": "<double>", "purchase-price": "<double>",
  "discount": "<double>"
}
```

### Multiple orders response schema (`order/list`)

Same `order`/`items` shape as above, wrapped per-order under `orders[]`, plus `code`
and `deviceTimestamp` at the top level. Differences from the default response:
- `items[].take-away` is **always `false`** here — use an action returning the
  default response to read the real value.
- `locked-until` **does** distinguish the two cases: `null` always means not
  locked; a lock with no expiration is returned as the maximal timestamp
  `9223372036854775807`.
- The `points` clearing rule is the same as the default response.

### Error response

On failure, the webhook receives an error envelope instead of an order payload:

```json
{"code": "<integer>", "message": "<string>", "localizedMessage": "<string?>", "deviceTimestamp": "<long>"}
```

`localizedMessage` is only sent by errors that provide one (currently
product-not-found failures). For code `2999`, `message` is the only way to
distinguish the underlying cause.

### Result codes

The HTTP status is always `200 OK` — these are **not** HTTP codes, only the `code`
field in the webhook payload determines the actual result.

| Code | Name | Description |
|---|---|---|
| 0 | `CODE_OK` | |
| 100 | `CODE_OTHER` | |
| 1001 | `REQUEST_ERROR` | |
| 1002 | `CONFIGURATION_ERROR` | current POS configuration doesn't allow this action |
| 1003 | `MISSING_ACTION` | `action` parameter missing |
| 1004 | `UNKNOWN_ACTION` | action unknown/no longer supported |
| 1005 | `DATA_FORMAT_ERROR` | |
| 1006 | `LICENSE_ERROR` | insufficient license for this action |
| 1007 | `EXPIRED_REQUEST` | `validity` has passed |
| 1008 | `USER_NOT_FOUND` | `user-id` not found |
| 1009 | `ACTION_NOT_ALLOWED_FOR_COUNTRY` | action disallowed for the POS's country |
| 1010 | `CUSTOMER_NOT_FOUND` | `customer-id` not found on the register; **not** raised by split actions (silently ignored there) |
| 1011 | `TABLE_NOT_FOUND` | `table-id` not found |
| 1999 | `SERVER_ERROR` | |
| 2001 | `ORDER_LOCKED` | locked by another service/user |
| 2002 | `ORDER_NOT_FOUND` | |
| 2003 | `ORDER_PAYMENT_METHOD_NOT_FOUND` | |
| 2004 | `ORDER_PAID` | already paid |
| 2005 | `ORDER_ISSUED` | already issued |
| 2006 | `ORDER_NOT_ISSUED` | not issued yet |
| 2007 | `INVALID_PRINT_EMAIL` | SK: `print-email` must be a valid email for the given `print-type` |
| 2008 | `INVALID_PRINT_TYPE` | SK: `print-type: none` not supported |
| 2009 | `ORDER_NOT_EMPTY` | order still has items |
| 2010 | `ORDER_ALREADY_CANCELED` | |
| 2011 | `ORDER_IS_EOS_ORDER` | created by EOS, cannot be modified here |
| 2012 | `ORDER_INVALID_GUEST_COUNT` | must be > 0 |
| 2013 | `ORDER_NO_ITEMS_TO_SPLIT` | |
| 2014 | `ORDER_LOCK_EXPIRED` | (2.19+) re-lock and retry |
| 2015 | `ORDER_OPEN_ORDERS_LIMIT_REACHED` | (2.18+) max 500 open orders reached |
| 2999 | `ORDER_OTHER` | catch-all (permission, discount, points/credits, stock, merge, customer-selection, etc.) — use `message` |
| 3001 | `REGISTER_CLOSED` | |
| 4001 | `PRODUCT_NOT_FOUND` | |
| 5001 | `ORDER_ITEM_NOT_FOUND` | |
| 5002 | `ORDER_ITEM_LOW_QUANTITY` | |
| 5003 | `ORDER_ITEM_NEGATIVE_QUANTITY` | |
| 6001 | `ORDER_TRANSITION_UNKNOWN` | transition not recognized (possibly old Dotypos version) |
| 6002 | `ORDER_TRANSITION_DENIED` | |
| 7001 | `COURSE_CHANGE_NOT_ALLOWED_FOR_PRODUCT` | |
| 7002 | `COURSE_CHANGE_ITEM_ALREADY_PREPARED` | |
| 8001 | `TAKE_AWAY_NOT_ENABLED` | |
| 8002 | `TAKE_AWAY_NOT_ALLOWED_FOR_PRODUCT` | |
| 8003 | `TAKE_AWAY_NOT_ALLOWED_FOR_ORDER` | |
| 8004 | `TAKE_AWAY_NOT_ALLOWED_FOR_ORDER_WITH_NO_TAKEAWAY_ITEMS` | |
| 10001 | `CUSTOMIZATION_NOT_FOUND` | |
| 10002 | `CUSTOMIZATION_CATEGORY_NOT_FOUND` | |
| 10003 | `PRODUCT_NOT_FOUND` (customizations context) | |
| 10004 | `PRODUCT_NOT_FOUND_IN_CUSTOMIZATION` | |
| 10005 | `INVALID_SELECTED_QUANTITY` | quantity outside the customization's bounds |
| 70302001 | `BLOCKED_BY_FAILED_PRINT` | SK: fiscalization failed because the previous document isn't printed — call `order/sk/print-last-document` first |
| 70302002 | `ISSUED_FISCALIZATION_FAILED` | SK: order issued but fiscalization failed |

### Developer Mode

Available in Dotypos 2.16+ and Mobile Waiter 2.8+; opens early-access POS-action
capabilities. Enable: App Settings → About App → long-tap the app name/version
title (a red "Developer mode" label appears bottom-left). Disable: App Settings →
Overview → terminate Developer Mode. Currently gates: early access to the "multiple
quantity customization items" feature and its per-item-limit configuration.

### POS Actions breaking changes

**Multiple quantity customization items (planned 2026-04, Dotypos 2.17+):** allows
adding a customization item multiple times per order item (covers BOGO, 2+1,
multiple toppings of the same type). Default behavior is unchanged (qty still
limited to 1 per item) until configured. Test via Developer Mode (2.16+), which lets
you configure individual customization-item limits. Updated Product Customization
endpoints carrying the new per-item quantity-limit field were planned for January
2026.

### Version history (capability → minimum Dotypos version)

| Since | Added |
|---|---|
| 1.234 | `lock` param; split actions (`order/split`, `order/split-issue`, `order/split-issue-pay`); combined `order/create-issue`/`order/create-issue-pay`; item customizations in requests; `order/perform-status-transition`; `status`/`course-id`/`price-in-points` in responses |
| 1.235 | `order/list` |
| 1.237 | Courses (`course-id`, `order/change-item-course`, `order/prepare-next-course`) and takeaway (`take-away`, `order/set-item-takeaway`) |
| 1.238.12 | `discount-percent` on order creation |
| 1.239.8 | `order/hello`, `deviceTimestamp` in responses |
| 1.241 | optional staff notifications for issued orders (enabled on request) |
| 1.242 | pass-through errors, `print-png`, result code 1009 |
| 1.243 | `order/cancel`, `canceled-date`/`order-number` in responses, `print-logo`, pass-through error 100006 |
| 2.1 | `idempotency-key` |
| 2.9 | `manual-price` on customization items |
| 2.10 | `order/sk/print-last-document`, result code 70302001 |
| 2.12 | result code 70302002 |
| 2.14 | `guest-count`, result code 2012 |
| 2.16 | Developer Mode |
| 2.17 | `qty` on customization items, result codes 1010/1011/2013 |
| 2.18 | result code 2015 (open-orders limit), license gating of `guest-count` |
| 2.19 | result code 2014 (`ORDER_LOCK_EXPIRED`) |

---

## Others

### Base Sales Report

- Doc: https://docs.api.dotypos.com/others/reports/base-sales-report/
- `GET /branches/:branchId/sales-report` — generates a sales report for a branch.
- Query params: `vatPayer` (boolean), `dateFrom`/`dateTo` (string — ISO date-time
  with zone offset, or Unix ms timestamp), `_sellerId` (number, filter or `null` for
  all sellers), `lang` (2-letter lowercase ISO language code).
- Response (top-level fields; nested arrays' own field descriptions follow):

  ```json
  {
    "created": "<timestamp>", "from": "<timestamp>", "to": "<timestamp>",
    "registerName": "<string>",
    "moneyTransactionInfo": { "...": "see below" },
    "revenue": { "...": "see below" },
    "discounts": ["..."], "cashInOutTransactions": ["..."],
    "categorySales": ["..."], "productSales": ["..."], "tagSales": ["..."],
    "employeeSales": ["..."], "customerSales": ["..."], "proxySales": ["..."],
    "takeawaySales": ["..."], "fiscalizationSales": ["..."], "receiptInfo": ["..."],
    "employeePayments": ["..."], "employeeTips": ["..."],
    "paymentMethodTips": ["..."], "writeoffs": ["..."]
  }
  ```

  - `moneyTransactionInfo`: `currency`, `saleCount`/`saleValue`/`rawSaleValue`,
    `cancelCount`/`cancelValue`/`rawCancelValue`,
    `cashAdvanceCount`/`cashAdvanceValue`/`rawCashAdvanceValue`,
    `cashInCount`/`cashInValue`/`rawCashInValue`,
    `cashOutCount`/`cashOutValue`/`rawCashOutValue`, plus
    `alternativeCurrency{Sales,Cancel,CashAdvance,CashIn,CashOut}Amount` objects
    (`{amount, currencyCode}`).
  - `revenue`: `totalWithVat`/`totalVat`/`totalWithoutVat`;
    `vatInfo[]` (`rate`, `base`, `value`, `special`); `paymentTypeInfo[]` (`typeId`,
    `count`, `total`, `rawTotal`, `currency`).
  - `discounts[]`: `employee` (`name`, `id`), `count`, `value`,
    `valueWithoutVat`.
  - `cashInOutTransactions[]`: `id` (money log ID), `dir` (boolean, **always
    `false`, unused**), `created`, `employee` (`name`, `id`), `value`, `note`.
  - `categorySales[]`/`productSales[]`/`tagSales[]`/`employeeSales[]`/
    `customerSales[]`/`proxySales[]` (by EET subject)/`takeawaySales[]`/
    `fiscalizationSales[]`: all share `value`, `valueWithoutNonPurchase`,
    `purchaseValue`, `valueWithoutVAT`, `valueWithoutNonPurchaseWithoutVAT`,
    `purchaseValueWithoutVAT`, plus an entity-specific `id`/`name` pair —
    `productSales` additionally has `count` (integer), `unit` (enum),
    `categoryId` (long); `takeawaySales`/`fiscalizationSales` use `id` as a
    `1`/`0` flag (take-away/eat-in, or fiscalization-required/not) instead of a
    real entity ID and have no `name`.
  - `receiptInfo[]`: `type` (`"cancelled"`, `"cancellation"`, `"unpaid"`,
    `"total"`), `value`, `count`.
  - `employeePayments[]`: `employeeId`, `employeeName`, `paymentMethodId`, `count`,
    `total`, `rawTotal`, `currency`.
  - `employeeTips[]`: `name`, `currency`, `tipAmount`, `tipAmountMainCurrency`.
  - `paymentMethodTips[]`: `name` (localized via `lang`), `currency`, `tipAmount`,
    `tipAmountMainCurrency`.
  - `writeoffs[]`: `productId`, `name`, `count` (double), `pricePurchaseWithoutVat`,
    `unit` (string), `currency`.

### Webhook

- Doc: https://docs.api.dotypos.com/others/webhook/
- Endpoints:
  | Method | Path | Notes |
  |---|---|---|
  | GET | `/webhooks` | list all configured webhooks |
  | POST | `/webhooks` | register a new webhook |
  | DELETE | `/webhooks/:webhookId` | |
- Fields:
  | Field | Type | Notes |
  |---|---|---|
  | `id` | long | |
  | `_cloudId` | integer | |
  | `_warehouseId` | long? | |
  | `method` | string | `"POST"` or `"GET"` |
  | `url` | string | regex-validated |
  | `payloadEntity` | string | one of `"STOCKLOG"`, `"POINTSLOG"`, `"PRODUCT"`, `"ORDERBEAN"`, `"RESERVATION"`, `"CUSTOMER"` |
  | `payloadVersion` | string | only currently-supported value: `"V1"` (field names compatible with API v1) |
  | `versionDate` | timestamp? | ISO or timestamp format |
- No filter/sort markers or ETag documented for this entity.

### Breaking changes (general — non-POS)

Same BC1/BC2 content as the General → Breaking changes section above (this is the
same page cross-linked from multiple places on the site).

### Release notes

- Doc: https://docs.api.dotypos.com/others/release-notes/
- A continuously-updated, dated changelog of Dotypos application releases (mostly
  POS-app bug fixes, occasional "New" feature entries), going back to 2022. **Not
  reproduced here**: it is a live, ever-growing list (100+ dated entries as of this
  scan) rather than API schema/contract information, so copying it would already be
  stale the next time Dotypos ships a release. Consult the page directly if you need
  the history of a specific fix or feature by date.

### Third-party libraries

- Doc: https://docs.api.dotypos.com/others/third-party-libraries/
- Community/third-party API v2 clients, not maintained by Dotypos. As of this scan,
  lists one: a Java client
  (https://github.com/grizzlysoftware/dotykacka-java-api-v2-client, Java 11, Gradle
  7.2, no Base Sales Report/POS Actions support). Not reproduced further — this
  page is a directory of clients, not API schema/contract information.

---

## Guides

### Getting started

- Doc: https://docs.api.dotypos.com/getting-started/
- To use the API, register a Client Application via Dotypos's registration form;
  after review you receive a Client ID/Client Secret and a testing license key
  (creating a Dotypos account with it also creates a testing Cloud).
- Testing requires installing the Dotykacka application and activating the license.
- A Postman collection is available for first steps; see Third-party libraries
  above for community client libraries.

### Authorization

- Doc: https://docs.api.dotypos.com/authorization/
- Two-step flow: obtain a **Refresh Token** once (via a browser-based consent
  redirect), then exchange it for short-lived **Access Tokens** as needed.

**Step 1 — Connector endpoint (obtaining the Refresh Token).**

- `POST https://admin.dotykacka.cz/client/connect/v2` — **this is not a REST
  endpoint**; it must be opened via a browser (form submission) so the user can
  grant consent interactively.
- **The older `GET /client/connect` endpoint is explicitly documented as
  deprecated** — the site links a migration guide to move callers to this POST v2
  flow. (`GET` used `client_id`/`client_secret`/`scope`/`redirect_uri` directly as
  query parameters, with `client_secret` sent in plaintext in the URL and no
  timestamp/signature; that shape still appears in the Delivery Notes Integrations
  guide's own testing-setup instructions, side by side with the deprecation notice
  on this page — recorded as observed, not reconciled.)
- Request parameters (form-encoded, `application/x-www-form-urlencoded`), all
  required except `state`:
  | Name | Type | Notes |
  |---|---|---|
  | `client_id` | string | issued at registration |
  | `timestamp` | integer | Unix seconds, current time |
  | `signature` | string | HMAC-SHA256, see below |
  | `scope` | string | only supported value: `*` |
  | `redirect_uri` | string | where the user returns to after granting access |
  | `state` | string? | CSRF-protection value, echoed back unchanged |
- **Signature calculation:** `signature = hex(HMAC_SHA256(key: client_secret,
  message: String(timestamp)))` — 64 hex characters. Example:
  `timestamp = 1704123456` → `message = "1704123456"` →
  `signature = "a1b2c3d4e5f6..."`.
- Time window: the timestamp must be within roughly ±1 minute of the server's clock
  at connection time; the user then has ~15 minutes to complete login and cloud
  selection. Both failure modes surface as "The connection has expired. Check the
  time settings on your device." — fix by NTP-syncing the client's clock.
  "Unknown client application or wrong application secret" means `client_id` is
  wrong, or the HMAC signature/`client_secret` used to compute it is wrong.
- On success, the browser is redirected to `redirect_uri` with query params: `token`
  (the Refresh Token), `cloudid` (the selected Cloud ID), `state` (echoed back, only
  if it was sent). Example: `https://your-app.com/callback?token=abc123def456&cloudid=789&state=random-csrf-token`.
- The Refresh Token never expires and should be stored safely.

**Step 2 — Access Token.**

`POST https://api.dotykacka.cz/v2/signin/token` — body must be JSON
(`form-data` is rejected). Default validity is one hour (not guaranteed to stay
exactly that).

| Header | Value |
|---|---|
| `Authorization` | `User <refreshToken>` |

Request body:
- `{"_cloudId": <cloudId>}` — standard case; the returned Access Token only grants
  access to that one cloud. To use another cloud, request a new Access Token with
  its ID.
- `{}` (empty object) — special case: the token only allows `GET /clouds` (listing
  clouds); every other `:cloudId`-scoped endpoint is denied.

Response: `{"accessToken": "eyJ0.eyJ1.eyJ2..."}` (`201`), or `401` on failure.

**Step 3 — using the Access Token.** Every authenticated request needs header
`Authorization: Bearer <accessToken>`.

### Delivery Notes Integrations

- Doc: https://docs.api.dotypos.com/delivery-notes-integrations/
- Guide for suppliers integrating electronic delivery notes/remittances so
  customers can stock in items without manual entry.
- Flow: supplier uploads the delivery-note XML via the API → it appears in the
  customer's Warehouse app → the customer may adjust prices/quantities before
  confirming stock-in → confirming updates product prices/stock quantities and
  marks the delivery note `Stacked`.
- Registration is the same Client Application flow as Getting started.
- **Testing setup** (simplified, not for production) uses the connector URL
  `https://admin.dotykacka.cz/client/connect?client_id={client_id}&client_secret={client_secret}&scope=*&redirect_uri=https://dotykacka.cz`
  — i.e. the **deprecated GET connector** (see Authorization above) — opened
  directly in a browser; the Refresh Token comes back as `?token=...&cloudid=...`
  on the redirect to `https://dotykacka.cz`.
- **Production setup** should instead: generate the connector URL params and
  auto-redirect the user; point `redirect_uri` at your own webhook; use `state` for
  CSRF protection; store the Refresh Token safely (it never expires).
- Access Token: `POST https://test.api.dotykacka.cz/v2/signin/token` with header
  `Authorization: User {RefreshToken}` and body `{"_cloudId": {cloudId}}` — note the
  `test.` subdomain used in this guide's example, versus `api.dotykacka.cz` in the
  Authorization guide; recorded as observed, not reconciled.
- A quick access check (development only, skip in production for performance):
  `GET /clouds/{cloudId}` with `Authorization: Bearer {AccessToken}` — `200` +
  cloud info means the token/cloud pair is good.
- Uploading: see the Delivery Note entity section above for the endpoint, XML
  structure and validations.
- Managing products: see the Product entity section above. Reiterated guidance:
  POST never expects an ID (always creates); PUT requires an ID (generate one
  client-side to create via PUT); PATCH accepts only the fields being changed;
  ETags are mandatory when editing; batch PUT/PATCH must preserve the order from
  the GET response the ETag came from; **do not round price values** (see Prices
  above).

---

## Summary table: list-endpoint response shape

Every list endpoint scraped for this reference (all 30 Entity domains, Webhook)
returns a **bare JSON array**, not the wrapper object described on the general
Paging page — confirmed by each entity's own worked example. Exceptions/notes:

- **Cloud Manifest** has no list endpoint at all (single aggregated object per
  cloud).
- **Daily Menu** and **Daily Menu Product** show only a `// Response` placeholder
  on their list endpoints (no concrete example) — consistent with the rest of the
  API by inference, not directly confirmed.
- **Customer Account** / **Customer Account Log** — "search" endpoints return bare
  arrays; the "for a specific customer" single/list endpoints return a single
  object or array respectively, scoped by URL rather than by `filter`.
- **Portion**'s `POST` endpoint takes/returns a single object (not a list), unlike
  every other entity's batch-oriented `POST`.
- **Warehouse**'s sub-resources (`stockups`, `transfers`, `sales`, `stock-takings`,
  `stock-taking-dates`) are single-object operations, not list-oriented CRUD.
