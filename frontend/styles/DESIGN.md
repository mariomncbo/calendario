---
name: Nordic Zen Calendar
colors:
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
  inverse-surface: '#30312e'
  inverse-on-surface: '#f2f1ed'
  outline: '#757873'
  outline-variant: '#c4c7c2'
  surface-tint: '#5b5f5b'
  primary: '#202420'
  on-primary: '#ffffff'
  primary-container: '#353935'
  on-primary-container: '#9fa29d'
  inverse-primary: '#c4c7c2'
  secondary: '#506353'
  on-secondary: '#ffffff'
  secondary-container: '#d0e5d0'
  on-secondary-container: '#546757'
  tertiary: '#2b2113'
  on-tertiary: '#ffffff'
  tertiary-container: '#413626'
  on-tertiary-container: '#af9f8a'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e0e3dd'
  primary-fixed-dim: '#c4c7c2'
  on-primary-fixed: '#191d19'
  on-primary-fixed-variant: '#444844'
  secondary-fixed: '#d3e8d3'
  secondary-fixed-dim: '#b7ccb8'
  on-secondary-fixed: '#0e1f12'
  on-secondary-fixed-variant: '#394b3c'
  tertiary-fixed: '#f2e0c9'
  tertiary-fixed-dim: '#d5c4ae'
  on-tertiary-fixed: '#231a0c'
  on-tertiary-fixed-variant: '#514535'
  background: '#faf9f5'
  on-background: '#1b1c1a'
  surface-variant: '#e3e2df'
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

This design system embodies Nordic Zen and Organic Minimalism, designed specifically for a full-screen desktop weekly calendar that transforms time management into a contemplative, architectural ritual. The aesthetic avoids cold tech tropes and chaotic scheduling dashboards, grounding the user in tranquility, spaciousness, and deliberate focus.

### Personality & Emotional Tenor
- **Tranquil & Grounded:** Emits an immediate physiological sigh of relief upon viewing. The atmosphere feels more like a sunlit Scandinavian gallery or a stone tea room than an administrative productivity utility.
- **Architectural Precision:** Every column, time-marker, and event block relates harmoniously to an intentional, tectonic spatial rhythm.
- **Organic Restraint:** Tactile micro-borders, muted chalk-and-stone tonal relationships, and subdued sage accents produce an environment of quiet, understated luxury.

### Target Audience
Creative directors, architects, writers, and executives who value clarity over clutter, intentional pacing over frantic multitasking, and craftsmanship in digital everyday tools.

## Colors

The color palette draws directly from Nordic limestone, unbleached linen, weathered slate, and muted boreal flora. 

- **Primary (`#353935` - Muted Charcoal):** Replaces harsh pure black. Used for high-emphasis typography, primary actions, and anchor strokes.
- **Secondary (`#768A78` - Soft Sage):** A tranquil, desaturated herbal green used for active state indications, focus highlights, current time indicators, and restorative scheduling blocks.
- **Tertiary (`#A99985` - Warm Stone / Earth):** An organic warm sand accent for secondary tags, deadlines, and subtle categorical groupings.
- **Neutral (`#F7F6F2` - Bone White / Alabaster):** The atmospheric canvas base that provides physical warmth without turning yellow or sterile blue.

### Functional Tonal Hierarchy
- **Canvas Base:** `#F7F6F2` (Warm Bone)
- **Sub-surface / Column Wells:** `#EFECE6` (Pale Limestone)
- **Card & Active Blocks:** `#FFFFFF` (Pure Uncoated Paper)
- **Micro-borders / Grid Lines:** `#E4E0D7` (Linen Weft)
- **Muted Text / Time Guides:** `#8A8780` (Dry Clay)

## Typography

The type system relies on **Plus Jakarta Sans** across all roles to achieve a sculptural, contemporary humanist balance. Its wide apertures, clean geometric geometry, and gentle modern curves echo mid-century Scandinavian furniture design.

### Hierarchy & Treatment
- **Headlines:** Set in light and regular weights (`400`–`500`) with tight tracking (`-0.03em`) to mimic architectural inscriptions and gallery title walls.
- **Body:** Open, serene, and legible with standard tracking, delivering fatigue-free reading across dense weekly schedules.
- **Labels & Temporal Markers:** Slightly elevated tracking (`+0.04em` to `+0.06em`) in semi-bold weights (`600`) to guarantee high-scan precision for hours, dates, and status tags without shouting.

