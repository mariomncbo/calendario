---
name: Nordic Zen Calendar
colors:
  surface: '#121315'
  surface-dim: '#121315'
  surface-bright: '#38393b'
  surface-container-lowest: '#0d0e10'
  surface-container-low: '#1b1c1e'
  surface-container: '#1f2022'
  surface-container-high: '#292a2c'
  surface-container-highest: '#343537'
  on-surface: '#e3e2e4'
  on-surface-variant: '#c1c8c0'
  inverse-surface: '#e3e2e4'
  inverse-on-surface: '#303032'
  outline: '#8b938b'
  outline-variant: '#424842'
  surface-tint: '#a8d0b3'
  primary: '#a8d0b3'
  on-primary: '#133723'
  primary-container: '#7aa085'
  on-primary-container: '#123622'
  inverse-primary: '#42664e'
  secondary: '#aeceb5'
  on-secondary: '#1a3624'
  secondary-container: '#334f3c'
  on-secondary-container: '#a1c0a7'
  tertiary: '#c8c6c1'
  on-tertiary: '#30312d'
  tertiary-container: '#989792'
  on-tertiary-container: '#2f302c'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#c4ecce'
  primary-fixed-dim: '#a8d0b3'
  on-primary-fixed: '#002110'
  on-primary-fixed-variant: '#2b4e38'
  secondary-fixed: '#caebd0'
  secondary-fixed-dim: '#aeceb5'
  on-secondary-fixed: '#042010'
  on-secondary-fixed-variant: '#314d3a'
  tertiary-fixed: '#e4e2dd'
  tertiary-fixed-dim: '#c8c6c1'
  on-tertiary-fixed: '#1b1c19'
  on-tertiary-fixed-variant: '#474743'
  background: '#121315'
  on-background: '#e3e2e4'
  surface-variant: '#343537'
typography:
  display-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: '600'
    lineHeight: 48px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '600'
    lineHeight: 36px
    letterSpacing: -0.015em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
    letterSpacing: -0.015em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '500'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '500'
    lineHeight: 24px
    letterSpacing: -0.005em
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: 0em
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0.005em
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 18px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.04em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-tablet: 0.75rem
  gutter-mobile: 0.5rem
  margin: 2rem
  margin-tablet: 1.5rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

The brand personality is contemplative, balanced, and deliberately quiet. It strips scheduling of panic, productivity guilt, and visual noise, treating time instead as an open architectural landscape. The target audience includes thoughtful professionals, knowledge workers, writers, and designers who seek mental clarity, rhythm, and intentional living.

