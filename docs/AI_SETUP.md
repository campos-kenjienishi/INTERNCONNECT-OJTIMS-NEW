# AI Setup (Gemini)

This project supports both a local runtime (Ollama) and an external provider (Gemini). The app is configured to use Gemini by default and will fall back to an internal summary generator if Gemini is not configured.

## Quick steps to enable Gemini

1. In your Google Cloud project, enable the Generative Language / Vertex AI API (APIs & Services → Library).
2. Create credentials:
   - For quick testing: create an API key (APIs & Services → Credentials → Create credentials → API key). Restrict by API and IP.
   - For production: create a Service Account, grant `Vertex AI User`, create a JSON key, and use `gcloud auth print-access-token` to obtain a short-lived token.
3. Set environment variables in `.env`:

```
AI_PROVIDER=gemini
GEMINI_API_ENDPOINT=https://generativelanguage.googleapis.com/v1/models/MODEL:generate
GEMINI_API_KEY=<your_api_key_or_access_token>
AI_MODEL=<optional model name>
AI_CACHE_TTL=300
```

4. Clear config cache and run the test:

```powershell
php artisan config:clear
php artisan ai:test
```

The `ai:test` command will report whether Gemini is configured and print the summarization result (or the fallback summary if Gemini is not available).

## Cost control and safety
- Add a budget in Billing → Budgets & alerts.
- Restrict API keys by API and IP.
- Cache responses (`AI_CACHE_TTL`) to reduce calls.
- Implement rate limiting at the web/app layer if you expect bursts.

## Rollback / Fallback
- If Gemini calls fail, the application will use an internal fallback summary generator so the UI remains functional.

## Removing local runtime
- If you no longer want to maintain Ollama, uninstall the Ollama application on hosts and remove any model files under `C:\Users\<user>\AppData\Local\Ollama` (Windows) or `/home/<user>/.ollama`.

If you'd like, I can also add a small CI step to validate `ai:test` on deployment.
