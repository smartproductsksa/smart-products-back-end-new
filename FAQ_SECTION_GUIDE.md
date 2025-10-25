# FAQ Section Guide

## Overview

The FAQ (Frequently Asked Questions) section allows you to create structured Q&A content for your pages. Each FAQ section can contain multiple questions with rich-text answers.

## Features

- ✅ **Section Title** - Customizable main heading (default: "Frequently Asked Questions")
- ✅ **Section Description** - Optional introduction text
- ✅ **Multiple Q&A Items** - Unlimited question-answer pairs
- ✅ **Rich Text Answers** - Support for formatting, lists, and links
- ✅ **Reorderable** - Drag and drop to reorder questions
- ✅ **Collapsible** - Each item can be collapsed for easier management
- ✅ **Cloneable** - Duplicate similar questions quickly

## Use Cases

Perfect for:
- 📝 **Product FAQs** - Common questions about your products/services
- 💼 **Customer Support** - Help center content
- 🎓 **Onboarding** - New user questions
- 🔧 **Technical Documentation** - How-to questions
- 📞 **Contact/Shipping Info** - Policies and procedures
- 🏢 **About Us** - Company-related questions

## How to Use in Admin Panel

### Creating an FAQ Section

1. **Navigate** to Pages → Create or Edit a page
2. **Add Content** → Click "Add" button
3. **Select** "FAQ Section" from the block types
4. **Configure:**
   - **Section Title**: Enter main heading (e.g., "Frequently Asked Questions")
   - **Section Description**: Add optional introduction (e.g., "Find answers to common questions")
5. **Add FAQ Items:**
   - Click "Add FAQ Item"
   - Enter **Question** (required, max 500 characters)
   - Enter **Answer** (required, rich text editor)
   - Repeat for more items
6. **Save** the page

### Managing FAQ Items

#### Reordering Questions
- Drag the handle (⋮⋮) on the left of each item
- Drop in desired position

#### Collapsing/Expanding
- Click the collapse arrow (▼/▶) to show/hide item details
- When collapsed, you'll see just the question text

#### Cloning Items
- Click the clone icon (≡) to duplicate an item
- Useful for similar questions

#### Deleting Items
- Click the delete icon (−) to remove an item

## Data Structure

### Stored in Database

```json
{
  "type": "faq",
  "data": {
    "section_title": "Frequently Asked Questions",
    "section_description": "Find answers to common questions about our services",
    "items": [
      {
        "question": "What are your business hours?",
        "answer": "<p>We are open Monday to Friday, 9 AM to 5 PM.</p>"
      },
      {
        "question": "How can I contact support?",
        "answer": "<p>You can reach us via:</p><ul><li>Email: support@example.com</li><li>Phone: 123-456-7890</li></ul>"
      }
    ]
  }
}
```

### API Response

