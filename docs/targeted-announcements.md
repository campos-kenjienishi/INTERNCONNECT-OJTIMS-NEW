# Targeted coordinator announcements

## Implementation plan

1. Resolve eligible OJT professors using local OJT assignments and academic-period designations, never the FLSS faculty role alone.
2. Add audience selection, multiple program selection, academic-period filters, and explicit professor checkboxes to the existing coordinator dashboard.
3. Preview the actual recipients, then persist the announcement and recipient snapshot together.
4. Filter student and professor feeds on the server. Preserve existing class announcements and legacy student broadcasts.
5. Test selection boundaries, authorization, duplicate submissions, and migration safety.

## Coordinator workflow

On the coordinator dashboard, Create Announcement supports Students, OJT Professors, or both. All Programs is separate from professor selection: choosing all programs never automatically changes selected-professor mode into an all-professor broadcast.

The school year defaults to the latest available year. The coordinator can explicitly choose All School Years. An empty semester means All Semesters. Specific semesters require a matching, non-archived class; students without class/semester information are not guessed into that period. The existing `1st Sem`/`1st Semester`, `2nd Sem`/`2nd Semester`, and `Summer`/`Summer Term` values are matched as equivalent.

Eligible professors are active faculty accounts with a matching non-archived class, a matching approved OJT student assignment, or a coordinator-entered designation. Student-assignment fallback excludes students tied to an archived or out-of-scope class. Legacy assignments use names only when a faculty name is unambiguous. Use an explicit designation for duplicate names. Final recipients always use user IDs.

Expand OJT Professor Designations to include an eligible faculty member who has no class/student setup. Designations apply to a program, school year, and optional semester. They do not change the person's account role, FLSS data, or access to student records. Removing a designation affects future selections, not announcements already published.

Preview and Publish shows counts and expandable recipient names. Empty specific-professor selections, invalid programs, and ineligible professor IDs are rejected. A changed form or changed recipient set requires a fresh preview. A publication key, unique database constraint, and coordinator row lock prevent duplicate announcements from retried submissions.

Recipients are an immutable snapshot. Message edits preserve the original audience. To change the audience, publish a new announcement; the edit modal explains this. Assignment changes, new students, and FLSS syncs do not silently expand prior audiences.

## Recipient experience

- Students receive matching coordinator announcements in the existing home feed.
- Professors receive only their targeted coordinator announcements in the new dashboard section, paginated at five per page.
- Old coordinator announcements remain student-only.
- Existing professor announcements remain restricted to their owned classes.
- Email notifications are optional at publication. Read receipts are not implemented.

Only approved students (`users.status = 1`) and supported professor account statuses (`0`, `1`, or `Active`) are eligible for new targeted announcements. Status `0` is the existing default for locally created professor accounts, not a faculty deactivation flag; FLSS imports use `Active`. Student status `0` remains ineligible. Explicit inactive faculty statuses are excluded. The faculty directory alone does not establish OJT eligibility. Programs are matched against local course names; review designations when programs are renamed.

## Deployment

Back up the database before deploying. Run only this migration against the intended database:

```sh
php artisan migrate --path=database/migrations/2026_10_31_000001_add_targeted_announcements.php
php artisan view:clear
```

It adds nullable `targeting` and `publication_key` fields to `announcements`, plus the `announcement_recipients` and `ojt_professor_designations` tables. Existing announcements are not changed or deleted. No files or uploads are touched. Without the migration, targeted publishing stays disabled and existing announcements remain available. Do not roll back after publishing without a database backup: rollback removes the new recipient/designation data.

## Verification

```sh
php vendor/bin/phpunit --do-not-cache-result --filter TargetedAnnouncementTest
node --check public/js/coordinator/announcement-audience.js
```

Manual checks:

1. Select Students and one program; preview the matching names, publish, and compare accounts from selected and excluded programs.
2. Select Professors and specific-professor mode. Confirm that an empty selection is rejected, and only checked professors receive the published announcement.
3. Select Both and All Programs but only one professor. Confirm all matching students and only that professor are included.
4. Try a semester and compare the class assignments; verify excluded/archived faculty do not appear.
5. Designate a faculty member with no class setup, preview the professor list, remove the designation, and check that the member disappears from future selection.
6. Cancel the publication modal; nothing should be saved. Double-click Publish/retry the same submission; only one announcement should exist.
7. Change assignments after preview; publishing should require another preview. Change assignments after publishing; existing recipients should stay unchanged.
8. Confirm legacy student announcements and professor-to-class announcements still work.
9. Check the forms, checklists, preview, designation list, and professor feed on mobile and in dark mode.

Work remains on `dev`; promotion to `main` follows the user's pull-request workflow.

## Email notification phase

### Design and safety

The optional Send email notification checkbox uses the same saved audience. Preview includes valid-email and skipped-email counts. The selected delivery addresses are included in the signed preview, so changing an address after preview requires another confirmation. Missing or invalid addresses are skipped, but the account still receives its in-app announcement.

Publication saves a message/address snapshot and a database outbox in the same transaction. It never connects to SMTP during the web request. The outbox is consumed by `announcements:send-emails`; this works independently of `QUEUE_CONNECTION=sync` and does not change how other application emails are sent. It does not need Laravel's `jobs` table or a persistent queue worker. No emails are created for old announcements, and editing does not resend or change already queued email content.

