# Upload Security Deployment

INTERNCONNECT validates file extensions, MIME types, document signatures, archive expansion limits, and malware scan results before moving an upload out of quarantine. Uploaded documents are stored outside the public web root and are delivered only through authorized application routes.

## Production Policy

Production must use fail-closed malware scanning:

```env
UPLOAD_MALWARE_SCAN_ENABLED=true
UPLOAD_MALWARE_SCAN_REQUIRED=true
CLAMAV_HOST=127.0.0.1
CLAMAV_PORT=3310
CLAMAV_TIMEOUT=30
```

`UPLOAD_MALWARE_SCAN_REQUIRED=true` blocks new uploads whenever scanning is disabled, unreachable, times out, or returns an invalid response. Existing documents remain available.

### Hostinger Business Web Hosting

Hostinger support confirmed that Business Web Hosting uses Monarx hosting-level protection but does not provide a customer-accessible synchronous scanner interface for Laravel. This deployment must represent that limitation honestly:

```env
UPLOAD_MALWARE_SCAN_ENABLED=false
UPLOAD_MALWARE_SCAN_REQUIRED=false
UPLOAD_HOSTING_SCANNER=monarx
UPLOAD_SECURITY_EVIDENCE_ENABLED=true
UPLOAD_INTEGRITY_CHECK_ENABLED=true
```

`scan_status=disabled` means the application did not perform a synchronous antivirus scan. `hosting_scan_status=provider_managed` identifies Monarx as a separate hosting-level control; it must not be interpreted as a clean result for an individual upload.

This exception is acceptable only with the remaining controls enabled: strict content validation, private non-executable storage, generated filenames, authorized delivery routes, sandboxed PDF responses, direct legacy access blocking, hashes, and scheduled integrity verification. A VPS or institution-approved private scanning service should restore synchronous scanning when available.

For local development only, both malware settings may be set to `false`. This allows uploads but records their scan status as `disabled`.

## ClamAV Requirements

- Run `clamd` on the same server or a private network host.
- Keep virus signatures updated with `freshclam` or the operating system's updater service.
- Configure ClamAV's `StreamMaxLength`, `MaxFileSize`, and `MaxScanSize` to at least the application's `UPLOAD_MAX_SIZE_MB` value.
- Never expose ClamAV TCP port `3310` to the public internet. The protocol does not provide application authentication or transport encryption.
- Allow the PHP application to connect to the configured host and port through the local firewall.

If the hosting plan cannot run ClamAV or connect to a private ClamAV service, leave `UPLOAD_MALWARE_SCAN_REQUIRED=true`. Uploads will remain safely blocked until suitable scanning infrastructure is available.

## Deployment Check

After changing environment variables, rebuild Laravel's configuration cache and verify the scanner before accepting uploads:

```bash
php artisan config:cache
php artisan uploads:scanner-status
```

The status command exits successfully only when ClamAV is reachable, or when scanning is intentionally optional and disabled. A production deployment should report `ready` and `Upload malware scanning is operational.`

On the confirmed Hostinger Business Web Hosting deployment, it will instead report `disabled` and optional. This is expected only when the Hostinger-specific environment settings above are active.

Verify recorded file integrity at any time with:

```bash
php artisan uploads:verify-security
```

The command updates evidence statuses but never modifies or deletes uploaded files. Configure Hostinger's Laravel scheduler cron so the weekly integrity check registered by the application can run.

## Operational Testing

1. Confirm a normal PDF upload succeeds while the status command reports `ready`.
2. In an isolated staging environment, upload the standard EICAR antivirus test file using an allowed test profile.
3. Confirm the upload is rejected, the quarantine directory is empty afterward, and a warning appears in the application log.
4. Stop `clamd`, retry a normal upload, and confirm the upload fails closed with a temporary scanner error.
5. Restart `clamd` and rerun `php artisan uploads:scanner-status` before restoring upload access.

Do not use real malware for validation. Do not add the EICAR test content to this repository.

## Incident Notes

Rejected or unscanned files never leave the quarantine directory. The application removes the quarantined copy after every success or failure. Logs include the policy profile, extension, size, scanner reason, and a SHA-256 hash for detected malware; original filenames and file contents are not logged.
