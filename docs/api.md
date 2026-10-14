# API

The API is versioned under `/api/v1` and supports both Laravel Sanctum bearer tokens and authenticated first-party browser sessions. The endpoint paths, response formats, authorization, and user ownership scoping are identical regardless of authentication method.

Requests without a valid bearer token or an authenticated session from a configured stateful origin remain unauthorized. Stateful browser requests use the existing `/admin` login session. The request's `Origin` or `Referer` host must be listed in `SANCTUM_STATEFUL_DOMAINS`; local defaults include `localhost` and `127.0.0.1` hosts. Use one consistent host and port for `/admin` and API requests.

Bearer access tokens expire after 30 days, require the `api:read` ability, and should be treated like passwords. Session-authenticated browser calls do not need a bearer token.

## Use the API from an authenticated browser session

After signing in through `/admin`, same-origin browser requests can call the API using the session cookie. For example, from a page served by the app:

```js
const response = await fetch('/api/v1/dogs', {
  headers: { Accept: 'application/json' },
  credentials: 'same-origin',
});

const dogs = await response.json();
```

For state-changing requests, Sanctum's CSRF protection applies. First obtain the `XSRF-TOKEN` cookie from `/sanctum/csrf-cookie`, then send its URL-decoded value in the `X-XSRF-TOKEN` header along with the session cookie. The current API is read-only except for the token-authentication routes.

## Issue a token

Send the account credentials and a label identifying the client device:

```sh
curl -X POST http://localhost:8000/api/v1/auth/token \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"email":"test@example.com","password":"password","device_name":"local-client"}'
```

The response includes a one-time `access_token`. Store it securely; the API does not return the plain token again.

## Use the API

For non-browser clients, send the token in the `Authorization` header:

```sh
curl http://localhost:8000/api/v1/ping \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer YOUR_ACCESS_TOKEN'
```

Available authorized read endpoints:

- `GET /api/v1/ping` — confirm the token works.
- `GET /api/v1/me` — return the authenticated account.
- `GET /api/v1/dogs` and `GET /api/v1/dogs/{dog}` — list or retrieve only the authenticated user's dogs.
- `GET /api/v1/dogs/{dog}/{resource}` — retrieve that dog's `feedings`, `walks`, `play-sessions`, `vet-visits`, `health-flags`, `schedules`, or `documents`.

List endpoints are paginated. Use `?per_page=50` to request a page size from 1 to 100. A dog owned by another account returns `404`, avoiding disclosure of its existence.

## Revoke a token

```sh
curl -X DELETE http://localhost:8000/api/v1/auth/token \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer YOUR_ACCESS_TOKEN'
```

The token issuance endpoint is rate limited. Invalid credentials return `401`; validation failures return `422`; protected routes return `401` when a valid token is missing or expired.