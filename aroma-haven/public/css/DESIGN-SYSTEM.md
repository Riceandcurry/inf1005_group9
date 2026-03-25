# Aroma Haven Mini Design System

This file freezes the current frontend baseline for consistency.

## 1) Token-first rule

When updating visuals, change tokens in `tokens.css :root` first, then component rules in `components.css`.
Avoid hardcoded colors, radius, spacing, and shadows in component blocks.

## File structure

- `tokens.css`: design tokens only.
- `base.css`: global typography, utility classes, and Bootstrap overrides.
- `components.css`: reusable components (navbar, footer, hero, bean-card).
- `pages.css`: page-specific styles only.
- `styles.css`: legacy compatibility entrypoint that imports the four files above.

## 2) Core tokens (source of truth)

- Colors: `--ah-oat`, `--ah-steamed`, `--ah-terracotta`, `--ah-espresso`, `--ah-cortado`, `--ah-sage`
- Type scale: `--ah-text-xs`, `--ah-text-sm`, `--ah-text-md`, `--ah-text-lg`
- Spacing scale: `--ah-space-1` to `--ah-space-12`
- Radius scale: `--ah-radius-xs`, `--ah-radius-sm`, `--ah-radius-md`, `--ah-radius-lg`, `--ah-radius-pill`
- Border/shadow: `--ah-border-soft`, `--ah-border-mid`, `--ah-shadow-sm`, `--ah-shadow-md`, `--ah-shadow-lg`
- Component sizes: `--ah-brand-width`, `--ah-brand-min-height`, `--ah-touch-target`
- Letter spacing: `--ah-letter-wide`, `--ah-letter-wider`

## 3) Component rules

### Navbar

- `.ah-navbar` uses tokenized border/background only.
- `.ah-brand` width/height must come from `--ah-brand-width` and `--ah-brand-min-height`.
- `.ah-nav-link` must use tokenized type and letter spacing.

### Buttons

- `.btn-primary` and `.btn-outline-primary` share:
  - `min-height: var(--ah-touch-target)`
  - pill radius via `--ah-radius-pill`
  - tokenized text sizing and spacing
- Hover shadows must use tokenized brand shadows.

### Bean cards

- `.ah-bean-card` uses tokenized border/radius/shadow.
- Image container uses `--ah-radius-sm`.
- Tags use pill radius and tokenized border.
- Add button must stay full-width with `min-height: var(--ah-touch-target)`.

## 4) Responsive rules

- Keep current grid behavior:
  - mobile: 1 column
  - tablet: 2 columns
  - desktop (`lg+`): 3 columns
- On `<=575.98px`, hero CTAs stay centered and constrained width.

## 5) Accessibility baseline

- Keep visible `:focus-visible` states for nav links, icon links, toggler, card links, and primary action buttons.
- Icon-only controls must include `aria-label`.