Each email has one To address and no recipient list in Cc/Bcc. Dashboard links use the configured `APP_URL`, not request headers. Production requires an HTTPS application URL. Recipients must sign in normally; the email does not grant access to the announcement or other records. Email content is HTML-escaped. Outbox addresses and content are private data and should be included in database backup/access controls.

Delivery statuses:

- Queued: waiting for the background command.
- Processing: claimed by a worker; another worker cannot claim it again.
- Sent: accepted by the configured live mail transport. This is not proof of inbox delivery or reading.
- Captured (test): written to a log/array transport, with no real recipient email sent.
- Failed: preparation/configuration failed before transport started. The coordinator can retry these up to three total attempts.
- Skipped: missing/invalid email, removed recipient, changed role, or a suppressed message.
- Needs review: transport started but acceptance could not be confirmed, or a worker was interrupted for at least fifteen minutes. These are never automatically resent; inspect the mail provider's records first.

SMTP cannot guarantee exactly-once delivery across a connection loss. The system prevents concurrent claims and repeat sends for recorded successes and deliberately does not blindly retry uncertain deliveries. A send failure does not remove the published in-app announcement. Deleting an announcement cancels pending outbox rows, but cannot recall email already sent or a send already in progress.

The coordinator's My Announcements table displays counts; refresh to see background updates. Retry failed emails is author-only and does not requeue sent, captured, skipped, processing, or uncertain rows. It also does not reset the three-attempt limit.

### Local setup and manual validation

Only the new email migration is needed after the targeting migration:

```sh
php artisan migrate --path=database/migrations/2026_10_31_000002_add_announcement_email_outbox.php
php artisan config:clear
php artisan view:clear
```

Local settings:

```dotenv
ANNOUNCEMENT_EMAIL_ENABLED=true
ANNOUNCEMENT_EMAIL_MAILER=announcement_log
ANNOUNCEMENT_EMAIL_BATCH_SIZE=10
ANNOUNCEMENT_EMAIL_INTERVAL_MS=1000
```

`APP_ENV=local` always forces the private `announcement_log` transport, even if the announcement mailer is accidentally configured as SMTP. Automated tests use the array transport. Do not change `APP_ENV` to production locally just to test email.

1. Select one known eligible professor or student account with a valid email.
2. Check Send email notification, then preview. Expect one valid email and the test capture warning.
3. Publish. My Announcements should show Queued: 1, with no email sent during the web request.
4. In the project directory run:

   ```sh
   php artisan announcements:send-emails --limit=1
   ```

5. Refresh the dashboard. Expect Captured (test): 1. Inspect `storage/logs/announcement-mail-YYYY-MM-DD.log` for the rendered message, correct dashboard link, and one To address. Keep this file private; it contains email addresses and message content.
6. Run the command again. It should not capture the same delivery twice.
7. Test without the checkbox; no email outbox entry should be created.

The command consumes the oldest queued entries first. If other pending test notifications exist, `--limit=1` may process one of those rather than the newly created announcement.

### Hosted rollout

Production email is opt-in; the example environment file leaves it disabled. On the intended hosted site, deploy and back up the database, run only the email migration above, configure the existing SMTP credentials and a correct HTTPS `APP_URL`, and set:

```dotenv
APP_ENV=production
ANNOUNCEMENT_EMAIL_ENABLED=true
ANNOUNCEMENT_EMAIL_MAILER=smtp
ANNOUNCEMENT_EMAIL_BATCH_SIZE=1
ANNOUNCEMENT_EMAIL_INTERVAL_MS=1000
```

Clear cached configuration after editing environment settings. Confirm the actual mail provider's per-minute, hourly, and daily limits before increasing batch size or scheduling frequency; no provider quota has been verified by this implementation. Check whether your shared hosting also limits PHP execution time.

In hPanel configure a cron command using the hosting account's actual PHP executable and this site's actual application path:

```sh
/path/to/php /path/to/site/artisan announcements:send-emails --limit=1
```

Use the frequency supported by your plan and allowed by the mail provider. Start with a controlled test to an account you own, with other real-recipient outbox entries absent. Do not run the sender against a database containing real queued messages merely to check whether SMTP works. Staging should remain disabled or use `ANNOUNCEMENT_EMAIL_MAILER=announcement_log`.

If an application scheduler cron is already running, the command is also registered every minute through the scheduler; use that OR the dedicated cron, not both. The worker has a shared cache lock and per-row atomic claims. Keep the application cache shared across workers. A command normally processes at most its requested batch and stops taking new messages after about fifty seconds; an in-progress transport call can extend that time. SMTP timeout is thirty seconds per message.

Disabling `ANNOUNCEMENT_EMAIL_ENABLED` pauses delivery; it does not discard existing queued messages. Re-enabling it resumes them. Log/array and failover behavior are not silently treated as live delivery: capture transports produce Captured status, and failover mailers are unsupported because a fallback log must not be reported as a sent email.

This migration only adds `announcements.email_payload` and `announcement_email_deliveries`. It does not alter existing announcements, users, recipient assignments, uploaded files, or global queue/mail defaults. Do not roll back after use without preserving delivery history, since removing that history removes duplicate-send protection.