## Layout & Spacing

The layout treats the desktop viewport as a full-screen architectural plan, employing a responsive 7-column day layout bookended by an ultra-slim, ambient time-ruler rail and optional collapsible context sidebar.

### Layout Mechanics
- **Full-Screen Canvas:** The viewport runs 100vh with a structural framing margin (`margin: 2rem`).
- **Calendar Grid:** The 7-column weekly matrix uses dedicated calendar gutters (`gutter-calendar: 0.5rem`), creating hairline vertical divisions between daily timelines.
- **Temporal Rhythm:** Vertical row increments strictly follow a 60-minute cadence mapped to `space-xl` (40px base line-height) or `48px` intervals with micro-subdivisions at 15-minute intervals.
- **Safe Padding:** Calendar events never touch their container borders; interior padding enforces `space-xs` (vertical) and `space-sm` (horizontal) margins to preserve breathing space.

## Elevation & Depth

This system intentionally rejects synthetic drop shadows and diffuse multi-colored blurs, choosing instead **Tactile Micro-Borders** combined with **Tonal Surface Layering**.

### Architectural Planarity
- **Base Canvas (Level 0):** `#F7F6F2` (Matte, non-reflective warm stone floor).
- **Day Columns / Track Wells (Level 1):** `#EFECE6` recessed background, partitioned by a 1px solid stroke in `#E4E0D7`.
- **Event Containers (Level 2):** Pure `#FFFFFF` fill resting on the track, bound by a crisp 1px tactile border in `#DDD8CE`.
- **Active & Drag States (Level 3):** When moving an event or opening an inspector, the element utilizes a whisper shadow: `0 4px 16px rgba(53, 57, 53, 0.04), 0 1px 2px rgba(53, 57, 53, 0.03)` with a slightly darkened border `#C8C3B8`.
- **Now Indicator:** A clean 1px hairline horizontal beam in `#768A78` with an anchored 5px solid circular pivot pin.

## Shapes

The design system maintains a **Soft (`1`)** roundedness profile to project discipline, structural calm, and architectural restraint. 

- **Base Radius (`0.25rem` / 4px):** Applied to individual event blocks, badges, input fields, and hover states.
- **Card & Modal Radius (`0.5rem` / 8px):** Applied to floating detail panels, modal dialogs, and popovers.
- **Pills / Radii Override:** Reserved strictly for categorical status indicators and the current date badge indicator to offer a soft, natural focal contrast against orthogonal calendar lines.

## Components

### Buttons
- **Primary:** Solid `#353935` background, `#F7F6F2` text, 0.25rem radius. Subtle hover state transition to `#242624` with zero shift.
- **Secondary / Ghost:** Transparent background, 1px `#DDD8CE` border, `#353935` text. On hover, fills with `#EFECE6`.
- **Text Action:** Unbordered, `#8A8780` text, transitioning to `#353935` with an understated bottom hairline underline.

### Calendar Event Blocks
- **Standard Entry:** Pure white fill, 1px micro-border in `#E4E0D7`, padded with `space-sm` around `body-sm` text. Left accent indicator is an integrated 2.5px vertical bar colored in muted sage (`#768A78`) or warm stone (`#A99985`).
- **Tentative / Focus Time:** Muted limestone fill (`#EFECE6`) with a dashed 1px border `#DDD8CE` and slate typography.
- **Active / Selected:** Crisp charcoal hairline border (`#353935`) and an imperceptible warm glow.

### Chips & Badges
- Compact height (20px), `label-sm` uppercase type, 100px pill radius, tinted in 10% opacity soft sage or warm stone with 1px tonal boundary outlines.

### Inputs & Date Selectors
- Flat bone white surface, 1px `#DDD8CE` perimeter outline, 4px corner radius. On focus: `#353935` border stroke without heavy rings or chromatic halos.

### Checkboxes & Radios
- Small, custom square and circular geometries (14px × 14px) bordered in 1px `#8A8780`. Filled with `#768A78` and a chalk-white minimalist checkmark when selected.

### Inspector / Detail Drawer
- Side-docked architectural panel anchored to the right viewport. Flat `#FFFFFF` surface with a continuous 1px left boundary in `#E4E0D7`, using spacious `space-lg` vertical stack groupings for event metadata, notes, and participants.