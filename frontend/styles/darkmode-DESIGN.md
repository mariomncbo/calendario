---
name: Atelier Chronos Dark
colors:
  background: '#0a0a0c'
  surface: '#0a0a0c'
  surface-dim: '#0a0a0c'
  surface-bright: '#0a0a0c'
  surface-container-lowest: '#18181b'
  surface-container-low: '#121214'
  surface-container: '#18181b'
  surface-container-high: '#1d1d20'
  surface-container-highest: '#27272a'
  on-surface: '#f4f4f5'
  on-surface-variant: '#e4e4e7'
  outline: '#a1a1aa'
  outline-variant: '#27272a'
  primary: '#f4f4f5'
  on-primary: '#0a0a0c'
  primary-container: '#e2e2e3'
  on-primary-container: '#171719'
  secondary: '#c6c6c9'
  on-secondary: '#0a0a0c'
  secondary-container: '#45464e'
  on-secondary-container: '#b4b4bd'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
typography:
  headline-xl:
    fontFamily: Geist
    fontSize: 36px
    fontWeight: '400'
    lineHeight: 44px
    letterSpacing: -0.03em
  headline-lg:
    fontFamily: Geist
    fontSize: 28px
    fontWeight: '400'
    lineHeight: 36px
    letterSpacing: -0.025em
  headline-md:
    fontFamily: Geist
    fontSize: 20px
    fontWeight: '500'
    lineHeight: 28px
    letterSpacing: -0.015em
  body-lg:
    fontFamily: Geist
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: -0.01em
  body-md:
    fontFamily: Geist
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Geist
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0.005em
  label-lg:
    fontFamily: Geist
    fontSize: 13px
    fontWeight: '600'
    lineHeight: 18px
    letterSpacing: 0.02em
  label-md:
    fontFamily: Geist
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.04em
  label-sm:
    fontFamily: Geist
    fontSize: 10px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.06em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-calendar: 0.5rem
  margin: 2rem
  margin-compact: 1.25rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.875rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system embodies a precision-first, architectural approach to the weekly calendar. It operates with quiet authority, rejecting gratuitous decoration in favor of structural clarity and absolute legibility.

The visual style blends high-contrast minimalism with technical utilitarianism: deep obsidian substrates, razor-thin hairlines, and surgical typography make every event and task an object of architectural focus. The atmosphere is composed, nocturnal, and unapologetically focused.

## Colors

Opaque stepped dark values create structured depth without decorative color. Surfaces transition systematically from deep canvas obsidian (`#0a0a0c`) to panel charcoal (`#121214`), elevated tiers (`#18181b`), and active states (`#27272a`).

- **Canvas Background:** `#0a0a0c`
- **Panel / Structural Surface:** `#121214`
- **Elevated Container:** `#18181b`
- **Border / Hairline Divider:** `#27272a` (sharp interactive borders `#3f3f46`)
- **Text Primary (Warm White):** `#f4f4f5`
- **Text Secondary (Chalk):** `#e4e4e7`
- **Text Muted (Zinc Label):** `#a1a1aa`

Interactive emphasis is achieved via high-contrast inversion: primary elements use stark white backgrounds (`#f4f4f5`) with pitch-black typography (`#0a0a0c`). Accent colors for tags and event categorization remain muted, low-saturation monochrome zinc tones to preserve architectural neutrality. The current-day accent is the chalk secondary (`#c6c6c9`).

## Typography

Typographic discipline is anchored entirely by **Geist** (fallback a Plus Jakarta Sans), leveraging its stark geometry and tabular numeral support. Numeric readability is vital: calendar dates, duration indicators, hours, and counters enforce tabular figures (`font-feature-settings: "tnum"`) for pixel-perfect vertical alignment.

Letter spacing is tightly managed:

- Headlines receive slight negative tracking (`-0.03em` to `-0.015em`).
- Labels and micro-metadata (`label-sm`) apply deliberate positive tracking (`+0.02em` to `+0.06em`).
- Hierarchy is achieved through weight (`400` vs `500`/`600`) and tonal brightness (`#f4f4f5` down to `#a1a1aa`), never through excessive size variance.

