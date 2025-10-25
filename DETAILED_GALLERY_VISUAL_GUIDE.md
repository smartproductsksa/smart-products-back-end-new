# Detailed Gallery - Visual Guide

## What You'll See in the Admin Panel

### Selecting the Block Type

When you click "Add" in the page builder, you'll now see:

```
┌────────────────────────────────────────────┐
│  Select Block Type:                        │
│  ○ Hero                                    │
│  ○ Text Section                            │
│  ○ Image Gallery (Simple)                  │ ← For basic photo galleries
│  ○ Gallery with Details (Clients, Team..)  │ ← NEW! For structured content
│  ○ Text with Image                         │
│  ○ Model List                              │
└────────────────────────────────────────────┘
```

### The Detailed Gallery Form

Once selected, you'll see:

```
┌─────────────────────────────────────────────────────────────────┐
│ Gallery with Details (Clients, Team, etc.)                [×]   │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Section Title (optional)                                       │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Our Valued Clients                                       │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  Gallery Items                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ⋮⋮ Client A                                 [▼] [-] [≡] │ ← Drag, Collapse, Clone, Delete
│  │                                                          │   │
│  │  Item Title *                                            │   │
│  │  ┌────────────────────┐                                 │   │
│  │  │ Client A           │                                 │   │
│  │  └────────────────────┘                                 │   │
│  │                                                          │   │
│  │  Image *                                                │   │
│  │  ┌────────────────────────────────────────────────┐    │   │
│  │  │                                                 │    │   │
│  │  │         [📷 client-a-logo.jpg]                 │    │   │
│  │  │            200 x 150 px                         │    │   │
│  │  │                                                 │    │   │
│  │  │  [Edit Image] [Remove]                          │    │   │
│  │  └────────────────────────────────────────────────┘    │   │
│  │                                                          │   │
│  │  Description (optional)                                 │   │
│  │  ┌────────────────────────────────────────────────┐    │   │
│  │  │ Leading technology company since 2010           │    │   │
│  │  │                                                 │    │   │
│  │  │                                                 │    │   │
│  │  └────────────────────────────────────────────────┘    │   │
│  │                                                          │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ⋮⋮ Client B                                 [▼] [-] [≡] │   │
│  │  ... (same structure)                                    │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  [+ Add Item]  ← Add more clients/team members/etc.            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Collapsed View

When collapsed, items show their title for easy navigation:

```
┌─────────────────────────────────────────────────────────────────┐
│ Gallery with Details (Clients, Team, etc.)                [×]   │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Section Title: Our Valued Clients                              │
│                                                                  │
│  Gallery Items                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ⋮⋮ Client A                                 [▶] [-] [≡] │   │
│  └─────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ⋮⋮ Client B                                 [▶] [-] [≡] │   │
│  └─────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ⋮⋮ Client C                                 [▶] [-] [≡] │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  [+ Add Item]                                                   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Real-World Examples

### Example 1: Clients Page

```
Section Title: "Companies That Trust Us"

Item 1:
  Title: TechCorp Inc.
  Image: [tech-corp-logo.png]
  Description: Global technology leader partnering with us since 2018

Item 2:
  Title: Retail Giant LLC
  Image: [retail-logo.png]
  Description: Leading e-commerce platform with 10M+ customers

Item 3:
  Title: FinanceX
  Image: [financex-logo.png]
  Description: (empty - optional)
```

### Example 2: Team Page

```
Section Title: "Meet Our Leadership Team"

Item 1:
  Title: John Doe
  Image: [john-photo.jpg]
  Description: CEO & Founder - 20+ years in tech industry

Item 2:
  Title: Jane Smith
  Image: [jane-photo.jpg]
  Description: CTO - Former Google engineer, Stanford PhD

Item 3:
  Title: Mike Johnson
  Image: [mike-photo.jpg]
  Description: CFO - Harvard MBA, ex-McKinsey consultant
```

### Example 3: Certifications

```
Section Title: "Our Industry Certifications"

Item 1:
  Title: ISO 9001:2015
  Image: [iso-badge.png]
  Description: Quality management system certification

Item 2:
  Title: SOC 2 Type II
  Image: [soc2-badge.png]
  Description: Security and compliance certification

Item 3:
  Title: GDPR Compliant
  Image: [gdpr-badge.png]
  Description: European data protection standards
```