The visual style unites **Warm Nordic Minimalism** with Japanese spatial philosophy (*Ma*, negative space). It deliberately avoids harsh pure blacks (#000000) and eye-fatiguing bright whites (#ffffff). Instead, it relies on deep mineral charcoals, softened limestone, mist greys, and tactile muted sage greens. Every surface feels carved from slate or basalt, creating a sanctuary-like dark mode interface that reduces cognitive fatigue during extended planning sessions.

## Colors

The palette is derived from Nordic geology and subarctic flora, specifically engineered for zero-strain dark mode viewing:

- **Primary (`#7aa085`)**: Pale lichen sage. Used for the current active day indicator, primary button fills, interactive focus states, and priority time markers.
- **Secondary (`#4e6b56`)**: Deep forest moss. Used for secondary active indicators, muted timeline highlights, tag containers, and subtle selected states.
- **Tertiary (`#a8a7a2`)**: Cool mist grey. Used for supporting body copy, secondary metadata, inactive week numbers, and structural grid rules.
- **Neutral Base (`#161719`)**: Raw basalt charcoal. Serves as the canvas background (`surface-ground`).

### Extended Surface Tiers
- **Surface Ground**: `#161719` (Canvas, view background)
- **Surface Sunken**: `#111214` (Nested calendar grids, input troughs)
- **Surface Raised (Default Card)**: `#1c1e22` (Event cards, sidebar panels)
- **Surface Overlay (Modals, Popovers)**: `#24272e` (Flyout menus, inspector panels)
- **Border / Divider**: `#2d3038` (Low-contrast hairline dividers, grid bounds)

### Typography & Content
- **Text High-Contrast**: `#e6e5e0` (Limestone white; primary headers, active dates)
- **Text Medium-Contrast**: `#a8a7a2` (Mist grey; body copy, day labels, time markers)
- **Text Low-Contrast / Disabled**: `#595c64` (Off-hours, past events, placeholder text)

## Typography

Typography relies uniformly on **Plus Jakarta Sans**, utilizing its geometric clarity, open apertures, and subtle human warmth to preserve legibility without sterile mechanical coldness.

- **Numerals**: Numerical dates within the calendar grid prioritize medium to semi-bold weights (`500` / `600`) to remain immediately anchorable across dense multi-column schedules.
- **Micro-labels**: Time scales (e.g., `09:00 AM`), day-of-week monograms (`MON`, `TUE`), and status pills leverage `label-sm` with slightly expanded letter tracking (`0.04em`) to guarantee clean scannability against dark surfaces.
- **Hierarchy through Contrast, not Weight**: Hierarchy is expressed by transitioning between Limestone (`#e6e5e0`), Mist Grey (`#a8a7a2`), and Muted Slate (`#595c64`) rather than relying excessively on heavy bold weights.

## Layout & Spacing

The layout is built upon a rigid, proportional grid system paired with generous negative internal space to generate a peaceful, unhurried visual cadence:

- **Desktop (1200px+)**: Multi-pane layout consisting of a fixed 280px left rail (mini-month picker, calendar list, tags), a flexible fluid central view (7-day or month grid), and an optional collapsible 340px right event inspector. Default canvas margin is `margin` (`2rem`) with `gutter` (`1rem`) between columns.
- **Tablet (768px – 1199px)**: Left rail collapses into an off-canvas drawer. The main schedule defaults to a 3-day or condensed 7-day fluid view with `gutter-tablet` (`0.75rem`) and `margin-tablet` (`1.5rem`).
- **Mobile (< 768px)**: Single-column stack. Day view or compact agenda stream with sticky single-row date ribbon. Margins tighten to `margin-mobile` (`1rem`) and grid gaps reduce to `gutter-mobile` (`0.5rem`).
- **Rhythm**: All interactive targets, event chips, and grid cells conform to strict multiples of `0.25rem` (4px baseline), ensuring absolute spatial alignment between time markers and visual blocks.

## Elevation & Depth

Visual hierarchy is constructed through tonal step layers combined with delicate low-contrast outlines. Heavy drop shadows and glossy skeumorphism are strictly avoided to retain an authentic stone-and-mineral tranquility.

- **Ground Level (Zero Depth)**: `#161719` acts as the atmospheric baseline. Grid divisions within the calendar grid are drawn using 1px hairline borders of `#2d3038`.
- **Level 1 (Card & Module Depth)**: Surfaces shift to `#1c1e22`. Outlines use a fine 1px stroke of `#2d3038`. No ambient shadow is used; structural depth is communicated purely through the tonal lift.
- **Level 2 (Popovers, Tooltips, Floating Event Menus)**: Surfaces sit on `#24272e` with an outline of `rgba(168, 167, 162, 0.12)`. Ambient shadow is subtle, diffuse, and tinted: `0 12px 32px -4px rgba(0, 0, 0, 0.45)`.
- **Level 3 (Modal Dialogs & Command Bars)**: Surfaces use `#24272e` surrounded by an edge-lit border of `rgba(122, 160, 133, 0.25)` (sage bloom) and a heavy, diffused backing scrim of `rgba(10, 11, 13, 0.7)` with `backdrop-filter: blur(8px)`.

## Shapes

The design system employs refined, medium-curved geometry. Elements utilize a consistent 0.5rem (8px) base radius, stepping up to 1rem (16px) for major view containers and 1.5rem (24px) for modals:

- **Calendar Event Cards**: Styled with `rounded` (0.5rem / 8px). Creates a soft, pebble-like quality without feeling cartoonish.
- **Today Highlight Indicator**: Circular pill geometry for the numerical date badge within headers.
- **Modals & Flyouts**: `rounded-lg` (1rem / 16px) to reinforce a natural, polished stone feel.
- **Form Fields & Action Buttons**: `rounded` (0.5rem / 8px) to mirror event block contours.

## Components

### Buttons
- **Primary**: Solid Sage fill (`#7aa085`) with deep charcoal text (`#161719`), bold weight (`600`), 0.5rem border radius. Hover state darkens slightly to `#688b71`.
- **Secondary / Ghost**: Background is transparent with a 1px border of `#2d3038` and Limestone text (`#e6e5e0`). Hover state shifts background to `#1c1e22` and border to `#a8a7a2`.
- **Icon Buttons (Navigation, Chevron controls)**: Square 36x36px with 0.5rem radius, `#1c1e22` background, `#a8a7a2` icon fill, transitioning to `#e6e5e0` on hover.

### Calendar Day Cells & Current Day State
- **Standard Day Cell**: Background `#161719`, right and bottom borders in `#2d3038`. Date numbers in `#a8a7a2` (`label-md`).
- **Current Day ("Today")**: Marked with an illuminated solid circular pill around the date number filled with `#7aa085` and text `#161719`. The entire day column has a faint vertical gradient sheen of `linear-gradient(180deg, rgba(122, 160, 133, 0.06) 0%, transparent 100%)`.

### Event Chips & Blocks
- **Standard Event**: `#1c1e22` container, 1px left accent border (2px width) tinted in `#7aa085` or category-specific muted earth tones (slate blue, warm terracotta, dampened mustard). Text is `#e6e5e0`.
- **Focused / Selected Event**: Outer ring of 2px `#7aa085` with background elevated to `#24272e`.

### Input Fields & Search
- **Container**: Fill `#111214`, border 1px solid `#2d3038`, radius 0.5rem, padding `0.5rem 0.75rem`.
- **Typography**: Text in `#e6e5e0`, placeholder text in `#595c64`.
- **Focus**: Border transitions to `#7aa085` with zero outer glow rings.

### Checkboxes & Filter Toggles
- **Checkbox**: 18x18px, 0.25rem radius, border 1.5px `#2d3038`. Checked state fills with `#7aa085` displaying a `#161719` tick icon.
- **Calendar Filter Pills**: Low-contrast background `#1c1e22`, bordered with `#2d3038`, featuring an internal 6px circular dot indicating calendar category color.

### Time Indicator Line (Current Moment)
- An ultra-thin horizontal line (`1.5px`) spanning the active day or current week view colored in vivid `#7aa085`, anchored on the left by an illuminated 6px circular sage bead.