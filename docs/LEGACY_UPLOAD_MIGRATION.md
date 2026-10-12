# Legacy Upload Migration

This migration copies database-referenced documents from `public/assets` into `storage/app/private/uploads`. It does not rename database values, modify document records, overwrite conflicting private files, or delete legacy source files.

## Safety Model

- Dry-run is the default; copying requires `--execute`.
- Production copying additionally requires `--force`.
- Every source must have a safe filename and remain physically inside the configured legacy directory.
- Every source passes the current extension, MIME, document-structure, size, and malware checks.
- Malware scanning must normally be operational. `--allow-unscanned` is permitted in production only for the documented Hostinger Business Web Hosting exception, after its hPanel Malware Scanner reports clean.
- Files are copied to a temporary private path, SHA-256 verified, moved into place, and verified again.
- An existing destination is never overwritten. Matching files are reported as `already_migrated`; differing files are reported as `conflict`.
- Temporary migration files may be cleaned up, but existing legacy and private documents are never deleted by this command.
- Each run writes a JSON audit report under `storage/app/migration-reports`.
- Files with no database reference are not copied because their correct authorization category cannot be inferred. They remain untouched and are listed under `orphaned_sources` in the report.

The command inventories these database references:

| Table | Private directory | Policy |
| --- | --- | --- |
| `uploaded_files` | `templates` | PDF and approved Office templates |
| `file_requirements` | `requirements` | PDF |
| `file_requirements` with `Notarized MOA` | `moa` | PDF |
| `companies` | `moa` | PDF |

Duplicate references to the same destination and filename are copied only once and listed together in the report.

## Commands

Check scanner readiness:

```bash
php artisan uploads:scanner-status
```

Run the safe inventory and validation pass:

```bash
php artisan uploads:migrate-legacy
```

Copy files outside production:

```bash
php artisan uploads:migrate-legacy --execute
```

Copy files in production after reviewing the dry-run report:

```bash
php artisan uploads:migrate-legacy --execute --force
```

For the confirmed Hostinger Business Web Hosting environment, where no synchronous scanner interface is available, first confirm the hPanel Malware Scanner reports clean and then run:

```bash
php artisan uploads:migrate-legacy --execute --force --allow-unscanned
```

Rerun the dry-run after execution. A complete migration reports only `already_migrated`. The command is resumable, so a failed or interrupted run can safely be executed again.

## Report Statuses

- `pending`: Validated and ready to copy during an execution run.
- `migrated`: Copied and checksum-verified successfully.
- `already_migrated`: An identical private copy already exists.
- `missing_source`: A database record references a file that is absent.
- `security_rejected`: Validation or malware scanning rejected the source.
- `unscanned`: Scanning is disabled and was not explicitly allowed.
- `conflict`: A private file exists with different contents; neither file was changed.
- `copy_failed` or `verification_failed`: The private copy could not be safely completed.
- `evidence_failed`: The file copy is intact, but its database security record could not be created; fix the database and rerun.
- `unsafe_filename`, `unsafe_source`, or `unsafe_destination`: A path safety rule rejected the record.

Any status other than `pending`, `migrated`, or `already_migrated` requires review. Do not manually replace a conflicting destination until both files have been identified and backed up.

## Hostinger Production Runbook

1. Create a Hostinger backup of website files and the database. The migration does not modify the database, but a deployment backup is still required.
2. Deploy the approved `main` branch through hPanel Git deployment. Confirm the selected repository branch before deploying.
3. Enable SSH in hPanel under the website's **SSH Access** section, then connect using the command shown by Hostinger.
4. Change to the Laravel project directory—the directory containing `artisan`—and confirm it with `pwd` and `ls -la`.
5. Configure the confirmed Hostinger Business Web Hosting controls in `.env`:

   ```env
   UPLOAD_MALWARE_SCAN_ENABLED=false
   UPLOAD_MALWARE_SCAN_REQUIRED=false
   UPLOAD_HOSTING_SCANNER=monarx
   UPLOAD_SECURITY_EVIDENCE_ENABLED=true
   UPLOAD_INTEGRITY_CHECK_ENABLED=true
   ```

6. Rebuild cached configuration and verify the scanner policy:

   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan uploads:scanner-status
   ```

7. Run the dry-run and download its JSON report from `storage/app/migration-reports` using hPanel File Manager or SFTP:

   ```bash
   php artisan uploads:migrate-legacy
   ```

8. Resolve all `missing_source`, `conflict`, `security_rejected`, and other failure statuses before continuing.
9. Check available disk space. During migration, both original and private copies coexist, so free space must exceed the total `pending` bytes plus normal application headroom.
10. After confirming Hostinger's hPanel Malware Scanner reports clean, execute and then verify:

    ```bash
    php artisan uploads:migrate-legacy --execute --force --allow-unscanned
    php artisan uploads:migrate-legacy --allow-unscanned
    php artisan uploads:verify-security
    ```

11. Test representative student requirements, MOAs, and templates through each authorized role. Confirm direct document URLs under `/assets` return HTTP 403 while CSS and JavaScript assets still load.
12. Keep the original files and all migration reports during the observation period. Their later archival or removal must be a separate, explicitly approved phase.

Hostinger documents SSH availability and connection through hPanel in its [SSH access guide](https://support.hostinger.com/en/articles/1583645-how-to-enable-ssh-access) and documents branch selection in its [Git deployment guide](https://support.hostinger.com/en/articles/1583302-how-to-deploy-a-git-repository).

### Hostinger plan limitation

The application needs a reachable `clamd` service for production uploads and migration. On a Hostinger VPS, install and bind ClamAV to localhost. Hostinger describes VPS plans as self-managed and recommends malware scanners such as ClamAV in its [VPS security guidance](https://support.hostinger.com/en/articles/8224050-how-to-secure-your-vps-from-abusive-activity).

For Web or Cloud hosting without a callable scanner, use the documented provider-scanner exception only after confirming hosting-level protection is active. For this confirmed Business Web Hosting deployment, set `UPLOAD_HOSTING_SCANNER=monarx` and `UPLOAD_MALWARE_SCAN_REQUIRED=false`; the application still enforces MIME, signature, structure, private-storage, authorization, and integrity controls. On any hosting environment without either a callable scanner or confirmed provider protection, keep `UPLOAD_MALWARE_SCAN_REQUIRED=true`. Never expose port `3310` publicly.

## Direct Legacy Access Protection

`public/assets/.htaccess` denies direct HTTP requests for document and executable extensions while leaving frontend subdirectories such as `assets/css` and `assets/js` available. Laravel can still read retained legacy originals for controlled fallback delivery.

LiteSpeed and Apache honor this file. Nginx does not read `.htaccess`; an equivalent server rule must deny document and executable extensions under `/assets` before production cutover.
