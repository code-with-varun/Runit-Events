# Changelog & Release History

## Version 1.2.0 (November 2026)
### Added
- **Multi-Format Image Uploads**: Dedicated management for Square Cards (`800x800`), Landscape Banners (`1920x600`), and Portrait Posters (`750x1000` / `1080x1920`) with live previews and resolution badges.
- **Zero-Crop Hero Slider**: Responsive edge-to-edge presentation without cropping custom designed banners.
- **Past Highlights & Replay Catalog**: Concluded masterclasses automatically appear under `/explore?status=completed` with video replay and slide deck links.
- **Footer CMS Manager**: Fully editable contact details, social links (LinkedIn, X, YouTube, GitHub), and office address from Admin Management.
- **Admin Password Reset**: Secure password reset functionality directly inside the Admin portal.
- **Dynamic Seat Capacity Urgency**: Real-time status chips (*Filling Fast*, *Few Seats Left 🔥*, *No Seats Left*) without exposing raw seat counts.

### Fixed
- Fixed issue where concluded events were hidden when querying `status=completed`.
- Fixed hero banner cropping on widescreen monitors.
- Fixed video embed URLs to automatically sanitize YouTube, Vimeo, and iframe links.
