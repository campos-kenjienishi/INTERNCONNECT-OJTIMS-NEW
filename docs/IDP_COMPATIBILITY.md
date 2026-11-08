# IdP callback compatibility

Strict callback state matching remains the default. The external IdP currently returns
code-only callbacks in the observed login flow. An operator can explicitly enable
compatibility without changes to other applications or to the IdP.

## Deployment

Deploy the updated application before applying these settings. On production:

```dotenv
IDP_CALLBACK_URL=https://ojt.ncwbsit2027.com/auth/callback
IDP_ENFORCE_STATE=true
IDP_ALLOW_MISSING_STATE=true
IDP_LOGIN_ATTEMPT_SECONDS=600
```

Use the staging site's own registered callback URL when testing there. Keep each
environment's client credentials and callback registration consistent. Never copy
production secrets into a test environment.

Run `php artisan config:clear` from the deployed project directory. Start a fresh login
from InternConnect using one tab. Previously opened callbacks cannot be reused.
No database migration is needed. No mail is sent by the compatibility guard.

## Safeguards and limitations

- Both modes require a pending login initiated in the same browser session.
- Attempts expire after 600 seconds by default; the setting is bounded to 60–900 seconds.
- Starting another login in the same session replaces the previous pending attempt.
- Callback handling consumes the pending attempt, including rejected callbacks.
- Returned state must match, even when compatibility is enabled. Empty, malformed,
  or mismatched state is never treated as an omitted state.
- Cache-backed markers reject concurrent reuse of an attempt and reuse of a presented
  code for 15 minutes. Use persistent, shared application cache across workers/nodes.
  Provider-side authorization-code single-use enforcement is still required.
- Token exchange, JWT signature/time verification, and profile retrieval still run.
- Compatibility acceptance is logged without state, authorization codes, or tokens.
- The legacy `IDP_ENFORCE_STATE=false` setting now selects the constrained code-only
  behavior rather than bypassing all pending-attempt checks. Prefer the explicit
  settings above.

Code-only compatibility does NOT cryptographically bind the returned authorization
code to the initiating browser session. Session presence, expiration, and replay
checks do not replace state or verified PKCE protection against login CSRF. Anyone
holding a different valid code may still attempt login substitution during a pending
window. This is an explicit compatibility trade-off, not equivalent OAuth hardening.

Disable compatibility (`IDP_ALLOW_MISSING_STATE=false`, `IDP_ENFORCE_STATE=true`)
when the IdP supports a state round trip. Do not claim successful state validation in
security-testing evidence for a code-only login.

## Validation

Automated tests use synthetic requests and mocked external services. They do not
prove a live IdP login succeeds. After deployment, verify one authorized account
through login/MFA, the correct returned account and portal, and missing-session,
expired-attempt, mismatched-state, and reused-callback rejection. Hide codes, tokens,
cookies, and unnecessary personal information in evidence.
