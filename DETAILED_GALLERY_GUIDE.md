# Detailed Gallery Feature Guide

## Overview

The Detailed Gallery feature allows you to create image galleries where each image has its own title, image, and optional description. This is perfect for displaying:

- **Clients/Partners** - Logo + Company name + Description
- **Team Members** - Photo + Name + Role/Bio
- **Testimonials** - Photo + Client name + Testimonial text
- **Portfolio Items** - Image + Project name + Details
- **Certifications** - Badge + Certificate name + Details
- **Awards** - Trophy + Award name + Achievement details

## Difference from Simple Gallery

The page builder now has **TWO gallery options**:

### 1. Image Gallery (Simple)
- **Use for:** Basic photo galleries, image showcases
- **Contains:** Multiple images with one overall title
- **Best for:** Event photos, product images, general galleries

### 2. Gallery with Details (Clients, Team, etc.)
- **Use for:** Structured content where each item needs context
- **Contains:** Multiple items, each with:
  - ✅ **Title** (Required) - e.g., "John Doe", "Company XYZ"
  - ✅ **Image** (Required) - Photo, logo, or visual
  - ✅ **Description** (Optional) - Additional context or details
- **Best for:** Clients, team members, partners, testimonials

## How to Use in Admin Panel

### Creating a Detailed Gallery

1. **Go to Pages** → Create or Edit a page
2. **Add Content Section** → Click "Add"
3. **Select** "Gallery with Details (Clients, Team, etc.)"
4. **Enter Section Title** (Optional) - e.g., "Our Valued Clients"
5. **Add Items:**
   - Click "Add Item"
   - Enter **Title** (e.g., "ABC Corporation")
   - Upload **Image** (supports image editor with aspect ratios)
   - Enter **Description** (optional) (e.g., "Leading tech company since 2010")
   - Repeat for more items

### Features

- ✅ **Reorderable** - Drag and drop items to reorder
- ✅ **Collapsible** - Each item can be collapsed for cleaner UI
- ✅ **Cloneable** - Duplicate items to save time
- ✅ **Image Editor** - Crop images to 16:9, 4:3, or 1:1 aspect ratios
- ✅ **Item Labels** - Each item shows its title in the admin panel
- ✅ **Minimum 1 Item** - At least one item required per gallery

## Data Structure

### Stored in Database

```json
{
  "type": "detailed_gallery",
  "data": {
    "section_title": "Our Valued Clients",
    "items": [
      {
        "title": "Client A",
        "image": "pages/detailed-gallery/client-a-xyz123.jpg",
        "description": "Leading technology company since 2010"
      },
      {
        "title": "Client B",
        "image": "pages/detailed-gallery/client-b-abc456.jpg",
        "description": null
      }
    ]
  }
}
```

### API Response

