# Process feedback

All full HTML web responses receive the same CSS and JavaScript through
`InjectProcessFeedback`. Print/export responses, downloads, JSON, fragments and
external services are excluded. Existing confirmation dialogs keep their actions.

## Coverage

| Process | Loading | Completion |
| --- | --- | --- |
| Requirement, MOA and template uploads | Actual multipart progress, then server processing | Success modal with filename |
| Requirement approval/denial | Button spinner and loading panel | Toast; bulk actions use a modal |
| Evaluation send/resend, submission and release | Loading panel | Modal |
| Account/password setup/reset and registration | Loading panel | Modal |
| Profile, company and OJT edits | Loading panel | Toast |
| Room/class changes and announcements | Loading panel | Toast |
| MOA link/unlock requests | Loading panel | Modal |
| GuiSIS, IdP, FLSS and PUPTAS synchronization | Existing sync dialog or shared loading panel | Existing summary, with shared styling |
| Report/email operations | Loading panel | Server-confirmed completion modal |
| AJAX reads in cards and AJAX DataTables | Scoped skeleton while requesting data | Actual content |
| Login | Loading panel | Existing redirect; errors use feedback |
| Bud and AI insight panels | Existing typing indicator/shimmer | Actual answer, without a success dialog |

Server-rendered tables do not receive fake loading delays. File downloads do not
claim completion because the browser does not expose whether a download was saved.

## Contracts

- Regular forms keep their methods, fields, validation and redirects. Programmatic
  `form.submit()` is covered, and repeated submissions on a pending form are blocked.
- Multipart POST forms opt into `X-InternConnect-Upload: 1` and retain CSRF fields.
  Middleware translates explicit success/error redirect flash messages into JSON.
  Validation errors return HTTP 422; a redirect without new success flash is not success.
- Uploads retain inputs/files on validation, transport or business failure. They
  navigate to the server-provided same-origin redirect only after acknowledgment.
- AJAX responses are observed without consuming the caller's original body or
  changing its callbacks. A 200 response alone is never interpreted as success.
- Existing SweetAlert2 results are styled and reused. The component works without
  SweetAlert2 too, including accessible dialogs and dismissible notifications.
- Light/dark mode, keyboard focus, and reduced-motion preference are supported.
- `data-feedback="off"` on a form opts out. Forms targeting another window keep
  native behavior. `[data-feedback-skeleton]` marks a container for scoped read loading.

For new explicit workflows, `window.InternConnectFeedback` provides
`begin(options, form, button)`, `end(operation)`, `success(message, mode)` and
`error(message)`. Modes are `toast` and `modal`. Confirm success from the server
before calling `success`.

## Validation

```powershell
node --test tests/Frontend/process-feedback.test.cjs
php vendor/phpunit/phpunit/phpunit --filter=ProcessFeedbackTest --do-not-cache-result
php artisan view:cache
```