When fetching a page via `/api/v1/pages/{slug}`:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "FAQ Page",
    "slug": "faq",
    "content": [
      {
        "type": "faq",
        "data": {
          "section_title": "Frequently Asked Questions",
          "section_description": "Find answers to common questions",
          "items": [
            {
              "question": "What are your business hours?",
              "answer": "<p>We are open Monday to Friday, 9 AM to 5 PM.</p>"
            }
          ]
        }
      }
    ]
  }
}
```

## Frontend Implementation

### React Example

```jsx
function FaqSection({ section }) {
  const { section_title, section_description, items } = section.data;
  const [openIndex, setOpenIndex] = useState(null);
  
  return (
    <div className="faq-section">
      <h2 className="faq-title">{section_title}</h2>
      
      {section_description && (
        <p className="faq-description">{section_description}</p>
      )}
      
      <div className="faq-items">
        {items.map((item, index) => (
          <div 
            key={index} 
            className={`faq-item ${openIndex === index ? 'open' : ''}`}
          >
            <button 
              className="faq-question"
              onClick={() => setOpenIndex(openIndex === index ? null : index)}
            >
              <span>{item.question}</span>
              <span className="faq-icon">
                {openIndex === index ? '−' : '+'}
              </span>
            </button>
            
            {openIndex === index && (
              <div 
                className="faq-answer"
                dangerouslySetInnerHTML={{ __html: item.answer }}
              />
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
```

### Vue Example

```vue
<template>
  <div class="faq-section">
    <h2 class="faq-title">{{ section.data.section_title }}</h2>
    
    <p v-if="section.data.section_description" class="faq-description">
      {{ section.data.section_description }}
    </p>
    
    <div class="faq-items">
      <div 
        v-for="(item, index) in section.data.items" 
        :key="index"
        :class="['faq-item', { open: openIndex === index }]"
      >
        <button 
          class="faq-question"
          @click="toggleItem(index)"
        >
          <span>{{ item.question }}</span>
          <span class="faq-icon">{{ openIndex === index ? '−' : '+' }}</span>
        </button>
        
        <div 
          v-if="openIndex === index"
          class="faq-answer"
          v-html="item.answer"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['section'],
  data() {
    return {
      openIndex: null
    };
  },
  methods: {
    toggleItem(index) {
      this.openIndex = this.openIndex === index ? null : index;
    }
  }
};
</script>
```

### Vanilla JavaScript

```javascript
function renderFaqSection(section) {
  const { section_title, section_description, items } = section.data;
  
  let html = '<div class="faq-section">';
  html += `<h2 class="faq-title">${section_title}</h2>`;
  
  if (section_description) {
    html += `<p class="faq-description">${section_description}</p>`;
  }
  
  html += '<div class="faq-items">';
  
  items.forEach((item, index) => {
    html += `
      <div class="faq-item" data-index="${index}">
        <button class="faq-question" onclick="toggleFaq(${index})">
          <span>${item.question}</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer" style="display: none;">
          ${item.answer}
        </div>
      </div>
    `;
  });
  
  html += '</div></div>';
  return html;
}

function toggleFaq(index) {
  const item = document.querySelector(`.faq-item[data-index="${index}"]`);
  const answer = item.querySelector('.faq-answer');
  const icon = item.querySelector('.faq-icon');
  
  if (answer.style.display === 'none') {
    answer.style.display = 'block';
    icon.textContent = '−';
    item.classList.add('open');
  } else {
    answer.style.display = 'none';
    icon.textContent = '+';
    item.classList.remove('open');
  }
}
```

## Example CSS Styling

```css
.faq-section {
  max-width: 800px;
  margin: 4rem auto;
  padding: 0 2rem;
}

.faq-title {
  font-size: 2.5rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 1rem;
  color: #1a202c;
}

.faq-description {
  text-align: center;
  font-size: 1.125rem;
  color: #718096;
  margin-bottom: 3rem;
}

.faq-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.faq-item.open {
  border-color: #4299e1;
  box-shadow: 0 4px 6px rgba(66, 153, 225, 0.1);
}

.faq-question {
  width: 100%;
  padding: 1.25rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  border: none;
  cursor: pointer;
  font-size: 1.125rem;
  font-weight: 600;
  text-align: left;
  color: #2d3748;
  transition: background-color 0.2s ease;
}

.faq-question:hover {
  background-color: #f7fafc;
}

.faq-icon {
  font-size: 1.5rem;
  color: #4299e1;
  font-weight: 300;
  transition: transform 0.3s ease;
}

.faq-item.open .faq-icon {
  transform: rotate(180deg);
}

.faq-answer {
  padding: 0 1.5rem 1.25rem;
  color: #4a5568;
  line-height: 1.75;
  animation: slideDown 0.3s ease;
}

.faq-answer p {
  margin-bottom: 1rem;
}

.faq-answer ul, .faq-answer ol {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
}

.faq-answer a {
  color: #4299e1;
  text-decoration: underline;
}

.faq-answer a:hover {
  color: #2b6cb0;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .faq-section {
    padding: 0 1rem;
  }
  
  .faq-title {
    font-size: 2rem;
  }
  
  .faq-question {
    padding: 1rem;
    font-size: 1rem;
  }
  
  .faq-answer {
    padding: 0 1rem 1rem;
    font-size: 0.9375rem;
  }
}
```

## Real-World Examples

### Example 1: Product FAQ

```
Section Title: "Product Questions"
Section Description: "Everything you need to know about our products"

Items:
1. Q: What materials are your products made from?
   A: Our products are made from sustainable, eco-friendly materials including 
      organic cotton, recycled polyester, and biodegradable packaging.

2. Q: Do you offer international shipping?
   A: Yes, we ship to over 50 countries worldwide. Shipping times vary by 
      destination (3-5 days domestic, 7-14 days international).

3. Q: What is your return policy?
   A: We accept returns within 30 days of purchase. Items must be unused and 
      in original packaging. See our full return policy for details.
```

### Example 2: Technical Support FAQ

```
Section Title: "Technical Support"
Section Description: "Get help with common technical issues"

Items:
1. Q: How do I reset my password?
   A: Click "Forgot Password" on the login page. Enter your email and follow 
      the instructions sent to your inbox. If you don't receive an email within 
      5 minutes, check your spam folder.

2. Q: The app won't load. What should I do?
   A: Try these steps:
      1. Clear your browser cache
      2. Update to the latest browser version
      3. Disable browser extensions
      4. Try a different browser
      If the issue persists, contact support@example.com

3. Q: How do I export my data?
   A: Go to Settings > Data & Privacy > Export Data. Select the data types you 
      want to export and click "Request Export". You'll receive a download link 
      via email within 24 hours.
```

### Example 3: Pricing & Billing FAQ

```
Section Title: "Pricing & Billing"

Items:
1. Q: What payment methods do you accept?
   A: We accept all major credit cards (Visa, Mastercard, Amex), PayPal, and 
      bank transfers for enterprise accounts.

2. Q: Can I change my plan later?
   A: Yes! You can upgrade or downgrade your plan at any time from your account 
      settings. Changes take effect immediately, and we'll prorate the cost.

3. Q: Do you offer refunds?
   A: We offer a 30-day money-back guarantee. If you're not satisfied, contact 
      us within 30 days for a full refund, no questions asked.
```

## Best Practices

### Content Writing

1. **Clear Questions**
   - Use natural language that users would search for
   - Start with question words (What, How, When, Where, Why)
   - Keep questions concise (under 100 characters when possible)

2. **Helpful Answers**
   - Be specific and actionable
   - Use bullet points or numbered lists for multi-step answers
   - Include links to related pages when helpful
   - Keep answers scannable (short paragraphs)

3. **Organization**
   - Group related questions into separate FAQ sections
   - Put most frequently asked questions first
   - Consider using multiple FAQ sections for different topics

### Technical

1. **Performance**
   - Limit to 20-30 questions per section
   - Use multiple sections for larger FAQs
   - Implement lazy loading for very long lists

2. **Accessibility**
   - Use semantic HTML (button for questions, div for answers)
   - Ensure keyboard navigation works
   - Add ARIA attributes for screen readers
   - Maintain good color contrast

3. **SEO**
   - Use FAQ schema markup (JSON-LD)
   - Each question is a potential search query
   - Include keywords naturally in questions and answers

## Advanced Features

### Schema Markup for SEO

Add structured data to help search engines:

```javascript
const faqSchema = {
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": section.data.items.map(item => ({
    "@type": "Question",
    "name": item.question,
    "acceptedAnswer": {
      "@type": "Answer",
      "text": item.answer.replace(/<[^>]*>/g, '') // Strip HTML
    }
  }))
};
```

### Search Functionality

```javascript
function searchFaq(query, items) {
  const lowerQuery = query.toLowerCase();
  return items.filter(item => 
    item.question.toLowerCase().includes(lowerQuery) ||
    item.answer.toLowerCase().includes(lowerQuery)
  );
}
```

### Table of Contents

```javascript
function FaqTableOfContents({ items }) {
  return (
    <nav className="faq-toc">
      <h3>Quick Navigation</h3>
      <ul>
        {items.map((item, index) => (
          <li key={index}>
            <a href={`#faq-${index}`}>
              {item.question}
            </a>
          </li>
        ))}
      </ul>
    </nav>
  );
}
```

## Testing

Run the test suite:

```bash
php artisan test tests/Feature/FaqSectionTest.php
```

Tests cover:
- ✅ Creating pages with FAQ sections
- ✅ API response structure
- ✅ Optional section description
- ✅ Rich text in answers
- ✅ Reordering items
- ✅ Multiple FAQ sections per page
- ✅ Combining with other content blocks
- ✅ Large number of items

## API Integration

### Fetching FAQ Pages

```javascript
// Fetch specific page
const response = await fetch('/api/v1/pages/faq');
const page = await response.json();

// Find FAQ sections
const faqSections = page.data.content.filter(
  section => section.type === 'faq'
);

// Render each section
faqSections.forEach(section => {
  renderFaqSection(section);
});
```

### Search Across All FAQ Pages

```javascript
async function searchAllFaqs(query) {
  const response = await fetch('/api/v1/pages');
  const pages = await response.json();
  
  const results = [];
  
  pages.data.forEach(page => {
    page.content
      .filter(section => section.type === 'faq')
      .forEach(section => {
        section.data.items.forEach(item => {
          if (
            item.question.toLowerCase().includes(query.toLowerCase()) ||
            item.answer.toLowerCase().includes(query.toLowerCase())
          ) {
            results.push({
              page: page.title,
              slug: page.slug,
              question: item.question,
              answer: item.answer
            });
          }
        });
      });
  });
  
  return results;
}
```

## Troubleshooting

### FAQ Not Showing in API Response

**Check:**
- Page status is "published"
- FAQ section has at least 1 item
- Both question and answer are filled

### Rich Text Not Rendering

**Solution:**
```javascript
// Use dangerouslySetInnerHTML in React
<div dangerouslySetInnerHTML={{ __html: item.answer }} />

// Or v-html in Vue
<div v-html="item.answer" />
```

### Items Not Collapsing/Expanding

**Check:**
- Click event is bound to the question button
- State management is working correctly
- CSS transitions are not conflicting

## Summary

The FAQ section provides a flexible, user-friendly way to add Q&A content to your pages:

- 📝 **Easy to manage** - Intuitive admin interface
- 🎨 **Customizable** - Full control over styling
- 🔧 **Flexible** - Combine with other content blocks
- 📱 **Responsive** - Works on all devices
- ✅ **Well-tested** - Comprehensive test coverage

For more information, see:
- Main documentation: `README.md`
- Test examples: `tests/Feature/FaqSectionTest.php`

---

**Ready to use! Add FAQ sections to your pages now.** 🚀

