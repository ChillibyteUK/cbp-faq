# FAQ Blocks

A reusable WordPress plugin that registers a custom FAQ post type with taxonomy, ACF fields, and a dynamic Gutenberg block with FAQ Schema support.

## Description

FAQ Blocks provides a complete solution for managing and displaying FAQs on your WordPress site. It includes:

- Custom post type for FAQs
- Taxonomy for organizing FAQs into categories
- ACF fields for structured FAQ content
- Dynamic Gutenberg block for displaying FAQs
- Built-in FAQ Schema markup for SEO

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

Use the FAQ Blocks Gutenberg block to display your FAQs:

1. In the block editor, click the **+** button
2. Search for **FAQ** or **FAQ Blocks**
3. Add the block to your page
4. Configure the block settings to filter and display FAQs

## Features

- **Custom Post Type**: Dedicated FAQ post type for easy management
- **Taxonomy Support**: Organize FAQs into categories
- **ACF Integration**: Structured question/answer fields
- **Gutenberg Block**: Dynamic block for flexible display options
- **FAQ Schema**: Automatic JSON-LD schema markup for SEO
- **Responsive**: Mobile-friendly design

## Development

### File Structure

```
cbp-faq/
├── cbp-faq.php          # Main plugin file
├── includes/            # Additional plugin files
└── README.md           # This file
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
- Custom FAQ post type
- Taxonomy support
- ACF field integration
- Gutenberg block
- FAQ Schema markup
