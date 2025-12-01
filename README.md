# FAQ Blocks

A reusable WordPress plugin that registers a custom FAQ post type with taxonomy, ACF fields, and a dynamic Gutenberg block with FAQ Schema support.

## Description

FAQ Blocks provides a complete solution for managing and displaying FAQs on your WordPress site. It includes:

- Custom post type for FAQs
- Taxonomy for organizing FAQs into categories
- ACF fields for structured FAQ content
- Two dynamic Gutenberg blocks for displaying FAQs (List and Tabs)
- Built-in FAQ Schema markup for SEO
- Search functionality within FAQ tabs
- Custom CSS class support for styling flexibility

## Installation

1. Download or clone this repository into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins
   git clone https://github.com/ChillibyteUK/cbp-faq.git
   ```

2. Activate the plugin through the WordPress admin panel under 'Plugins'

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Advanced Custom Fields (ACF) plugin

## Usage

### Creating FAQs

1. Navigate to **FAQs** in the WordPress admin menu
2. Click **Add New FAQ**
3. Enter your question and answer using the ACF fields
4. Assign categories as needed
5. Publish the FAQ

### Displaying FAQs

The plugin provides two Gutenberg blocks for displaying FAQs:

#### FAQ List Block

1. In the block editor, click the **+** button
2. Search for **CBP FAQ List**
3. Add the block to your page
4. Select a FAQ category to display
5. Optionally enable "Show Title" to display the category name as a heading
6. Configure block settings (alignment, anchor, custom CSS classes)

#### FAQ Tabs Block

1. In the block editor, click the **+** button
2. Search for **CBP FAQ Tabs**
3. Add the block to your page
4. Select one or more FAQ categories to display as tabs
5. Optionally enable "Include Search" to add a search box for filtering FAQs
6. Configure block settings (alignment, anchor, custom CSS classes)

### Block Settings

Both blocks support:
- **Alignment**: Wide, full width, or default alignment
- **HTML Anchor**: Add a custom anchor for direct linking
- **Additional CSS Classes**: Add custom classes for styling in the Advanced panel
- **Show Title** (FAQ List only): Display category name as H2 heading

## Features

- **Custom Post Type**: Dedicated FAQ post type for easy management
- **Taxonomy Support**: Organize FAQs into categories
- **ACF Integration**: Structured question/answer fields with excerpt support
- **Two Gutenberg Blocks**: 
  - FAQ List: Display FAQs from a single category
  - FAQ Tabs: Display multiple categories in a tabbed interface
- **Search Functionality**: Optional search box for filtering FAQs within tabs
- **Category Title Display**: Optional H2 heading showing category name
- **FAQ Schema**: Automatic JSON-LD schema markup for SEO
- **Custom Styling**: Support for additional CSS classes via Gutenberg
- **Block Alignment**: Full support for wide and full-width alignments
- **Responsive**: Mobile-friendly design
- **JavaScript Interactivity**: Tab switching and live search filtering

## Development

### File Structure

```
cbp-faq/
├── cbp-faq.php              # Main plugin file
├── includes/
│   ├── block-render.php     # Block registration and render callbacks
│   ├── cpt-taxonomy.php     # Custom post type and taxonomy registration
│   └── options-page.php     # ACF options page configuration
└── README.md                # This file
```

### Hooks & Filters

The plugin provides various hooks and filters for customization. Refer to the inline documentation in the source code for details.

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/ChillibyteUK/cbp-faq).

## License

This plugin is licensed under the GPL v2 or later.

## Author

**ChillibyteUK - DS**  
[GitHub](https://github.com/ChillibyteUK)

## Changelog

### 1.0.0
- Initial release
- Custom FAQ post type with category taxonomy
- ACF field integration for questions and answers
- Two Gutenberg blocks (FAQ List and FAQ Tabs)
- FAQ Schema markup (JSON-LD)
- Search functionality for FAQ Tabs block
- Optional category title display for FAQ List block
- Support for custom CSS classes via Gutenberg Advanced panel
- Block alignment support (wide, full-width)
- Responsive design with inline styles
- JavaScript-powered tab switching and search filtering
