# Splice Test Theme

A WordPress theme with project filtering capabilities and date-based filtering functionality.

## Features

- Custom post type for Projects
- Date-based filtering for projects
- Responsive project grid layout
- Project details display
- Debug mode for troubleshooting

## Installation

1. Clone this repository into your WordPress themes directory:
```bash
cd wp-content/themes/
git clone [repository-url] splice-test-theme
```

2. Activate the theme in WordPress admin:
   - Go to Appearance > Themes
   - Find "Splice Test Theme"
   - Click "Activate"

3. Set up required plugins:
   - Debug Bar (recommended for development)

## Project Post Type

The theme includes a custom "Project" post type with the following fields:
- Title
- Content
- Featured Image
- Project Start Date
- Project End Date
- Client Name
- Project URL

### Adding a New Project

1. In WordPress admin, go to Projects > Add New
2. Fill in the project details:
   - Title: Project name
   - Content: Project description
   - Featured Image: Project thumbnail
   - Custom Fields:
     - project_start_date (YYYY-MM-DD format)
     - project_end_date (YYYY-MM-DD format)
     - client_name
     - project_url

## Date Filtering

The projects archive page (`/projects/`) includes date filtering functionality:

1. Start Date: Filter projects ending on or after this date
2. End Date: Filter projects starting on or before this date
3. Both Dates: Filter projects that overlap with the date range

### Debug Mode

Add `?debug=1` to the URL to enable debug output showing:
- Date normalization results
- Meta query structure
- Project data

Example: `http://your-site.com/projects/?debug=1`

## Theme Structure

```
splice-test-theme/
├── README.md
├── style.css
├── functions.php
├── header.php
├── footer.php
├── archive-project.php
├── single-project.php
└── js/
    ├── navigation.js
    └── project-filters.js
```


### Date Validation

The theme includes robust date validation:
- Validates YYYY-MM-DD format
- Ensures dates are valid (e.g., no February 31st)
- Handles various date input formats
- Provides detailed debug output for troubleshooting

## Troubleshooting

1. Date filtering not working:
   - Ensure dates are in YYYY-MM-DD format
   - Check debug output with `?debug=1`
   - Verify project dates in WordPress admin

2. Projects not displaying:
   - Check if projects exist in the database
   - Verify project dates are valid
   - Check project status (must be "Published")

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

MIT License