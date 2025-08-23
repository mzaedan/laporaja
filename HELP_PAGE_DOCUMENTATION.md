# Help and Support Page

## Overview
The Help and Support page provides users with comprehensive guidance on how to effectively use the LapoRaja reporting system. It categorizes reports by priority levels and provides examples to help users understand which category their issue belongs to.

## Features

### 1. Priority-Based Categorization
The help page organizes reports into three priority levels:

- **High Priority (Red)**: Safety and security issues requiring immediate attention
- **Medium Priority (Yellow)**: Infrastructure and public facility issues needing timely resolution
- **Low Priority (Blue)**: Administrative and routine service issues

### 2. Detailed Examples
Each priority category includes specific, real-world examples to help users classify their reports correctly:

- **High Priority Examples**: Fallen trees blocking roads, damaged bridges, fires, electrical hazards, water pipe breaks
- **Medium Priority Examples**: Potholed roads, clogged drains, accumulated garbage, broken streetlights, leaking roofs
- **Low Priority Examples**: Delayed administrative services, data entry errors, security patrol scheduling, cleanliness issues, damaged information boards

### 3. Practical Tips
The page includes actionable tips for creating effective reports:
- Choose appropriate priority categories
- Include photos for clarity
- Provide specific location details
- Write clear descriptions
- Contact authorities for high-priority issues

### 4. Emergency Contacts
Quick access to important emergency phone numbers:
- Police: 110
- Fire Department: 113
- Ambulance: 118
- Search and Rescue: 115

### 5. Action Buttons
Direct links to:
- Create new report
- View user's existing reports

## Technical Implementation

### Files Created/Modified:
1. **Controller**: `app/Http/Controllers/HelpController.php`
2. **View**: `resources/views/pages/help.blade.php`
3. **Routes**: Added help route in `routes/web.php`
4. **Navigation**: Updated `resources/views/includes/nav-mobile.blade.php`
5. **Profile**: Updated `resources/views/pages/app/profile.blade.php`
6. **Tests**: `tests/Feature/HelpPageTest.php`

### Route Information:
- **URL**: `/help`
- **Route Name**: `help`
- **Method**: GET
- **Controller**: `HelpController@index`

### Navigation Integration:
- Added to mobile bottom navigation with help icon
- Accessible from user profile page
- Uses consistent styling with the rest of the application

### Testing:
Comprehensive feature tests ensure:
- Page loads successfully
- All priority categories are displayed
- Specific examples are shown
- Emergency contacts are available
- Proper view rendering

## Usage

Users can access the help page through:
1. Mobile navigation menu (help icon)
2. Profile page → "Bantuan dan dukungan" link
3. Direct URL: `/help`

The page is designed to be mobile-friendly and follows the application's existing design patterns for consistency.