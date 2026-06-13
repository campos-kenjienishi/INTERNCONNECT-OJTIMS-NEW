# AI Setup (Gemini)

This project uses an external AI provider for report and analytics summaries. Gemini and OpenAI are supported. If the configured provider is not available or a request fails, the app falls back to an internal summary generator so the UI remains usable.

## Quick steps to enable Gemini

1. In your Google Cloud project, enable the Gemini API / Generative Language API.
2. Create an API key in Google AI Studio or Google Cloud.
3. Restrict the key by API and IP when possible.
4. Set these values in `.env`:

```env
AI_PROVIDER=gemini
GEMINI_API_ENDPOINT=
GEMINI_API_KEY=<your_api_key>
AI_MODEL=gemini-3.5-flash
AI_AUTO_INSIGHTS=false
AI_CACHE_TTL=300
```

`GEMINI_API_ENDPOINT` can stay blank. When blank, the app builds this endpoint automatically:

```text
https://generativelanguage.googleapis.com/v1beta/models/{AI_MODEL}:generateContent
```

## Verify

Clear Laravel's cached config, then run the AI smoke test:

```powershell
php artisan config:clear
php artisan ai:test
```

The command prints the configured provider and a summarization result. A successful Gemini response includes `"source": "gemini"`. A successful OpenAI response includes `"source": "openai"`. If credentials are missing or the request fails, the result uses `"source": "fallback"`.

## OpenAI alternative

Set these values in `.env`:

```env
AI_PROVIDER=openai
OPENAI_API_ENDPOINT=https://api.openai.com/v1/responses
OPENAI_API_KEY=<your_openai_api_key>
OPENAI_MODEL=gpt-4.1-mini
AI_CACHE_TTL=300
```

OpenAI API billing is separate from a ChatGPT Plus subscription. Create and manage API keys from the OpenAI Platform dashboard.

## Cost control and safety

- Add a budget in Billing > Budgets & alerts.
- Restrict API keys by API and IP.
- Cache responses with `AI_CACHE_TTL` to reduce calls.
- Keep `AI_AUTO_INSIGHTS=false` to prevent Gemini calls on page load. Users can generate insight with buttons instead.
- Add route or user-level rate limiting before high-volume production use.

## Rollback / fallback

To disable external AI calls, set:

```env
AI_PROVIDER=fallback
```

The application will continue using internal generated summaries.