## Layout & Spacing

The layout is a single full-width weekly grid of 7 day columns.

- **Desktop (≥768px):** `grid-template-columns: repeat(7, ...)`; outer margins `margin` (`2rem`), gutters between columns `gutter-calendar` (`0.5rem`).
- **Mobile (<768px):** Strict single-column stack; margins condense to `margin-compact` (`1.25rem`).
- **Timeline cadence:** Vertical rhythm at 40px per hour; the timeline always ends at 24:00 and its height adapts to the earliest event.
- Spacing obeys strict 4px/8px modular leaps: `space-xs` (4px) for micro tags and inline dots; `space-sm` (8px) for event padding; `space-md` (14px) for container insets; `space-lg` (24px) for section separation.

## Elevation & Depth

Elevation is rendered strictly via tonal layer staging and crisp hairline borders rather than volumetric drop shadows, preserving the ultra-flat, high-precision technical feel.

- **Layer 0 (Canvas Base):** Ground substrate (`#0a0a0c`).
- **Layer 1 (Panels & row-title):** Recessed modules (`#121214`) defined by a 1px hairline border (`#27272a`).
- **Layer 2 (Day cards & event blocks):** Elevated content containers (`#18181b`) framed by a 1px border (`#27272a`).
- **Layer 3 (Modal overlay):** Elevated dialog (`#18181b`) with a sharp 1px border (`#3f3f46`), a diffused backdrop shield (`rgba(10, 10, 12, 0.8)` with an 8px blur), and a restrained shadow used only for the floating overlay: `0 8px 32px -4px rgba(0, 0, 0, 0.6)`.

## Shapes

Geometry is razor-sharp and deliberate. A soft technical corner (`0.25rem`) keeps structural rigidity without visual harshness.

- **Base Radius (`0.25rem`):** Chips, timeline event blocks, buttons, and hover states.
- **Card & Modal Radius (`0.5rem`):** Day cards and the settings dialog.
- **Pills (`full`):** Reserved for the today badge and the sync status dot.

## Components

### Header
Fixed top bar with a 24px backdrop blur, separated from the canvas by a 1px hairline bottom border (no shadow). Contains the brand mark, the Google sync status (a small chalk dot + label), navigation links, and a settings icon button.

### Buttons
- **Icon Buttons (settings, close):** Borderless, 18px icon in `outline`. Hover shifts to the `#18181b` surface. Focus is a crisp 1px `outline` ring without browser glows.
- **Segmented Color Mode Selector:** Options split by a 1px hairline. The active option inverts to `primary` (`#f4f4f5`) with `on-primary` text.
- **Account Actions:** Full-width rows; hairline top border between rows; icons in `outline`; hover on `#18181b`.

### Day Cards
Cards stack the header (uppercase day name, day number, `HOY` badge, commitment counter), all-day events, the timeline, and the tasks section.

- **Today:** Chalk top bar, faint zinc background (`rgba(161, 161, 170, 0.08)`), accent border, bolder day number, `HOY` pill.
- **Weekend:** Muted translucent background (`rgba(10, 10, 12, 0.4)`).

### All-Day Chips
Compact 20px-high rows with a `label-sm` title and a 6px chalk dot indicator, on the `#18181b` elevated surface with a 4px radius and a 1px hairline border.

### Timeline Event Blocks
Absolute blocks positioned proportionally to event duration across the shared timeline. A left-aligned 2px chalk accent bar signals the calendar origin. Time reads in `label-sm` secondary; the title is clamped to 2 lines. Overlapping events share the column width side by side.

### Tasks Section
Hairline top border, uppercase `label-sm` heading, and one row per task with a muted `outline` dot, title, and optional detail line.

### Settings Modal
Centered dialog (max 420px) with an `0.5rem` radius on the `#18181b` surface and a `#3f3f46` border. Shows the user profile (name + email), the color mode segmented selector, and the account actions. Closes via button, backdrop click, or Escape.