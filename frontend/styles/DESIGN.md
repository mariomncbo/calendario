---
name: Nordic Zen Calendar
colors:
  background: '#faf9f5'
  surface: '#faf9f5'
  surface-dim: '#dbdad6'
  surface-bright: '#faf9f5'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f4f0'
  surface-container: '#efeeea'
  surface-container-high: '#e9e8e4'
  surface-container-highest: '#e3e2df'
  on-surface: '#1b1c1a'
  on-surface-variant: '#444844'
  outline: '#757873'
  outline-variant: '#c4c7c2'
  primary: '#202420'
  on-primary: '#ffffff'
  primary-container: '#353935'
  on-primary-container: '#9fa29d'
  secondary: '#506353'
  on-secondary: '#ffffff'
  secondary-container: '#d0e5d0'
  on-secondary-container: '#546757'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '400'
    lineHeight: 44px
    letterSpacing: -0.03em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '400'
    lineHeight: 36px
    letterSpacing: -0.025em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '500'
    lineHeight: 28px
    letterSpacing: -0.015em
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: -0.01em
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0.005em
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '600'
    lineHeight: 18px
    letterSpacing: 0.02em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.04em
  label-sm:
    fontFamily: Plus Jakarta Sans
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

This design system embodies Nordic Zen and Organic Minimalism, designed for a personal weekly calendar that transforms time management into a contemplative ritual. The aesthetic avoids cold tech tropes, grounding the user in tranquility, spaciousness, and deliberate focus.

The interface relies on tactile micro-borders, muted chalk-and-stone tone relationships, and subdued sage accents: an environment of quiet, understated craftsmanship.

## Colors

The palette draws from Nordic limestone, unbleached linen, weathered slate, and muted boreal flora.

- **Primary (`#202420` — Muted Charcoal):** High-emphasis typography, active states, and primary actions.
- **Secondary (`#506353` / `#d0e5d0` — Soft Sage):** Current-day highlight, today badge, and event accent indicator.
- **Canvas (`#faf9f5` — Warm Bone):** Atmospheric background base.
- **Text:** `#1b1c1a` (main) and `#444844` (variant or muted).

### Functional Tonal Hierarchy
- **Canvas Base:** `#faf9f5` (Warm Bone)
- **Sub-surface / Card Wells:** `#efeeea` (Pale Limestone), hover `#e9e8e4`
- **Card & Active Blocks:** `#ffffff` (Pure Uncoated Paper)
- **Micro-borders / Grid Lines:** `#e9e8e4` – `#c4c7c2` (Linen Weft)
- **Muted Text / Time Guides:** `#757873` (Dry Clay)

## Typography

The type system relies on **Plus Jakarta Sans** across all roles.

- **Headlines:** Light and regular weights (`400`–`500`) with tight tracking (`-0.03em` to `-0.015em`).
- **Body:** Open and legible with standard tracking for fatigue-free reading across dense weekly schedules.
- **Labels & Temporal Markers:** Elevated tracking (`+0.02em` to `+0.06em`) in semi-bold (`600`) for high-scan precision on hours, dates, and status tags.

## Layout & Spacing

The layout is a single full-width weekly grid of 7 day columns.

- **Desktop (≥768px):** `grid-template-columns: repeat(7, ...)`; outer margins `margin` (`2rem`), gutters between columns `gutter-calendar` (`0.5rem`).
- **Mobile (<768px):** Strict single-column stack; margins condense to `margin-compact` (`1.25rem`).
- **Timeline cadence:** Vertical rhythm at 40px per hour; the timeline always ends at 24:00 and its height adapts to the earliest event.
- Spacing obeys 4px/8px modular leaps: `space-xs` (4px) for micro tags, `space-sm` (8px) for event padding, `space-md` (14px) for container insets, `space-lg` (24px) for section separation.

## Elevation & Depth

This system intentionally rejects synthetic drop shadows and diffuse blurs, choosing **Tactile Micro-Borders** combined with **Tonal Surface Layering**.

- **Layer 0 (Canvas):** `#faf9f5` matte warm stone base.
- **Layer 1 (Day track wells):** `#efeeea` recessed background with 1px strokes in `#e9e8e4`/`#c4c7c2`.
- **Layer 2 (Cards & event blocks):** Pure `#ffffff` fill with a crisp 1px tactile border.
- **Layer 3 (Modal overlay):** Dimmed backdrop `rgba(27, 28, 26, 0.5)` with a 4px blur; whispers shadows reserved for the floating dialog: `0 4px 16px rgba(53, 57, 53, 0.04), 0 1px 2px rgba(53, 57, 53, 0.03)`.
- **Now/today indicator:** A clean 3px sage beam on top of the current day card.

## Shapes

A **Soft** roundedness profile for discipline and structural calm.

- **Base Radius (`0.25rem`):** Chips, timeline event blocks, buttons, and hover states.
- **Card & Modal Radius (`0.5rem`):** Day cards and the settings dialog.
- **Pills (`full`):** Reserved for the today badge and the sync status dot.

## Components

### Header
Fixed top bar with a 24px backdrop blur. Contains the brand mark, the Google sync status (a small secondary dot + label), navigation links, and a settings icon button. On light, the header is separated by a soft shadow instead of a hard border.

### Buttons
- **Icon Buttons (settings, close):** Borderless, 18px icon in `outline`. Hover shifts to the `#efeeea` surface. Focus is a crisp 1px `outline` ring without browser glows.
- **Segmented Color Mode Selector:** Options split by a 1px hairline. The active option inverts to `primary` (`#202420`) with `on-primary` text.
- **Account Actions:** Full-width rows; hairline top border between rows; icons in `outline`; hover on `#efeeea`.

### Day Cards
Cards stack the header (uppercase day name, day number, `HOY` badge, commitment counter), all-day events, the timeline, and the tasks section.

- **Today:** Sage top bar, faint sage background (`rgba(208, 229, 208, 0.2)`), bolder day number, `HOY` pill.
- **Weekend:** Muted translucent background (`rgba(219, 218, 214, 0.5)`).

### All-Day Chips
Compact 20px-high rows with a `label-sm` title and a 6px secondary dot indicator, on the `#ffffff` elevated surface with a 4px radius.

### Timeline Event Blocks
Absolute blocks positioned proportionally to event duration across the shared timeline. A left-aligned 2px sage accent bar signals the calendar origin. Time reads in `label-sm` secondary; the title is clamped to 2 lines. Overlapping events share the column width side by side.

### Tasks Section
Hairline top border, uppercase `label-sm` heading, and one row per task with a muted `outline` dot, title, and optional detail line.

### Settings Modal
Centered dialog (max 420px) with an `0.5rem` radius on the `#ffffff` surface. Shows the user profile (name + email), the color mode segmented selector, and the account actions. Closes via button, backdrop click, or Escape.