# Runit Events Hub - Project Specifications & Requirements

## 1. Executive Summary
**Runit Events Hub** is an enterprise-grade corporate event, webinar, and technical workshop management platform developed for **Runit Infotech**. The application adopts a high-conversion, consumer-grade aesthetic inspired by modern booking portals (BookMyShow) tailored specifically for professional corporate events, executive summits, hands-on workshops, and verifiable certification programs.

---

## 2. Core Functional Modules

### A. Public Discovery & Catalog
- **BookMyShow Clean Hero Slider**:
  - Full-width promotional banners with configurable animation presets (Fade, Slide Left/Right, Zoom).
  - Clean image rendering without automatic text overlays to prevent layout clipping and preserve designed typography.
- **Categorization & Filtering**:
  - Live filter by category (AI, Cloud, FinTech, Full-Stack, etc.).
  - Event format filters: Live Webinar, Hands-on Workshop, Corporate Training, Certification Program.
  - Status filters: Upcoming Live Only vs. Completed Replays.
- **Dynamic Urgency & Capacity Status**:
  - Live seat indicators (*Filling Fast*, *Few Seats Left 🔥*, *No Seats Left (Waitlist)*, *Session Completed*).
  - No raw seat numbers exposed to maintain professional corporate standards.
- **Past Highlights & Knowledge Hub**:
  - Dedicated repository of concluded events with session replay videos (YouTube / external embeds).
  - Downloadable master slide decks (PDF/PPT).
  - Verifiable digital certificates.

### B. Booking & Registration Flow
- One-click registration for logged-in professionals with immediate seat reservation.
- Automated generation of unique booking reference codes (e.g. `BK-2026-XXXX`).
- Instant downloadable PDF Attendee Pass with high-resolution tamper-proof QR code.
- One-click calendar sync (.ics file generation for Google Calendar, Outlook, and Apple Calendar).

### C. Admin & Operations Console
- **Multi-Format Image Asset Management**:
  - Square Card Thumbnail (`card_image`): Recommended `800 x 800 px` (1:1 ratio) for catalog grids.
  - Landscape Hero Banner (`banner_image`): Recommended `1920 x 600 px` (16:5 / 3.2:1 ratio) for hero slider and headers.
  - Portrait Poster (`poster_image`): Recommended `750 x 1000 px` (3:4 ratio) or `1080 x 1920 px` (9:16 ratio) for mobile displays and flyers.
- **Speaker Mapping**:
  - Map verified speakers/instructors directly to events during creation or editing.
- **Post-Event Video Embed**:
  - Paste YouTube or external video URLs/iframes; system automatically sanitizes and embeds as a responsive 16:9 player.
- **Footer & Platform Settings CMS**:
  - Real-time management of platform brand descriptions, social links (LinkedIn, X, YouTube, GitHub), contact address, email, and phone numbers.
- **User Management & Password Reset**:
  - Complete list of registered users and instructors.
  - Secure password reset capability for administrators.

### D. Digital Certificate Engine
- Automated verification ledger with public certificate lookup (`/verify-cert`).
- Tamper-proof QR codes linking to authentic credentials.
- Conditional certificate badges shown only for eligible programs.