When you fetch a page via `/api/v1/pages/{slug}`, the detailed gallery will be included in the content array:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Our Clients",
    "slug": "our-clients",
    "content": [
      {
        "type": "detailed_gallery",
        "data": {
          "section_title": "Our Valued Clients",
          "items": [
            {
              "title": "Client A",
              "image": "pages/detailed-gallery/client-a-xyz123.jpg",
              "description": "Leading technology company since 2010"
            }
          ]
        }
      }
    ]
  }
}
```

## Frontend Implementation Guide

### Rendering the Gallery

Here's how to render the detailed gallery in your frontend:

#### React Example

```jsx
function DetailedGallery({ section }) {
  const { section_title, items } = section.data;
  
  return (
    <div className="detailed-gallery">
      {section_title && (
        <h2 className="gallery-title">{section_title}</h2>
      )}
      
      <div className="gallery-grid">
        {items.map((item, index) => (
          <div key={index} className="gallery-item">
            <img 
              src={`${API_URL}/storage/${item.image}`}
              alt={item.title}
              className="gallery-image"
            />
            <h3 className="item-title">{item.title}</h3>
            {item.description && (
              <p className="item-description">{item.description}</p>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
```

#### Vue Example

```vue
<template>
  <div class="detailed-gallery">
    <h2 v-if="section.data.section_title" class="gallery-title">
      {{ section.data.section_title }}
    </h2>
    
    <div class="gallery-grid">
      <div 
        v-for="(item, index) in section.data.items" 
        :key="index"
        class="gallery-item"
      >
        <img 
          :src="`${apiUrl}/storage/${item.image}`"
          :alt="item.title"
          class="gallery-image"
        />
        <h3 class="item-title">{{ item.title }}</h3>
        <p v-if="item.description" class="item-description">
          {{ item.description }}
        </p>
      </div>
    </div>
  </div>
</template>
```

#### Vanilla JavaScript

```javascript
function renderDetailedGallery(section) {
  const { section_title, items } = section.data;
  
  let html = '<div class="detailed-gallery">';
  
  if (section_title) {
    html += `<h2 class="gallery-title">${section_title}</h2>`;
  }
  
  html += '<div class="gallery-grid">';
  
  items.forEach(item => {
    html += `
      <div class="gallery-item">
        <img 
          src="${API_URL}/storage/${item.image}"
          alt="${item.title}"
          class="gallery-image"
        />
        <h3 class="item-title">${item.title}</h3>
        ${item.description ? `<p class="item-description">${item.description}</p>` : ''}
      </div>
    `;
  });
  
  html += '</div></div>';
  return html;
}
```

### Example CSS Styling

```css
.detailed-gallery {
  padding: 4rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.gallery-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 3rem;
  color: #1a202c;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.gallery-item {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.gallery-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.item-title {
  padding: 1.5rem 1.5rem 0.5rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #2d3748;
}

.item-description {
  padding: 0 1.5rem 1.5rem;
  color: #718096;
  line-height: 1.6;
}
```

## Use Case Examples

### 1. Clients Page

```
Section Title: "Companies That Trust Us"
Items:
  - Title: "TechCorp Inc."
    Image: tech-corp-logo.jpg
    Description: "Global technology leader partnering with us since 2018"
    
  - Title: "Retail Giant LLC"
    Image: retail-logo.jpg
    Description: "Leading e-commerce platform with 10M+ customers"
```

### 2. Team Page

```
Section Title: "Meet Our Leadership Team"
Items:
  - Title: "John Doe"
    Image: john-photo.jpg
    Description: "CEO & Founder - 20+ years in tech industry"
    
  - Title: "Jane Smith"
    Image: jane-photo.jpg
    Description: "CTO - Former Google engineer, Stanford PhD"
```

### 3. Partners Page

```
Section Title: "Strategic Partners"
Items:
  - Title: "Microsoft"
    Image: microsoft-logo.jpg
    Description: "Cloud infrastructure partner"
    
  - Title: "AWS"
    Image: aws-logo.jpg
    Description: null (no description needed)
```

### 4. Certifications Page

```
Section Title: "Our Certifications"
Items:
  - Title: "ISO 9001:2015"
    Image: iso-badge.jpg
    Description: "Quality management system certification"
    
  - Title: "SOC 2 Type II"
    Image: soc2-badge.jpg
    Description: "Security and compliance certification"
```

## File Storage

### Location
Images are stored in: `storage/app/public/pages/detailed-gallery/`

### Access URL
```
{APP_URL}/storage/pages/detailed-gallery/{filename}
```

### Example
```
http://localhost:8000/storage/pages/detailed-gallery/client-logo-abc123.jpg
```

## API Integration

### Fetching Pages with Detailed Galleries

```javascript
// Fetch a specific page
fetch('http://localhost:8000/api/v1/pages/our-clients')
  .then(res => res.json())
  .then(data => {
    const page = data.data;
    
    // Find detailed galleries
    const detailedGalleries = page.content.filter(
      section => section.type === 'detailed_gallery'
    );
    
    // Render each gallery
    detailedGalleries.forEach(gallery => {
      renderDetailedGallery(gallery);
    });
  });
```

### Filtering by Section Type

```javascript
function getContentByType(page, type) {
  return page.content.filter(section => section.type === type);
}

// Get all detailed galleries from a page
const galleries = getContentByType(pageData, 'detailed_gallery');

// Get all simple galleries
const simpleGalleries = getContentByType(pageData, 'image_gallery');
```

## Best Practices

### 1. Image Guidelines
- **Recommended size:** 800x600px minimum
- **Format:** JPEG, PNG, or WebP
- **Max file size:** 2MB (enforced by system)
- **Aspect ratio:** Use the image editor to standardize

### 2. Content Guidelines
- **Title:** Keep under 50 characters for best display
- **Description:** 100-200 characters recommended
- **Order:** Put most important items first

### 3. Performance
- Optimize images before upload
- Consider lazy loading for galleries with many items
- Use WebP format for smaller file sizes

### 4. Accessibility
- Always include meaningful alt text (titles are used)
- Ensure sufficient color contrast
- Make items keyboard navigable in your frontend

## Troubleshooting

### Images Not Displaying

**Run the fix command:**
```bash
php artisan storage:fix-permissions
```

**Check storage link:**
```bash
php artisan storage:link
```

### API Returns Empty Items

Check that:
- Page status is "published"
- Items have both title and image
- Images were uploaded successfully

### Items Not Reordering

In admin panel:
- Drag the handle (six dots) on the left of each item
- Save the page after reordering

## Testing

Run the test suite:
```bash
php artisan test tests/Feature/DetailedGalleryTest.php
```

Tests cover:
- ✅ Creating pages with detailed galleries
- ✅ API response structure
- ✅ Optional descriptions
- ✅ Multiple gallery types on same page
- ✅ Item reordering

## Summary

The Detailed Gallery feature provides a flexible way to showcase structured content with images, titles, and descriptions. It's perfect for clients, team members, partners, and any content where each item needs individual context.

**Key Benefits:**
- 🎯 Purpose-built for structured content
- 🎨 Flexible and customizable
- 📱 Responsive-ready
- 🔒 Secure file handling
- ✅ Fully tested
- 📚 Well-documented

For questions or issues, refer to the main README.md or project documentation.

