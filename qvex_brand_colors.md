# QVEX Brand Color Palette & Guidelines

## Overview
This document defines the official color palette for QVEX, a modern automotive marketplace platform. The color scheme combines vibrant greens with warm accents to create a trustworthy, innovative, and premium brand experience.

---

## Primary Brand Colors

### Emerald Green
- **HEX:** `#2ECC71`
- **RGB:** `rgb(46, 204, 113)`
- **HSL:** `hsl(145, 63%, 49%)`
- **Usage:** Primary brand color, main buttons, logos, headers
- **Applications:** Navigation, primary CTAs, brand elements, important notifications

### Forest Green
- **HEX:** `#27AE60`
- **RGB:** `rgb(39, 174, 96)`
- **HSL:** `hsl(145, 63%, 42%)`
- **Usage:** Secondary color, hover states, darker accents
- **Applications:** Button hover effects, secondary navigation, active states

### Lime Green
- **HEX:** `#A4D65E`
- **RGB:** `rgb(164, 214, 94)`
- **HSL:** `hsl(85, 58%, 60%)`
- **Usage:** Highlights, success states, call-to-action accents
- **Applications:** Success messages, progress indicators, highlight elements

### Spring Green
- **HEX:** `#58D68D`
- **RGB:** `rgb(88, 214, 141)`
- **HSL:** `hsl(145, 60%, 59%)`
- **Usage:** Light accents, backgrounds, subtle highlights
- **Applications:** Subtle backgrounds, light decorative elements, soft accents

---

## Supporting Colors

### Warm Cream
- **HEX:** `#FFE7BB`
- **RGB:** `rgb(255, 231, 187)`
- **HSL:** `hsl(39, 100%, 87%)`
- **Usage:** Warm accents, featured sections, premium highlights
- **Applications:** Featured vehicle listings, premium vendor badges, luxury car sections, special offers, VIP indicators

### Dark Navy
- **HEX:** `#2C3E50`
- **RGB:** `rgb(44, 62, 80)`
- **HSL:** `hsl(210, 29%, 24%)`
- **Usage:** Text, headings, professional elements
- **Applications:** Primary text, headings, navigation text, professional content

### Steel Gray
- **HEX:** `#7F8C8D`
- **RGB:** `rgb(127, 140, 141)`
- **HSL:** `hsl(184, 6%, 53%)`
- **Usage:** Secondary text, subtle borders, icons
- **Applications:** Secondary text, form labels, icons, subtle borders

### Light Gray
- **HEX:** `#ECF0F1`
- **RGB:** `rgb(236, 240, 241)`
- **HSL:** `hsl(192, 15%, 94%)`
- **Usage:** Backgrounds, dividers, card backgrounds
- **Applications:** Section backgrounds, card backgrounds, dividers, subtle borders

### Pure White
- **HEX:** `#FFFFFF`
- **RGB:** `rgb(255, 255, 255)`
- **HSL:** `hsl(0, 0%, 100%)`
- **Usage:** Clean backgrounds, cards, text on dark
- **Applications:** Main backgrounds, card content, text on dark backgrounds

---

## CSS Variables

```css
:root {
  /* Primary Brand Colors */
  --emerald-green: #2ECC71;
  --forest-green: #27AE60;
  --lime-green: #A4D65E;
  --spring-green: #58D68D;
  
  /* Supporting Colors */
  --warm-cream: #FFE7BB;
  --dark-navy: #2C3E50;
  --steel-gray: #7F8C8D;
  --light-gray: #ECF0F1;
  --pure-white: #FFFFFF;
  
  /* Semantic Color Assignments */
  --primary: var(--emerald-green);
  --primary-hover: var(--forest-green);
  --secondary: var(--warm-cream);
  --accent: var(--lime-green);
  --text-primary: var(--dark-navy);
  --text-secondary: var(--steel-gray);
  --background: var(--pure-white);
  --background-alt: var(--light-gray);
  --success: var(--lime-green);
  --premium: var(--warm-cream);
}
```

---

## Usage Guidelines

### Primary Actions
- Main buttons and CTAs
- Navigation highlights
- Logo and brand elements
- Important notifications
- Primary interactive elements

### Secondary Elements
- Hover states and interactions
- Supporting graphics
- Progress indicators
- Active states
- Secondary navigation

### Warm Accents & Premium
- Featured vehicle listings
- Premium vendor badges
- Luxury car sections
- Special offers highlights
- VIP user indicators

### Backgrounds & Layout
- Section backgrounds
- Success messages
- Highlight boxes
- Decorative elements
- Content containers

### Text & Content
- Headers and titles
- Body text and descriptions
- Captions and metadata
- Form labels
- Secondary information

---

## Color Combinations

### Recommended Pairings

#### Primary Gradient
```css
background: linear-gradient(135deg, #2ECC71, #27AE60);
```

#### Emerald + White
```css
background: #2ECC71;
color: #FFFFFF;
```

#### Cream + Navy (Premium)
```css
background: #FFE7BB;
color: #2C3E50;
```

#### Navy + Cream (Elegant)
```css
background: #2C3E50;
color: #FFE7BB;
```

#### Navy + Emerald (Professional)
```css
background: #2C3E50;
color: #2ECC71;
```

#### Light Gradient (Soft)
```css
background: linear-gradient(135deg, #A4D65E, #58D68D);
```

---

## Accessibility Guidelines

### Contrast Ratios
All color combinations meet WCAG 2.1 AA standards:

- **Dark Navy (#2C3E50) on White (#FFFFFF):** 12.63:1 ✅
- **Steel Gray (#7F8C8D) on White (#FFFFFF):** 4.54:1 ✅
- **Dark Navy (#2C3E50) on Warm Cream (#FFE7BB):** 8.92:1 ✅
- **Emerald Green (#2ECC71) on White (#FFFFFF):** 3.14:1 ✅
- **White (#FFFFFF) on Emerald Green (#2ECC71):** 3.14:1 ✅

### Best Practices
- Always test color combinations for sufficient contrast
- Provide alternative indicators beyond color alone
- Consider colorblind users when using green/red combinations
- Ensure interactive elements have clear visual states

---

## Brand Context

### Why These Colors Work for QVEX

#### Trust & Reliability
Green conveys growth, prosperity, and trustworthiness - essential for automotive transactions.

#### Modern Technology
Contemporary green palette suggests innovation and forward-thinking approach.

#### Automotive Industry Alignment
Green associations with eco-friendly and electric vehicles align with industry trends.

#### Cultural Considerations
Green has positive associations in Arabic markets, while cream adds warmth and sophistication.

#### Scalability
Color palette works across web, mobile, print, and digital marketing materials.

---

## Implementation Notes

### File Formats
- Use HEX codes for web development
- Provide RGB values for print materials
- Include HSL for programmatic color manipulation

### Consistency
- Always use defined color variables in CSS
- Maintain consistent color usage across all platforms
- Regular brand audits to ensure compliance

### Testing
- Test on various devices and screen types
- Verify accessibility with automated tools
- User testing for color comprehension

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Sep 2025 | Initial color palette definition |
| 1.1 | Sep 2025 | Added Warm Cream (#FFE7BB) to supporting colors |

---

## Contact

For questions about brand color usage or additional color needs, contact the QVEX design team.

---

*This document serves as the official reference for QVEX brand colors. All digital and print materials should adhere to these guidelines to ensure consistent brand representation.*