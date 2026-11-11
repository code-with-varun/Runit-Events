# Comprehensive QA Testing Checklist

## 1. Authentication & Security
- [ ] User registration with password strength requirements.
- [ ] Sign in with correct email/password.
- [ ] Invalid credentials produce clear error notice without exposing system details.
- [ ] Admin password reset feature operates correctly from Admin Management.
- [ ] Direct URL access to `/admin` without admin session redirects to login.

## 2. Event Discovery & BookMyShow Catalog
- [ ] Hero slider auto-plays with selected animation transition (Fade/Slide/Zoom).
- [ ] Category pill filters reload catalog seamlessly.
- [ ] Format dropdown (Webinar, Workshop, Corporate Training, Certification).
- [ ] Status filter: "Upcoming Live Only" displays only future events.
- [ ] Status filter: "Completed Replays" displays all concluded masterclasses with replay buttons.
- [ ] Dynamic seat chip reflects capacity state (Filling Fast / Few Seats Left 🔥 / Sold Out).

## 3. Booking & Attendee Passes
- [ ] One-click seat reservation generates unique booking code (`BK-2026-XXXX`).
- [ ] Confirmation page displays Google Calendar / Outlook `.ics` sync button.
- [ ] Attendee Pass PDF downloads cleanly with QR code.
- [ ] Dashboard displays all booked passes.

## 4. Admin Management Console
- [ ] Multi-format image uploads:
  - Square Card (`800 x 800`)
  - Hero Landscape Banner (`1920 x 600`)
  - Portrait Poster (`750 x 1000` / `1080 x 1920`)
- [ ] Post-event video embed sanitizes YouTube watch URLs into embed iframes.
- [ ] Speaker mapping to events.
- [ ] Footer settings CMS saves brand description, address, email, phone, and social URLs.