## Image Editor Features

When uploading an image, you can:

```
┌──────────────────────────────────────────┐
│  Image Editor                        [×] │
├──────────────────────────────────────────┤
│                                          │
│  ┌────────────────────────────────────┐ │
│  │                                    │ │
│  │      [  Your Uploaded Image  ]    │ │
│  │                                    │ │
│  │      (Drag to crop/reposition)    │ │
│  │                                    │ │
│  └────────────────────────────────────┘ │
│                                          │
│  Aspect Ratio:                           │
│  ○ 16:9   ○ 4:3   ○ 1:1                 │
│                                          │
│  [Cancel]              [Apply Changes]   │
│                                          │
└──────────────────────────────────────────┘
```

## Item Management Icons

```
⋮⋮ = Drag handle (click and drag to reorder)
[▼] = Collapse/expand item
[-] = Delete item
[≡] = Clone item (duplicate with same data)
```

## Tips for Best Experience

### 1. Use Descriptive Titles
✅ Good: "TechCorp Inc.", "John Doe - CEO"
❌ Bad: "Client 1", "Person"

### 2. Consistent Image Sizes
- Use the image editor to crop to same aspect ratio
- Recommended: 1:1 for profile photos, 16:9 for logos

### 3. Keep Descriptions Concise
- Aim for 1-2 sentences
- Use description field for additional context only

### 4. Order Matters
- Drag items to reorder
- Put most important items first

### 5. Use Collapsible View
- Collapse items when managing many entries
- Makes it easier to reorder and navigate

## Comparison: Simple vs Detailed Gallery

### Use Simple Gallery For:
```
┌──────────────────────────────────────────┐
│  Event Photos Gallery                    │
│                                          │
│  [Photo1] [Photo2] [Photo3]              │
│  [Photo4] [Photo5] [Photo6]              │
│                                          │
│  Just multiple images, no individual     │
│  titles or descriptions needed           │
└──────────────────────────────────────────┘
```

### Use Detailed Gallery For:
```
┌──────────────────────────────────────────┐
│  Our Clients                             │
│                                          │
│  ┌─────────────┐  Each item has:        │
│  │  [Logo]     │  • Name                │
│  │  Client A   │  • Image               │
│  │  Description│  • Details             │
│  └─────────────┘                         │
│                                          │
│  Structured content where each item      │
│  needs individual context                │
└──────────────────────────────────────────┘
```

## Mobile View in Admin

The form is responsive and works well on tablets:

```
┌────────────────────────┐
│ Gallery with Details   │
├────────────────────────┤
│                        │
│ Section Title          │
│ ┌────────────────────┐ │
│ │ Our Clients        │ │
│ └────────────────────┘ │
│                        │
│ ⋮⋮ Client A      [▼][-]│
│ ┌────────────────────┐ │
│ │ Title *            │ │
│ │ ┌────────────────┐ │ │
│ │ │ Client A       │ │ │
│ │ └────────────────┘ │ │
│ │                    │ │
│ │ Image *            │ │
│ │ [Upload]           │ │
│ │                    │ │
│ │ Description        │ │
│ │ ┌────────────────┐ │ │
│ │ │ ...            │ │ │
│ │ └────────────────┘ │ │
│ └────────────────────┘ │
│                        │
│ [+ Add Item]           │
│                        │
└────────────────────────┘
```

## Quick Start Checklist

1. ✅ Go to Pages → Create/Edit a page
2. ✅ Click "Add" to add content section
3. ✅ Select "Gallery with Details (Clients, Team, etc.)"
4. ✅ Enter section title (optional)
5. ✅ Click "Add Item"
6. ✅ Enter title for first item
7. ✅ Upload image
8. ✅ Add description (optional)
9. ✅ Repeat for more items
10. ✅ Save page
11. ✅ Test via API: `/api/v1/pages/{your-slug}`

## Need Help?

- Full documentation: `DETAILED_GALLERY_GUIDE.md`
- Implementation details: `DETAILED_GALLERY_CHANGELOG.md`
- General setup: `README.md`

---

**Happy building! 🎨**

